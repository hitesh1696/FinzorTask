<?php

namespace App\Services;

use App\Repositories\LoanDetailRepository;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmiService
{
    protected $loanDetailRepository;

    public function __construct(LoanDetailRepository $loanDetailRepository)
    {
        $this->loanDetailRepository = $loanDetailRepository;
    }

    public function processEmiData()
    {
        DB::statement('DROP TABLE IF EXISTS emi_details');

        $minDate = $this->loanDetailRepository->getMinFirstPaymentDate();
        $maxDate = $this->loanDetailRepository->getMaxLastPaymentDate();

        $columns = $this->generateDynamicColumns($minDate, $maxDate);

        DB::statement("CREATE TABLE emi_details (clientid BIGINT, $columns)");

        $loans = $this->loanDetailRepository->getAllLoans();
        foreach ($loans as $loan) {
            $this->insertEmiData($loan);
        }

        return DB::table('emi_details')->get();
    }

    private function generateDynamicColumns($minDate, $maxDate)
    {
        $columns = '';
        $start = new \DateTime($minDate);
        $end = new \DateTime($maxDate);
        while ($start <= $end) {
            $monthName = $start->format('Y_M');
            $columns .= "$monthName DECIMAL(10,2), ";
            $start->modify('+1 month');
        }
        return rtrim($columns, ', ');
    }

    private function insertEmiData($loan)
    {
        $clientId = $loan->clientid;
        $numOfPayments = $loan->num_of_payment;
        $loanAmount = $loan->loan_amount;
        $firstPaymentDate = Carbon::parse($loan->first_payment_date);
        $lastPaymentDate = Carbon::parse($loan->last_payment_date);

        $emiAmount = round($loanAmount / $numOfPayments, 2);
        $monthsDiff = $firstPaymentDate->diffInMonths($lastPaymentDate);

        $emiData = [];
        $remainingAmount = $loanAmount;

        for ($monthOffset = 0; $monthOffset <= $monthsDiff; $monthOffset++) {
            $currentMonth = $firstPaymentDate->copy()->addMonths($monthOffset)->format('Y_M');
            $monthlyEmi = 0.00;

            if ($monthOffset < $numOfPayments) {
                if ($monthOffset == $numOfPayments - 1) {
                    $monthlyEmi = round($remainingAmount, 2);
                } else {
                    $monthlyEmi = $emiAmount;
                    $remainingAmount -= $emiAmount;
                }
            }

            $emiData[$currentMonth] = $monthlyEmi;
        }

        $emiInsertData = array_merge(['clientid' => $clientId], $emiData);
        DB::table('emi_details')->insert($emiInsertData);
    }
}
