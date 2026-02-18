<?php

namespace App\Exports;

// use View
use Illuminate\Contracts\View\View;
// use frontView
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class Quote implements FromView, ShouldAutoSize
{

    public function  __construct($quote) {
        $this->quote = $quote;
    }

    public function view(): View{
        return view('admin.download.quote',[
            'quote' => $this->quote,
        ]);
    }
}
