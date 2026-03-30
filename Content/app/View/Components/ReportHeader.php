<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ReportHeader extends Component
{
    public $title;

    public function __construct($title = 'Report Title')
    {
        $this->title = $title;
    }

    public function render()
    {
        return view('components.report-header');
    }
}
