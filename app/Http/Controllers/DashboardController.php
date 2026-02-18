<?php

namespace App\Http\Controllers;

use App\Models\CustomerActivity;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Settings;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\TellyController;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /* =========================
     |  TOP KPIs
     ========================= */
    public function ajaxTop()
    {
        return response()->json([
            'projects' => Project::count(),
            'active' => Project::where('status', '!=', 'complete')->count(),
            'invoiced' => Invoice::sum('total'),
            'due' => Invoice::sum('balance'),
        ]);
    }

    /* =========================
     |  INVOICE MONTHLY TREND
     ========================= */
    public function ajaxInvoiceTrend()
    {
        $rows = Invoice::selectRaw(
            "DATE_FORMAT(invoice_date,'%Y-%m') as month,
             SUM(total) as total"
        )
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        return response()->json($rows);
    }

    /* =========================
     |  EMI SUMMARY
     ========================= */
    public function ajaxEmiSummary()
    {
        $today = Carbon::today();

        $paid = 0;
        $upcoming = 0;
        $overdue = 0;

        Project::whereNotNull('emi')
            ->with('invoices.payments')
            ->get()
            ->each(function ($p) use (&$paid, &$upcoming, &$overdue, $today) {

                $paidDates = $p->invoice
                    ? $p->invoice->payments
                        ->pluck('meta.emi_date')
                        ->filter()
                        ->toArray()
                    : [];

                foreach ($p->emi as $date => $amount) {
                    if (! $amount) {
                        continue;
                    }

                    if (in_array($date, $paidDates)) {
                        $paid += abs($amount);
                    } elseif ($date < $today->toDateString()) {
                        $overdue += abs($amount);
                    } else {
                        $upcoming += abs($amount);
                    }
                }
            });

        return response()->json(compact('paid', 'upcoming', 'overdue'));
    }

    /* =========================
     |  PROJECT HEALTH
     ========================= */
    public function ajaxProjectHealth()
    {
        return Project::where('status', '!=', 'complete')
            ->latest()
            ->take(8)
            ->get()
            ->map(function ($p) {
                return [
                    'code' => $p->project_code,
                    'status' => $p->status,
                    'progress' => $this->projectProgress($p->status),
                    'color' => $this->statusColor($p->status),
                ];
            });
    }

    private function projectProgress($status)
    {
        $steps = array_values(Project::STATUS_LABELS);
        $index = array_search($status, $steps);

        return $index !== false
            ? round(($index + 1) / count($steps) * 100)
            : 0;
    }

    private function statusColor($status)
    {
        return match ($status) {
            'new', 'document_collection' => 'secondary',
            'installation_started',
            'project_execution' => 'primary',
            'inspection', 'handover' => 'warning',
            'complete' => 'success',
            default => 'info'
        };
    }

    /* =========================
     |  OVERDUE ITEMS
     ========================= */
    public function ajaxOverdue()
    {
        $today = Carbon::today()->toDateString();
        $rows = [];

        Project::whereNotNull('emi')
            ->with('customer')
            ->get()
            ->each(function ($p) use (&$rows, $today) {
                foreach ($p->emi as $date => $amount) {
                    if ($date < $today && $amount) {
                        $rows[] = [
                            'title' => $p->project_code,
                            'sub' => $p->customer?->name,
                            'amount' => abs($amount),
                            'color' => 'danger',
                        ];
                    }
                }
            });

        return response()->json($rows);
    }

    /* =========================
     |  UPCOMING ITEMS
     ========================= */
    public function ajaxUpcoming()
    {
        $today = Carbon::today()->toDateString();
        $limit = Carbon::today()->addDays(7)->toDateString();
        $rows = [];

        Project::whereNotNull('emi')
            ->with('customer')
            ->get()
            ->each(function ($p) use (&$rows, $today, $limit) {
                foreach ($p->emi as $date => $amount) {
                    if ($date >= $today && $date <= $limit && $amount) {
                        $rows[] = [
                            'title' => $p->project_code,
                            'sub' => $p->customer?->name,
                            'amount' => abs($amount),
                            'color' => 'info',
                        ];
                    }
                }
            });

        return response()->json($rows);
    }

    /* =========================
     |  TEAM WORKLOAD
     ========================= */
    public function ajaxWorkload()
    {
        return Project::where('status', '!=', 'complete')
            ->selectRaw('assignee, COUNT(*) as total')
            ->groupBy('assignee')
            ->with('assigneeUser:id,fname,lname')
            ->get()
            ->map(fn ($r) => [
                'name' => $r->assigneeUser
                    ? $r->assigneeUser->fname.' '.$r->assigneeUser->lname
                    : 'Unassigned',
                'total' => $r->total,
            ]);
    }

    /* =========================
     |  ACTIVITY TIMELINE
     ========================= */
    public function ajaxActivity()
    {
        return CustomerActivity::latest()
            ->take(10)
            ->get()
            ->map(fn ($a) => [
                'type' => $a->action,
                'text' => $a->message,
                'time' => $a->created_at->diffForHumans(),
                'color' => 'secondary',
            ]);
    }

    /* =========================
     |  SMART INSIGHTS
     ========================= */
    public function ajaxInsights()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $insights = [];

        $overdue = Project::whereNotNull('emi')
            ->get()
            ->flatMap(fn ($p) => collect($p->emi ?? [])
                ->filter(fn ($amt, $date) => $date < $today->toDateString())
            )->count();

        if ($overdue > 0) {
            $insights[] = [
                'type' => 'risk',
                'color' => 'danger',
                'text' => "$overdue EMI payments are currently overdue",
            ];
        }

        $newInvoices = Invoice::whereDate('created_at', $today)->count();
        if ($newInvoices) {
            $insights[] = [
                'type' => 'finance',
                'color' => 'success',
                'text' => "$newInvoices invoices were generated today",
            ];
        }

        if (empty($insights)) {
            $insights[] = [
                'type' => 'neutral',
                'color' => 'secondary',
                'text' => 'No critical changes detected today',
            ];
        }

        return response()->json($insights);
    }

    // public function ajaxLedgerMonthly()
    // {
    //     $host = '127.0.0.1';
    //     $port = '9000';
    //     $tallyUrl = Settings::getValue('tally_tally_url','http://' . $host . ':' . $port);
    //     try {
    //         $response = Http::timeout(10)->get($tallyUrl);
    //         if ($response->successful()) {
    //             $connection = true;
    //         } else {
    //             $connection = false;
    //         }
    //     } catch (\Exception $e) {
    //         $connection = false;
    //     }
    //     // $ledger_enties = public_path('assets/admin/json/ledger_entries.json');
    //     // $ledger_enties = json_decode(file_get_contents($ledger_enties),true);
    //     $ledger_enties = [];

    //     if ($connection) {
    //         $CompanyName = Settings::getValue('tally_CompanyName');
    //         $YearStart = Settings::getValue('tally_YearStart');
    //         $YearEnd = Settings::getValue('tally_YearEnd');
    //         $CompanyName = $CompanyName;
    //         $YearStart = date("Ymd", strtotime($YearStart));
    //         $YearEnd = date("Ymd", strtotime($YearEnd));
    //     }else{
    //         return response()->json([
    //             'status' => false,
    //             'errors' => [
    //                 "tally is not connected"
    //             ]
    //         ],422);
    //     }
    //     // Build XML using your existing view
    //     $meta = [
    //         'CompanyName' => $CompanyName,
    //         'YearStart' => $YearStart,
    //         'YearEnd' => $YearEnd,
    //     ];

    //     $requestXML = view('page.tally.request.ledger_voucher_monthly', compact('meta'))->render();
    //     $xml = (new TellyController)->sendRequest($requestXML);

    //     $data = simplexml_load_string($xml);
    //     dd($data);
    //     /*
    //         Expected structure:
    //         VOUCHER
    //             DATE
    //             AMOUNT
    //             ISDEEMEDPOSITIVE
    //     */

    //     $monthly = [];

    //     foreach ($data->BODY->DATA->COLLECTION->VOUCHER ?? [] as $v) {

    //         $month = Carbon::parse((string) $v->DATE)->format('Y-m');

    //         $amount = abs((float) $v->AMOUNT);
    //         $isDebit = ((string) $v->ISDEEMEDPOSITIVE === 'Yes');

    //         if (! isset($monthly[$month])) {
    //             $monthly[$month] = [
    //                 'debit' => 0,
    //                 'credit' => 0,
    //             ];
    //         }

    //         if ($isDebit) {
    //             $monthly[$month]['debit'] += $amount;
    //         } else {
    //             $monthly[$month]['credit'] += $amount;
    //         }
    //     }

    //     ksort($monthly);

    //     return response()->json($monthly);
    // }
}
