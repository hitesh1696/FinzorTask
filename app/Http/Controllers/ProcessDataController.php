<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\EmiService;

class ProcessDataController extends Controller
{
    protected $emiService;
    public function __construct(EmiService $emiService)
    {
        $this->emiService = $emiService;
    }

    public function show()
    {
        $emi_details = '';
        return view('process_data.index', compact('emi_details'));
    }

    public function process()
    {
        $emi_details = $this->emiService->processEmiData();

        return redirect()->back()->with([
            'emi_details' => $emi_details,
            'success' => 'data processed successfully!'
        ]);
    }
}
