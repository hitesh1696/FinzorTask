<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\LoanDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProcessDataController extends Controller
{

    public function show()
    {
        $emi_details = '';
        return view('process_data.index', compact('emi_details'));
    }

    public function process()
    {
        DB::statement('DROP TABLE IF EXISTS emi_details');

        $minDate = LoanDetail::min('first_payment_date');
        $maxDate = LoanDetail::max('last_payment_date');

        $columns = $this->generateDynamicColumns($minDate, $maxDate);

        DB::statement("CREATE TABLE emi_details (clientid BIGINT, $columns)");

        $loans = LoanDetail::all();
        foreach ($loans as $loan) {
            $this->insertEmiData($loan);
        }

        $emi_details = DB::table('emi_details')->get();

        return redirect()->back()->with([
            'emi_details' => $emi_details,
            'success' => 'data processed successfully!'
        ]);
    }

    private function generateDynamicColumns($minDate, $maxDate)
    {
        // Generate column names for each month between minDate and maxDate
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
        $firstPaymentDate = \Carbon\Carbon::parse($loan->first_payment_date);
        $lastPaymentDate = \Carbon\Carbon::parse($loan->last_payment_date);

        // Calculate the monthly EMI amount
        $emiAmount = round($loanAmount / $numOfPayments, 2); // Round to 2 decimal places

        // Calculate the number of months between first and last payment date
        $monthsDiff = $firstPaymentDate->diffInMonths($lastPaymentDate);

        // Prepare the dynamic columns for the table
        $emiData = [];
        $remainingAmount = $loanAmount; // Track remaining amount for the last EMI adjustment

        // Loop through all months between the first and last payment date
        for ($monthOffset = 0; $monthOffset <= $monthsDiff; $monthOffset++) {
            $currentMonth = $firstPaymentDate->copy()->addMonths($monthOffset)->format('Y_M');

            // Initialize the EMI value for this month
            $monthlyEmi = 0.00;

            // If we are within the number of payments, add EMI to the corresponding month
            if ($monthOffset < $numOfPayments) {
                // If it's the last payment, adjust the EMI to ensure total matches loan amount
                if ($monthOffset == $numOfPayments - 1) {
                    $monthlyEmi = round($remainingAmount, 2);
                } else {
                    $monthlyEmi = $emiAmount;
                    $remainingAmount -= $emiAmount; // Subtract from remaining amount
                }
            }

            // Add the EMI value to the dynamic column for the current month
            $emiData[$currentMonth] = $monthlyEmi;
        }

        // Insert data into emi_details table
        $emiInsertData = array_merge(['clientid' => $clientId], $emiData);

        DB::table('emi_details')->insert($emiInsertData);
    }


}
