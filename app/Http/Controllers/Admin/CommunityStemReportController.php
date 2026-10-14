<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CommunityStemOverallExport;
use App\Exports\CommunityStemMyRaExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class CommunityStemReportController extends Controller
{
    public function myra()
    {
        return Excel::download(
            new CommunityStemMyRaExport(),
            'Community_STEM_MyRA_' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function overall()
    {
        return Excel::download(
            new CommunityStemOverallExport(),
            'Community_STEM_Overall_' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}
