<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SessionLog;
use Hash;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;
use App\Exports\Attendance as AttendanceExport;

class AttendanceController extends Controller
{
    public $months = [
        '01' => 'January',
        '02' => 'February',
        '03' => 'March',
        '04' => 'April',
        '05' => 'May',
        '06' => 'June',
        '07' => 'July',
        '08' => 'August',
        '09' => 'September',
        '10' => 'October',
        '11' => 'November',
        '12' => 'December'
    ];
    public function index(Request $request){

    }
    public function list(Request $request){
        $users = User::get();
        return view("page.users.log",compact('request','users'));
    }

    public function load(Request $request){
        $user = $request->input('user');
        $startdate = $request->input('startdate');
        $enddate = $request->input('enddate');
        $perPage = $request->get('perPage');
        $page = $request->get('page');
        $logs = SessionLog::orderByDesc('created_at');
        if($user != null){
            $logs = $logs->where('staffId',$user);
        }
        if($startdate != null){
            $logs = $logs->whereDate('created_at','>=',$startdate);
        }
        if($enddate != null){
            $logs = $logs->whereDate('created_at','<=',$enddate);
        }
        $count = $logs->count();
        $logs = $logs->limit($perPage)
                    ->skip($perPage * ($page - 1))
                    ->get();
        $nextPage = count($logs) > 0 ? $page + 1 : null;
        return response()->json([
            'data'      => $logs,
            'nextPage'  => $nextPage,
            'status'    => true,
            'total'     => $count
        ], 200);
    }

    public function generate_report(Request $request, $user) {
        $date_ = $request->input('month');
        $year = $month = null;

        if ($date_) {
            $date = explode('-', $date_);
            $year = $date[0] ?? null;
            $month = $date[1] ?? null;
        }
        $startDate = \Carbon\Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();
        $totalDays = $startDate->diffInDays($endDate) + 1;
        $sundays = 0;
        while ($startDate->lte($endDate)) {
            if ($startDate->isSunday()) {
                $sundays++;
            }
            $startDate->addDay();
        }
        $workingDays = $totalDays - $sundays;

        // Get user details
        $user_data = User::where('id', $user)->select('fname', 'lname','salary')->first();
        $user_name = $user_data ? $user_data->fname . ' ' . $user_data->lname : '';

        // Set the range (e.g., "September 2024")
        $range = $this->months[$month] . " " . $year;

        // Fetch logs
        $logs = SessionLog::orderBy('created_at');
        if ($user) {
            $logs->where('staffId', $user);
        }
        if ($month && $year) {
            $logs->whereMonth('created_at', $month)->whereYear('created_at', $year);
        }

        $data = $logs->get();
        $at = [];
        $last_in = '';
        $last_out = '';
        foreach ($data as $value) {
            $dateTime = $value->created_at;
            $date = $dateTime->format('Y-m-d');
            $time = $dateTime->format('H:i:s');

            // Initialize array for the date if it doesn't exist
            $at[$date] = $at[$date] ?? [
                "in" => '',
                "out" => '',
                "hours" => 0,
                "break" => 0  // Track total break time in hours
            ];

            // Check for 'Started' in the message
            if (strpos($value->message, 'Started') !== false) {
                if($at[$date]["in"] != '' && $at[$date]["out"] != ''){
                    $at[$date]["break"] = $this->add_hours($last_out, $time,$at[$date]["break"]);
                }else{
                    $at[$date]["in"] = $time;
                }
                $last_in = $time;
            }

            // Check for 'Ended' in the message
            if (strpos($value->message, 'Ended') !== false) {
                $at[$date]["out"] = $time;
                if ($at[$date]["in"] != '') {
                    $at[$date]["hours"] = $this->add_hours($last_in, $at[$date]["out"], $at[$date]["hours"]);
                }
                $last_out = $time;
            }
        }
        $salary = $user_data->salary;
        $work_hours = Settings::where('name', 'attendance_hours')->value('value');
        $total_hours = array_column($at, 'hours');

        // Convert all hours to decimal
        // dd($total_hours);
        $decimal_minutes = array_map(function ($time) {
            if($time == 0 || $time == "0"){
                $time = "00:00";
            }
            list($hours, $minutes) = explode(':', $time);
            return $minutes + ($hours * 60);
        }, $total_hours);


        // Sum the decimal hours
        $sum_decimal_minutes = array_sum($decimal_minutes);

        // Round off the sum to 2 decimal places
        $worked_minutes = round($sum_decimal_minutes);

        // Calculate working hours and convert to hours and minutes
        $working_minutes = $workingDays * $work_hours * 60;
        $per_day_salary = $salary / $workingDays;
        $per_hour_salary = $per_day_salary / $work_hours;
        $per_minute_salary = $per_hour_salary / 60;
        $payable_salary = ceil($per_minute_salary * $worked_minutes);


        return Excel::download(new AttendanceExport($at, $date_, $user_name, $range, $worked_minutes, $working_minutes, $payable_salary), 'attendance.xlsx');
    }

    // Function to add hours and minutes
    public function add_hours($in_time, $out_time, $current_hours) {
        $in = \Carbon\Carbon::createFromFormat('H:i:s', $in_time);
        $out = \Carbon\Carbon::createFromFormat('H:i:s', $out_time);
        $hours_diff = $in->diffInHours($out);
        $minutes_diff = $in->diffInMinutes($out) % 60;
        list($current_hours, $current_minutes) = sscanf($current_hours, '%d:%d');
        $total_minutes = $current_minutes + $minutes_diff;
        $extra_hours = intdiv($total_minutes, 60);
        $total_minutes = $total_minutes % 60;
        $total_hours = $current_hours + $hours_diff + $extra_hours;
        return sprintf('%02d:%02d', $total_hours, $total_minutes);
    }

}
