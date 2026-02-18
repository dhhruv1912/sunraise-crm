<?php

namespace App\Http\Controllers;

use App\Exports\Attendance as AttendanceExport;
use App\Models\SessionLog;
use App\Models\Settings;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceController extends Controller
{
    public function index()
    {
        return view('page.attendance.index');
    }

    /* ================= WIDGETS ================= */
    public function ajaxWidgets()
    {
        $today = Carbon::today();

        $totalPresent = DB::table('session_logs')
            ->whereDate('created_at', $today)
            ->distinct('staffId')
            ->count('staffId');

        $totalUsers = DB::table('users')->count();

        return view('page.attendance.widgets', [
            'present' => $totalPresent,
            'absent' => max(0, $totalUsers - $totalPresent),
            'total' => $totalUsers,
        ]);
    }

    /* ================= LIST ================= */
    public function ajaxList(Request $request)
    {
        $date     = $request->get('date', Carbon::today()->toDateString());
        $perPage  = (int) $request->get('per_page', 10); // default 10
        $page     = (int) $request->get('page', 1);

        $query = DB::table('session_logs as s')
            ->select(
                's.id',
                's.location',
                's.device',
                's.ip',
                's.created_at',
                's.updated_at'
            )
            ->whereDate('s.created_at', $date)
            ->orderBy('s.created_at', 'desc');

        /**
         * IMPORTANT:
         * We clone the query for total count
         * (Query Builder pagination best practice)
         */
        $total = (clone $query)->count();

        $logs = $query
            ->forPage($page, $perPage)
            ->get();

        return response()->json([
            'data' => $logs,
            'pagination' => [
                'current_page' => $page,
                'per_page'     => $perPage,
                'total'        => $total,
                'last_page'    => (int) ceil($total / $perPage),
            ],
        ]);
    }

    public function generateReport(Request $request, $userId)
    {
        $date_ = $request->input('month'); // YYYY-MM
        abort_if(! $date_, 400, 'Month is required');

        [$year, $month] = explode('-', $date_);

        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        /* ================= WORKING DAYS ================= */
        $totalDays = $startDate->daysInMonth;
        $sundays = 0;

        $cursor = $startDate->copy();
        while ($cursor->lte($endDate)) {
            if ($cursor->isSunday()) {
                $sundays++;
            }
            $cursor->addDay();
        }

        $workingDays = $totalDays - $sundays;

        /* ================= USER ================= */
        $user = User::select('fname', 'lname', 'salary')->findOrFail($userId);
        $userName = "{$user->fname} {$user->lname}";

        $range = $startDate->format('F Y');

        /* ================= FETCH LOGS ================= */
        $logs = SessionLog::where('staffId', $userId)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->orderBy('created_at')
            ->get();

        $attendance = [];
        $lastIn = null;
        $lastOut = null;

        foreach ($logs as $log) {
            $date = $log->created_at->format('Y-m-d');
            $time = $log->created_at->format('H:i:s');

            $attendance[$date] ??= [
                'in' => '',
                'out' => '',
                'hours' => '00:00',
                'break' => '00:00',
            ];

            if (str_contains($log->message, 'Started')) {
                if ($attendance[$date]['in'] && $attendance[$date]['out']) {
                    $attendance[$date]['break'] =
                        $this->addHours($lastOut, $time, $attendance[$date]['break']);
                } else {
                    $attendance[$date]['in'] = $time;
                }
                $lastIn = $time;
            }

            if (str_contains($log->message, 'Ended')) {
                $attendance[$date]['out'] = $time;

                if ($attendance[$date]['in']) {
                    $attendance[$date]['hours'] =
                        $this->addHours($lastIn, $time, $attendance[$date]['hours']);
                }
                $lastOut = $time;
            }
        }

        /* ================= SALARY CALC ================= */
        $workHoursPerDay = (int) Settings::where('name', 'attendance_hours')->value('value');
        $salary = $user->salary;

        $workedMinutes = collect($attendance)->sum(function ($row) {
            [$h, $m] = explode(':', $row['hours']);

            return ($h * 60) + $m;
        });

        $workingMinutes = $workingDays * $workHoursPerDay * 60;

        $perDaySalary = $salary / $workingDays;
        $perHourSalary = $perDaySalary / $workHoursPerDay;
        $perMinuteSalary = $perHourSalary / 60;

        $payableSalary = ceil($perMinuteSalary * $workedMinutes);

        return Excel::download(
            new AttendanceExport(
                $attendance,
                $date_,
                $userName,
                $range,
                $workedMinutes,
                $workingMinutes,
                $payableSalary
            ),
            'attendance_'.$date_.'.xlsx'
        );
    }

    /* ================= HELPER ================= */
    private function addHours($inTime, $outTime, $current)
    {
        $in = Carbon::createFromFormat('H:i:s', $inTime);
        $out = Carbon::createFromFormat('H:i:s', $outTime);

        $minutes = $in->diffInMinutes($out);

        [$ch, $cm] = explode(':', $current);
        $total = ($ch * 60) + $cm + $minutes;

        return sprintf('%02d:%02d', intdiv($total, 60), $total % 60);
    }

    public function salarySlipPdf(Request $request, $userId)
    {
        $date_ = $request->input('month');
        abort_if(! $date_, 400, 'Month is required');

        [$year, $month] = explode('-', $date_);

        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        /* ================= WORKING DAYS ================= */
        $totalDays = $startDate->daysInMonth;
        $sundays = 0;

        $cursor = $startDate->copy();
        while ($cursor->lte($endDate)) {
            if ($cursor->isSunday()) {
                $sundays++;
            }
            $cursor->addDay();
        }

        $workingDays = $totalDays - $sundays;

        /* ================= USER ================= */
        $user = User::select('fname', 'lname', 'salary')->findOrFail($userId);

        /* ================= ATTENDANCE ================= */
        $logs = SessionLog::where('staffId', $userId)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->orderBy('created_at')
            ->get();

        $attendance = [];
        $lastIn = null;

        foreach ($logs as $log) {
            $date = $log->created_at->format('Y-m-d');
            $time = $log->created_at->format('H:i:s');

            $attendance[$date] ??= ['hours' => '00:00'];

            if (str_contains($log->message, 'Started')) {
                $lastIn = $time;
            }

            if (str_contains($log->message, 'Ended') && $lastIn) {
                $attendance[$date]['hours'] =
                    $this->addHours($lastIn, $time, $attendance[$date]['hours']);
            }
        }

        /* ================= SALARY ================= */
        $workHours = (int) Settings::where('name', 'attendance_hours')->value('value');
        $salary = $user->salary;

        $workedMinutes = collect($attendance)->sum(function ($row) {
            [$h, $m] = explode(':', $row['hours']);

            return ($h * 60) + $m;
        });

        $workingMinutes = $workingDays * $workHours * 60;

        $perDaySalary = $salary / $workingDays;
        $perHourSalary = $perDaySalary / $workHours;
        $perMinuteSalary = $perHourSalary / 60;

        $payableSalary = ceil($perMinuteSalary * $workedMinutes);

        /* ================= PDF ================= */
        $pdf = Pdf::loadView('pdf.salary-slip', [
            'user' => $user,
            'month' => $startDate->format('F Y'),
            'workingDays' => $workingDays,
            'workedMinutes' => $workedMinutes,
            'workingMinutes' => $workingMinutes,
            'salary' => $salary,
            'payableSalary' => $payableSalary,
        ])->setPaper('A4');

        return $pdf->download(
            'salary-slip-'.$user->fname.'-'.$year.'-'.$month.'.pdf'
        );
    }
}
