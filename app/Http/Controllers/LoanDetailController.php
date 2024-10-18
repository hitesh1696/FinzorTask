<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Repositories\LoanDetailRepository;

class LoanDetailController extends Controller
{
    protected $loanDetailRepository;

    public function __construct(LoanDetailRepository $loanDetailRepository)
    {
        $this->loanDetailRepository = $loanDetailRepository;
    }

    public function index()
    {
        $loanDetails = $this->loanDetailRepository->getAllLoanDetails();
        return view('loan_details.index', compact('loanDetails'));
    }
}
