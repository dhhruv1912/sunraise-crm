<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Document;
use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    /* =============================
     * LIST PAGE
     * ============================= */
    public function index()
    {
        return view('page.projects.index');
    }

    /* =============================
     * LIST (AJAX)
     * ============================= */
    public function ajaxList(Request $request)
    {
        $page = (int) $request->get('page', 1);
        $perPage = (int) $request->get('per_page', 10);

        $q = Project::with(['customer', 'assigneeUser'])
            ->latest();

        /* ---------- FILTERS ---------- */

        if ($request->status) {
            $q->where('status', $request->status);
        }

        if ($request->priority) {
            $q->where('priority', $request->priority);
        }

        if ($request->search) {
            $s = $request->search;
            $q->where(function ($qq) use ($s) {
                $qq->where('project_code', 'like', "%{$s}%")
                    ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$s}%")
                    );
            });
        }

        /* ---------- PAGINATION ---------- */

        $total = (clone $q)->count();

        $rows = $q
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get()
            ->map(function ($p) {

                $today = Carbon::today();

                /* ===== LAST ACTIVITY ===== */
                $lastActivityAt = $p->history()
                    ->latest()
                    ->value('created_at');

                $idleDays = $lastActivityAt
                    ? Carbon::parse($lastActivityAt)->diffInDays($today)
                    : null;

                /* ===== SLA DELAY ===== */
                $isDelayed = $p->estimated_complete_date &&
                    $p->estimated_complete_date < $today &&
                    ! $p->actual_complete_date;

                /* ===== DOC BLOCK ===== */
                $docBlocked = empty($p->design_file) || empty($p->boq_file);

                /* ===== NEXT EMI ===== */
                $nextEmi = null;
                if ($p->emi) {
                    foreach ($p->emi as $date => $amt) {
                        if ($amt > 0) {
                            $nextEmi = $date;
                            break;
                        }
                    }
                }

                /* ===== NEXT ACTION LABEL ===== */
                $nextAction = match (true) {
                    $p->is_on_hold => 'On Hold',
                    $docBlocked => 'Upload Docs',
                    $isDelayed => 'Execution Delayed',
                    $nextEmi => 'EMI Due',
                    default => ucfirst(str_replace('_', ' ', $p->status))
                };

                return [
                    'id' => $p->id,
                    'project_code' => $p->project_code,
                    'customer' => $p->customer?->name,
                    'status' => $p->status,
                    'priority' => $p->priority,
                    'billing' => $p->billing_status,

                    /* intelligence */
                    'idle_days' => $idleDays,
                    'is_delayed' => $isDelayed,
                    'doc_blocked' => $docBlocked,
                    'next_action' => $nextAction,
                    'next_emi' => $nextEmi,
                    'on_hold' => (bool) $p->is_on_hold,
                ];
            });

        return response()->json([
            'data' => $rows,
            'meta' => [
                'current_page' => $page,
                'last_page' => (int) ceil($total / $perPage),
                'per_page' => $perPage,
                'total' => $total,
            ],
        ]);
    }

    /* =============================
     * WIDGETS
     * ============================= */
    public function ajaxWidgets()
    {
        $today = Carbon::today();

        $projects = Project::with(['invoices'])->get();
        $totalProjects = $projects->count();

        /* ================= EXECUTION PROGRESS ================= */

        $statusWeights = [
            'new' => 5,
            'document_collection' => 10,
            'document_verification' => 20,
            'site_visit' => 30,
            'installation_started' => 50,
            'project_execution' => 70,
            'project_completion' => 85,
            'complete' => 100,
        ];

        $executionSum = $projects->sum(function ($p) use ($statusWeights) {
            return $statusWeights[$p->status] ?? 0;
        });

        $executionPercent = $totalProjects > 0
            ? round($executionSum / $totalProjects)
            : 0;

        /* ================= ALERT COUNTS ================= */

        $onHold = $projects->where('is_on_hold', 1)->count();

        $delayed = $projects->filter(function ($p) use ($today) {
            return $p->estimated_complete_date &&
                   $p->estimated_complete_date < $today &&
                   ! $p->actual_complete_date;
        })->count();

        $overdueEmi = 0;

        foreach ($projects as $p) {
            if (! $p->emi || ! $p->invoices) {
                continue;
            }

            $paidEmis = $p->invoices->payments()
                ->pluck('meta')
                ->filter()
                ->pluck('emi_date')
                ->filter()
                ->toArray();

            foreach ($p->emi as $date => $amount) {
                if ($date < $today->toDateString() && $amount > 0 && ! in_array($date, $paidEmis)) {
                    $overdueEmi++;
                    break;
                }
            }
        }

        $blockedByDocs = $projects->filter(function ($p) {
            return empty($p->design_file) || empty($p->boq_file);
        })->count();

        /* ================= UPCOMING TIMELINE ================= */

        $upcomingList = $projects
            ->filter(fn ($p) => $p->estimated_complete_date &&
                $p->estimated_complete_date->between($today, $today->copy()->addDays(7))
            )
            ->map(fn ($p) => [
                'label' => $p->project_code,
                'date' => $p->estimated_complete_date->format('d M'),
            ])
            ->take(5)
            ->values();

        /* ================= STATUS DISTRIBUTION ================= */

        $newPct = $totalProjects > 0
            ? round($projects->where('status', 'new')->count() / $totalProjects * 100)
            : 0;

        $installPct = $totalProjects > 0
            ? round($projects->whereIn('status', [
                'installation_started',
                'project_execution',
            ])->count() / $totalProjects * 100)
            : 0;

        $completePct = $totalProjects > 0
            ? round($projects->where('status', 'complete')->count() / $totalProjects * 100)
            : 0;

        return view('page.projects.widgets', compact(
            'executionPercent',
            'onHold',
            'delayed',
            'overdueEmi',
            'blockedByDocs',
            'upcomingList',
            'newPct',
            'installPct',
            'completePct'
        ));
    }

    public function view(Project $project)
    {
        $project->load('customer:id,name');

        return view('page.projects.view', compact('project'));
    }

    public function ajaxWid(Project $project)
    {
        $today = Carbon::today();
        $statusWeight = [
            'new' => 5,
            'document_collection' => 10,
            'document_verification' => 20,
            'site_visit' => 30,
            'installation_started' => 50,
            'project_execution' => 70,
            'project_completion' => 85,
            'complete' => 100,
        ];
        $executionPercent = $statusWeight[$project->status] ?? 0;

        $invoice = $project->invoices;
        $total = $project->finalize_price ?? 0;
        $paid = $invoice?->paid_amount ?? 0;
        $balance = max($total - $paid, 0);

        $paidPercent = $total > 0 ? round(($paid / $total) * 100) : 0;
        $balancePercent = 100 - $paidPercent;

        $paidEmis = $invoice
            ? $invoice->payments()
                ->pluck('meta')
                ->filter()
                ->pluck('emi_date')
                ->filter()
                ->toArray()
            : [];

        $emiTimeline = [];

        foreach ($project->emi ?? [] as $date => $amount) {
            if ($amount == 0) {
                continue;
            }

            $state = 'upcoming';
            if (in_array($date, $paidEmis)) {
                $state = 'paid';
            } elseif ($date < $today->toDateString()) {
                $state = 'overdue';
            }

            $emiTimeline[] = [
                'date' => Carbon::parse($date)->format('d M Y'),
                'amount' => $amount,
                'state' => $state,
            ];
        }
        $isDelayed = $project->estimated_complete_date &&
            $project->estimated_complete_date < $today &&
            ! $project->actual_complete_date;

        $hasOverdueEmi = collect($emiTimeline)
            ->contains(fn ($e) => $e['state'] === 'overdue');

        return view('page.projects.partials.widget', compact(
            'executionPercent',
            'paidPercent',
            'balancePercent',
            'paid',
            'balance',
            'isDelayed',
            'hasOverdueEmi',
        ));
    }

    public function ajaxStatus(Project $project)
    {
        $stepper = $this->buildStatusStepper($project);

        // $stepper = buildStatusStepper($project);
        return view('page.projects.partials.status_stepper', compact('stepper'));
    }

    public function ajaxTimeline(Project $project)
    {
        $today = Carbon::today();
        $milestones = collect($project->meta['milestones'] ?? [])
            ->map(function ($m) use ($today) {

                $state = 'upcoming';

                if ($m['completed']) {
                    $state = 'done';
                } elseif ($m['planned_date'] && Carbon::parse($m['planned_date'])->lt($today)) {
                    $state = 'overdue';
                }

                return [
                    ...$m,
                    'state' => $state,
                    'slip' => $m['planned_date'] && $m['actual_date']
                        ? Carbon::parse($m['planned_date'])
                            ->diffInDays(Carbon::parse($m['actual_date']), false)
                        : null,
                ];
            })
            ->values();

        return view('page.projects.partials.milestones', compact('milestones'));
    }

    public function ajaxBilling(Project $project)
    {
        $stepper = $this->buildStatusStepper($project);

        return view('page.projects.partials.status_stepper', compact('stepper'));
    }

    public function ajaxEmi(Project $project)
    {
        $today = Carbon::today();
        $emiActions = [];
        $paidEmis = $project->invoices
            ? $project->invoices->payments()
                ->pluck('meta')
                ->filter()
                ->pluck('emi_date')
                ->filter()
                ->toArray()
            : [];
        foreach ($project->emi ?? [] as $date => $amount) {
            if ((float) $amount === 0.0) {
                continue;
            }

            $state = 'upcoming';
            if (in_array($date, $paidEmis)) {
                $state = 'paid';
            } elseif ($date < $today->toDateString()) {
                $state = 'overdue';
            }

            $emiActions[] = [
                'date' => $date,
                'amount' => abs((float) $amount),
                'state' => $state,
            ];
        }

        // dd($emiActions);
        return view('page.projects.partials.emi_actions', compact('emiActions'));
    }

    public function ajaxDocuments(Project $project)
    {
        $requiredDocs = $this->requiredProjectDocs();

        $documents = Document::where(function ($q) use ($project) {
            $q->where('entity_type', Project::class)
                ->where('entity_id', $project->id);
        })
            ->orWhere(function ($q) use ($project) {
                $q->where('entity_type', Customer::class)
                    ->where('entity_id', $project->customer_id);
            })
            ->get()
            ->groupBy('type');

        $docChecklist = collect($requiredDocs)->map(function ($cfg, $key) use ($documents) {
            $doc = $documents->get($key);

            return [
                'key' => $key,
                'label' => $cfg['label'],
                'entity' => $cfg['entity'],
                'multiple' => $cfg['multiple'],
                'uploaded' => (bool) $doc,
                'doc' => $doc,
            ];
        })->values();

        return view('page.projects.partials.documents', compact(
            'project',
            'docChecklist'
        ));
    }

    public function ajaxActivities(Project $project)
    {
        $activities = $this->buildActivityFeed($project);

        return view('page.projects.partials.activity', compact('activities'));
    }

    public function ajaxDashboard(Project $project)
    {
        $today = Carbon::today();

        /* ================= EXECUTION PROGRESS ================= */

        $statusWeight = [
            'new' => 5,
            'document_collection' => 10,
            'document_verification' => 20,
            'site_visit' => 30,
            'installation_started' => 50,
            'project_execution' => 70,
            'project_completion' => 85,
            'complete' => 100,
        ];

        $executionPercent = $statusWeight[$project->status] ?? 0;

        /* ================= FINANCIAL ================= */

        $invoice = $project->invoices;
        $total = $project->finalize_price ?? 0;
        $paid = $invoice?->paid_amount ?? 0;
        $balance = max($total - $paid, 0);

        $paidPercent = $total > 0 ? round(($paid / $total) * 100) : 0;
        $balancePercent = 100 - $paidPercent;

        /* ================= EMI TIMELINE ================= */

        $paidEmis = $invoice
            ? $invoice->payments()
                ->pluck('meta')
                ->filter()
                ->pluck('emi_date')
                ->filter()
                ->toArray()
            : [];

        $emiTimeline = [];

        foreach ($project->emi ?? [] as $date => $amount) {
            if ($amount == 0) {
                continue;
            }

            $state = 'upcoming';
            if (in_array($date, $paidEmis)) {
                $state = 'paid';
            } elseif ($date < $today->toDateString()) {
                $state = 'overdue';
            }

            $emiTimeline[] = [
                'date' => Carbon::parse($date)->format('d M Y'),
                'amount' => $amount,
                'state' => $state,
            ];
        }

        /* ================= RISK FLAGS ================= */

        $isDelayed = $project->estimated_complete_date &&
            $project->estimated_complete_date < $today &&
            ! $project->actual_complete_date;

        $hasOverdueEmi = collect($emiTimeline)
            ->contains(fn ($e) => $e['state'] === 'overdue');

        /* ================= ACTIVITY ================= */

        $stepper = $this->buildStatusStepper($project);
        $milestones = collect($project->meta['milestones'] ?? [])
            ->map(function ($m) use ($today) {

                $state = 'upcoming';

                if ($m['completed']) {
                    $state = 'done';
                } elseif ($m['planned_date'] && Carbon::parse($m['planned_date'])->lt($today)) {
                    $state = 'overdue';
                }

                return [
                    ...$m,
                    'state' => $state,
                    'slip' => $m['planned_date'] && $m['actual_date']
                        ? Carbon::parse($m['planned_date'])
                            ->diffInDays(Carbon::parse($m['actual_date']), false)
                        : null,
                ];
            })
            ->values();

        $requiredDocs = $this->requiredProjectDocs();

        $documents = Document::where(function ($q) use ($project) {
            $q->where('entity_type', Project::class)
                ->where('entity_id', $project->id);
        })
            ->orWhere(function ($q) use ($project) {
                $q->where('entity_type', Customer::class)
                    ->where('entity_id', $project->customer_id);
            })
            ->get()
            ->groupBy('type');

        $docChecklist = collect($requiredDocs)->map(function ($cfg, $key) use ($documents) {
            $doc = $documents->get($key);

            return [
                'key' => $key,
                'label' => $cfg['label'],
                'entity' => $cfg['entity'],
                'multiple' => $cfg['multiple'],
                'uploaded' => (bool) $doc,
                'doc' => $doc,
            ];
        })->values();

        $emiActions = [];
        $paidEmis = $invoice
            ? $invoice->payments()
                ->pluck('meta')
                ->filter()
                ->pluck('emi_date')
                ->filter()
                ->toArray()
            : [];

        foreach ($project->emi ?? [] as $date => $amount) {
            if ((float) $amount === 0.0) {
                continue;
            }

            $state = 'upcoming';
            if (in_array($date, $paidEmis)) {
                $state = 'paid';
            } elseif ($date < $today->toDateString()) {
                $state = 'overdue';
            }

            $emiActions[] = [
                'date' => $date,
                'amount' => abs((float) $amount),
                'state' => $state,
            ];
        }
        $activities = $this->buildActivityFeed($project);

        return view('page.projects.dashboard', compact(
            'project',
            'executionPercent',
            'paid',
            'balance',
            'paidPercent',
            'balancePercent',
            'emiTimeline',
            'docChecklist',
            'isDelayed',
            'hasOverdueEmi',
            'activities',
            'stepper',
            'milestones',
            'emiActions'
        ));
    }

    private function buildStatusStepper(Project $project): array
    {
        $flow = array_keys(Project::STATUS_LABELS);
        $currentIndex = array_search($project->status, $flow);

        return collect($flow)->map(function ($status, $index) use ($currentIndex, $project) {

            $state = 'upcoming';
            if ($index < $currentIndex) {
                $state = 'done';
            }
            if ($index === $currentIndex) {
                $state = 'current';
            }

            $history = $project->history
                ->where('status', $status)
                ->sortByDesc('created_at')
                ->first();

            return [
                'key' => $status,
                'label' => ucwords(str_replace('_', ' ', $status)),
                'state' => $state,
                'changed_at' => $history?->created_at?->format('d M Y'),
            ];
        })->toArray();
    }

    public function updateStatus(Project $project)
    {
        $flow = array_keys(Project::STATUS_LABELS);
        $currentIndex = array_search($project->status, $flow);

        abort_if($currentIndex === false, 400);

        $nextStatus = $flow[$currentIndex + 1] ?? null;
        abort_if(! $nextStatus, 400);

        $old = $project->status;
        $project->status = $nextStatus;
        $project->save();

        $project->history()->create([
            'status' => $nextStatus,
            'changed_by' => Auth::id(),
            'notes' => "Moved from {$old} to {$nextStatus}",
        ]);

        return response()->json([
            'message' => 'Status updated',
            'status' => $nextStatus,
        ]);
    }

    private function defaultMilestones(): array
    {
        return [
            ['key' => 'survey', 'title' => 'Site Survey'],
            ['key' => 'design', 'title' => 'Design Finalization'],
            ['key' => 'boq', 'title' => 'BOQ Approval'],
            ['key' => 'dispatch', 'title' => 'Material Dispatch'],
            ['key' => 'installation', 'title' => 'Installation'],
            ['key' => 'inspection', 'title' => 'Inspection'],
            ['key' => 'handover', 'title' => 'Handover'],
        ];
    }

    public function initMilestones(Project $project)
    {
        if (! empty($project->meta['milestones'])) {
            return;
        }

        $milestones = collect($this->defaultMilestones())->map(fn ($m) => [
            ...$m,
            'planned_date' => null,
            'actual_date' => null,
            'completed' => false,
        ])->toArray();

        $project->meta = array_merge($project->meta ?? [], [
            'milestones' => $milestones,
        ]);

        $project->save();
    }

    public function completeMilestone(Project $project, string $key)
    {
        $meta = $project->meta ?? [];
        $milestones = collect($meta['milestones'] ?? []);

        $milestones = $milestones->map(function ($m) use ($key) {
            if ($m['key'] === $key && ! $m['completed']) {
                $m['completed'] = true;
                $m['actual_date'] = now()->toDateString();
            }

            return $m;
        })->toArray();

        $meta['milestones'] = $milestones;
        $project->meta = $meta;
        $project->save();

        $project->history()->create([
            'action' => 'milestone_completed',
            'message' => "Milestone '{$key}' completed",
            'changed_by' => Auth::id(),
        ]);

        return response()->json(['message' => 'Milestone completed']);
    }

    private function requiredProjectDocs(): array
    {
        return [
            'design_file' => [
                'label' => 'Design File',
                'multiple' => false,
                'entity' => Project::class,
            ],
            'boq_file' => [
                'label' => 'BOQ File',
                'multiple' => false,
                'entity' => Project::class,
            ],
            'site_photos' => [
                'label' => 'Site Photos',
                'multiple' => true,
                'entity' => Project::class,
            ],
            'light_bill' => [
                'label' => 'Light Bill',
                'multiple' => false,
                'entity' => Customer::class,
            ],
            'aadhar_card' => [
                'label' => 'Aadhar Card',
                'multiple' => false,
                'entity' => Customer::class,
            ],
        ];
    }

    public function payEmi(Project $project, Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'emi_date' => 'required|date',
            'reference' => 'nullable|string',
        ]);

        $invoice = $project->invoices;
        abort_if(! $invoice, 400, 'Invoice not found');

        // prevent double payment
        $alreadyPaid = $invoice->payments()
            ->where('meta->emi_date', $request->emi_date)
            ->exists();

        abort_if($alreadyPaid, 422, 'EMI already paid');

        $payment = $invoice->payments()->create([
            'amount' => $request->amount,
            'method' => 'emi',
            'reference' => $request->reference,
            'paid_at' => now()->toDateString(),
            'received_by' => Auth::id(),
            'meta' => [
                'emi_date' => $request->emi_date,
            ],
        ]);

        // update invoice totals
        $invoice->paid_amount += $request->amount;
        $invoice->balance = max($invoice->total - $invoice->paid_amount, 0);
        $invoice->status = $invoice->balance == 0 ? 'paid' : 'partial';
        $invoice->save();

        // log project activity
        $project->history()->create([
            'action' => 'emi_paid',
            'message' => "EMI paid for {$request->emi_date}",
            'changed_by' => Auth::id(),
        ]);

        return response()->json(['message' => 'EMI payment recorded']);
    }

    private function buildActivityFeed(Project $project)
    {
        $activities = collect();

        /* ================= PROJECT HISTORY ================= */
        foreach ($project->history as $h) {
            // dump($h);
            $activities->push([
                'type' => 'status',
                'icon' => 'fa-diagram-project',
                'title' => $h->notes ?? ucfirst(str_replace('_', ' ', $h->status)),
                'user' => optional($h->changedBy)->fname.' '.optional($h->changedBy)->lname ?? 'System',
                'time' => $h->created_at,
            ]);
        }

        /* ================= DOCUMENT UPLOADS ================= */
        $docs = Document::where(function ($q) use ($project) {
            $q->where('entity_type', Project::class)
                ->where('entity_id', $project->id);
        })->orWhere(function ($q) use ($project) {
            $q->where('entity_type', Customer::class)
                ->where('entity_id', $project->customer_id);
        })->get();

        foreach ($docs as $d) {
            $activities->push([
                'type' => 'document',
                'icon' => 'fa-file-lines',
                'title' => "Uploaded {$d->type}",
                'user' => optional($d->uploader)->fname.' '.optional($d->uploader)->lname ?? 'User',
                'time' => $d->created_at,
            ]);
        }

        /* ================= EMI / PAYMENTS ================= */
        if ($project->invoices) {
            foreach ($project->invoices->payments as $p) {
                $label = $p->meta['emi_date'] ?? null
                    ? "EMI paid ({$p->meta['emi_date']})"
                    : 'Payment received';

                $activities->push([
                    'type' => 'payment',
                    'icon' => 'fa-indian-rupee-sign',
                    'title' => $label,
                    'user' => optional($p->receiver)->fname.' '.optional($p->receiver)->lname ?? 'User',
                    'time' => $p->created_at,
                ]);
            }
        }

        /* ================= SORT ================= */
        return $activities
            ->sortByDesc('time')
            ->take(20)
            ->values();
    }

    public function executionReport()
    {
        return Project::selectRaw('
                status,
                COUNT(*) as total
            ')
            ->groupBy('status')
            ->get();
    }

    public function delayReport()
    {
        return Project::whereNotNull('estimated_complete_date')
            ->whereNull('actual_complete_date')
            ->selectRaw('
            status,
            COUNT(*) as delayed
        ')
            ->whereDate('estimated_complete_date', '<', now())
            ->groupBy('status')
            ->get();
    }

    public function emiCashflow()
    {
        $rows = [];

        Project::whereNotNull('emi')
            ->with('invoices.payments')
            ->get()
            ->each(function ($p) use (&$rows) {

                $paid = $p->invoice?->payments
                    ->pluck('meta.emi_date')
                    ->filter()
                    ->toArray();

                foreach ($p->emi as $date => $amt) {
                    if ($amt <= 0) {
                        continue;
                    }
                    if (in_array($date, $paid)) {
                        continue;
                    }

                    $rows[] = [
                        'date' => $date,
                        'amount' => $amt,
                    ];
                }
            });

        return collect($rows)
            ->groupBy('date')
            ->map(fn ($d) => $d->sum('amount'))
            ->sortKeys();
    }

    public function workloadReport()
    {
        return Project::selectRaw('
                assignee,
                COUNT(*) as total
            ')
            ->where('status', '!=', 'complete')
            ->groupBy('assignee')
            ->get();
    }

    public function edit(Project $project)
    {
        $project->load([
            'quoteRequest',
            'quoteMaster',
            'customer',
            'invoices',
        ]);
        $paid = [];
        $paidEmis = $project->invoices
            ? $project->invoices->payments()
                ->select('meta','paid_at')
                ->get()
                ->filter()
                ->toArray()
            : [];
        foreach ($paidEmis as $i => $data) {
            if (isset($data,$data['meta']['emi_date']) && isset($data['paid_at'])) {
                $paid[$data['meta']['emi_date']] = $data['paid_at'];
            }
        }
        $users = User::where('status', 1)
            ->orderBy('fname')
            ->get(['id', 'fname', 'lname']);

        return view('page.projects.edit', [
            'project' => $project,
            'users' => $users,
            'quoteRequest' => $project->quoteRequest,
            'quoteMaster' => $project->quoteMaster,
            'invoice' => $project->invoices,
            'paidEmiDates' => $paid
        ]);
    }

    public function update(Request $request, Project $project)
    {
        $isHoldRequest = $request->has('is_on_hold');
        if ($isHoldRequest) {
            $rules = [
                'is_on_hold' => 'required|boolean',
                'hold_reason' => 'nullable|string|max:255',
            ];

            $v = Validator::make($request->all(), $rules);

            if ($v->fails()) {
                return response()->json([
                    'message' => $v->errors(),
                ], 422);
            }
            $project->is_on_hold = $request->is_on_hold;

            if ($request->is_on_hold) {
                if (! $request->hold_reason) {
                    return response()->json([
                        'message' => ['hold_reason' => ['Hold reason is required']],
                    ], 422);
                }
                $project->hold_reason = $request->hold_reason;
            } else {
                $project->hold_reason = null;
            }

            $project->save();

            $project->history()->create([
                'action' => $request->is_on_hold ? 'project_hold' : 'project_resume',
                'message' => $request->is_on_hold
                    ? 'Project put on hold'
                    : 'Project resumed',
                'changed_by' => Auth::id(),
            ]);

            return response()->json([
                'message' => 'Project updated',
            ]);
        }

        $rules = [
            'priority' => 'required|in:low,medium,high',
            'assignee' => 'nullable|exists:users,id',
            'reporter' => 'nullable|exists:users,id',

            'survey_date' => 'nullable|date',
            'installation_start_date' => 'nullable|date',
            'installation_end_date' => 'nullable|date',
            'estimated_complete_date' => 'nullable|date',
            'inspection_date' => 'nullable|date',
            'handover_date' => 'nullable|date',

            'finalize_price' => 'nullable|numeric|min:0',
            'subsidy_amount' => 'nullable|numeric|min:0',

            'emi' => 'nullable|string', // JSON
            'milestones' => 'nullable|array',
            'milestones.*.key' => 'required|string',
            'milestones.*.planned_date' => 'nullable|date',
        ];

        $v = Validator::make($request->all(), $rules);

        if ($v->fails()) {
            return response()->json([
                'message' => $v->errors(),
            ], 422);
        }

        /* ================= BASIC FIELDS ================= */

        $project->priority = $request->priority;
        $project->assignee = $request->assignee;
        $project->reporter = $request->reporter;

        /* ================= DATES ================= */

        $project->survey_date = $request->survey_date;
        $project->installation_start_date = $request->installation_start_date;
        $project->installation_end_date = $request->installation_end_date;
        $project->estimated_complete_date = $request->estimated_complete_date;
        $project->inspection_date = $request->inspection_date;
        $project->handover_date = $request->handover_date;

        /* ================= FINANCIAL ================= */

        $project->finalize_price = $request->finalize_price;
        $project->subsidy_amount = $request->subsidy_amount;
        $project->subsidy_status = $request->subsidy_status;

        /* ================= EMI ================= */

        if ($request->emi) {
            $emi = json_decode($request->emi, true);

            if (! is_array($emi)) {
                return response()->json([
                    'message' => ['emi' => ['Invalid EMI format']],
                ], 422);
            }

            ksort($emi); // always store sorted
            $project->emi = $emi;
        }

        /* ================= MILESTONES ================= */

        if ($request->milestones) {
            $meta = $project->meta ?? [];
            $existing = collect($meta['milestones'] ?? []);

            $updated = $existing->map(function ($m) use ($request) {
                $match = collect($request->milestones)
                    ->firstWhere('key', $m['key']);

                if ($match) {
                    $m['planned_date'] = $match['planned_date'] ?? null;
                }

                return $m;
            });

            $meta['milestones'] = $updated->values()->toArray();
            $project->meta = $meta;
        }
        $project->project_note = $request->project_note;

        $project->save();

        /* ================= ACTIVITY LOG ================= */

        $project->history()->create([
            'action' => 'project_updated',
            'message' => 'Project details updated',
            'changed_by' => Auth::id(),
        ]);

        return response()->json([
            'message' => 'Project updated successfully',
        ]);
    }
}
