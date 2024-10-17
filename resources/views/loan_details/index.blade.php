@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Loan Details') }}</div>

                <div class="card-body">
                        <table class="table table-bordered mt-4">
                            <thead>
                                <tr>
                                    <th>Client ID</th>
                                    <th>Number of Payments</th>
                                    <th>First Payment Date</th>
                                    <th>Last Payment Date</th>
                                    <th>Loan Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($loanDetails as $loan)
                                    <tr>
                                        <td>{{ $loan->clientid }}</td>
                                        <td>{{ $loan->num_of_payment }}</td>
                                        <td>{{ $loan->first_payment_date }}</td>
                                        <td>{{ $loan->last_payment_date }}</td>
                                        <td>{{ $loan->loan_amount }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection