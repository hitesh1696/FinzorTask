<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LoanDetail;

class LoanDetailController extends Controller
{
    public function index()
    {
        $loanDetails = LoanDetail::all();
        return view('loan_details.index', compact('loanDetails'));
    }

}
