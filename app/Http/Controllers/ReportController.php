<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Project;
use App\Models\QuoteRequest;
use App\Models\User;
use Carbon\Carbon;

class ReportController extends Controller
{
    /* ================= INDEX ================= */

    public function index()
    {
        return view('page.reports.index');
    }

    /* ================= EXECUTION HEALTH ================= */

    public function execution()
    {
        $total = Project::count();

        if ($total === 0) {
            return view('page.reports.execution', [
                'rows' => [],
            ]);
        }

        $map = [
            'new' => ['label' => 'New', 'color' => '#adb5bd'],
            'document_collection' => ['label' => 'Docs', 'color' => '#868e96'],
            'installation_started' => ['label' => 'Installation', 'color' => '#228be6'],
            'project_execution' => ['label' => 'Execution', 'color' => '#1c7ed6'],
            'complete' => ['label' => 'Completed', 'color' => '#2b8a3e'],
        ];

        $rows = Project::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get()
            ->map(function ($r) use ($total, $map) {

                $cfg = $map[$r->status] ?? [
                    'label' => ucfirst(str_replace('_', ' ', $r->status)),
                    'color' => '#ced4da',
                ];

                return [
                    'label' => $cfg['label'],
                    'percent' => round(($r->total / $total) * 100),
                    'color' => $cfg['color'],
                ];
            });

        return view('page.reports.execution', compact('rows'));
    }

    /* ================= DELAY ANALYSIS ================= */

    public function delays()
    {
        // $rows = Project::whereNotNull('estimated_complete_date')
        //     ->whereNull('actual_complete_date')
        //     ->whereDate('estimated_complete_date', '<', Carbon::today())
        //     ->selectRaw('status, COUNT(*) as delayed')
        //     ->groupBy('status')
        //     ->orderByDesc('delayed')
        //     ->get();
        $today = now()->toDateString();
        $rows = [];
        $row = Project::whereNotNull('estimated_complete_date')
            ->whereNull('actual_complete_date')
            ->whereRaw("DATE(estimated_complete_date) < '$today'")
            ->selectRaw('status')
            ->get();
        foreach ($row as $key => $r) {
            $rows[$r->status] = isset($rows[$r->status]) ? $rows[$r->status] + 1 : 1;
        }
        return view('page.reports.delays', compact('rows'));
    }

    /* ================= CASHFLOW (EMI) ================= */

    public function cashflow()
    {
        $today = Carbon::today()->toDateString();
        $rows = [];

        Project::whereNotNull('emi')
            ->with('invoices.payments')
            ->get()
            ->each(function ($project) use (&$rows) {

                $paidEmis = $project->invoice
                    ? $project->invoice->payments
                        ->pluck('meta.emi_date')
                        ->filter()
                        ->toArray()
                    : [];

                foreach ($project->emi as $date => $amount) {
                    if ((float) $amount <= 0) {
                        continue;
                    }
                    if (in_array($date, $paidEmis)) {
                        continue;
                    }

                    // bucket by month
                    $month = Carbon::parse($date)->format('Y-m');

                    $rows[$month] = ($rows[$month] ?? 0) + $amount;
                }
            });

        ksort($rows);

        return view('page.reports.cashflow', [
            'rows' => $rows,
        ]);
    }

    /* ================= WORKLOAD ================= */

    public function workload()
    {
        $users = [];

        /* ================= PROJECTS ================= */
        $pr = Project::where('status', '!=', 'complete')
            ->selectRaw('assignee as user_id, COUNT(*) as total')
            ->groupBy('assignee')
            ->get()
            ->each(function ($r) use (&$users) {
                $users[$r->user_id]['projects'] = ($users[$r->user_id]['projects'] ?? 0) + $r->total;
            });

        /* ================= LEADS ================= */
        Lead::whereNotIn('status', ['converted', 'dropped'])
            ->selectRaw('assigned_to as user_id, COUNT(*) as total')
            ->groupBy('assigned_to')
            ->get()
            ->each(function ($r) use (&$users) {
                $users[$r->user_id]['leads'] =
                    ($users[$r->user_id]['leads'] ?? 0) + $r->total;
            });

        /* ================= QUOTE REQUESTS ================= */
        QuoteRequest::whereNotIn('status', [
            'called_closed',
            'called_converted_to_lead',
        ])
            ->selectRaw('assigned_to as user_id, COUNT(*) as total')
            ->groupBy('assigned_to')
            ->get()
            ->each(function ($r) use (&$users) {
                $users[$r->user_id]['quotes'] =
                    ($users[$r->user_id]['quotes'] ?? 0) + $r->total;
            });

        /* ================= NORMALIZE ================= */
        $rows = collect($users)->map(function ($data, $userId) {

            $user = $userId
                ? User::find($userId)
                : null;

            $projects = $data['projects'] ?? 0;
            $leads = $data['leads'] ?? 0;
            $quotes = $data['quotes'] ?? 0;

            return (object) [
                'assignee_name' => $user
                    ? $user->fname.' '.$user->lname
                    : 'Unassigned',

                'projects' => $projects,
                'leads' => $leads,
                'quotes' => $quotes,

                'total' => $projects + $leads + $quotes,
            ];
        })
            ->sortByDesc('total')
            ->values();

        return view('page.reports.workload', compact('rows'));
    }
}
