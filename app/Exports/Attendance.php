<?php

namespace App\Exports;

// use View
use Illuminate\Contracts\View\View;
// use frontView
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class Attendance implements FromView, ShouldAutoSize
{

    public function  __construct($at, $date_, $user_name, $range, $worked_minutes, $working_minutes, $payable_salary) {
        $this->at = $at;
        $this->date_ = $date_;
        $this->user_name = $user_name;
        $this->range = $range;
        $this->worked_minutes = $worked_minutes;
        $this->working_minutes = $working_minutes;
        $this->payable_salary = $payable_salary;
    }

    public function view(): View{
        return view('admin.download.attandance',[
            'at' => $this->at,
            'date_' => $this->date_,
            'user_name' => $this->user_name,
            'range' => $this->range,
            'worked_minutes' => $this->worked_minutes,
            'working_minutes' => $this->working_minutes,
            'payable_salary' => $this->payable_salary,
        ]);
    }
}
