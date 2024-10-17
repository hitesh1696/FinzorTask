@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Process EMI Details') }}</div>

                <div class="card-body">
                            <form method="POST" action="{{ route('process-data') }}">
                                @csrf
                                <button type="submit">Process Data</button>
                            </form>

                        </div>
                   
            </div>
           
        </div>

        @if(session('emi_details'))
        <div class="">
                <h2>EMI Details</h2>
            <table class="table table-bordered mt-4">
                <thead>
                    <tr>
                        <th>Client ID</th>
                        @foreach(array_keys((array) session('emi_details')->first()) as $column)
                            @if($column !== 'clientid')
                                <th>{{ $column }}</th>
                            @endif
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach(session('emi_details') as $row)
                        <tr>
                            <td>{{ $row->clientid }}</td>
                            @foreach((array) $row as $key => $value)
                                @if($key !== 'clientid')
                                    <td>{{ number_format($value, 2) }}</td>
                                @endif
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        @endif
    </div>
</div>
@endsection