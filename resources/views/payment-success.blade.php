@extends('layouts.front')
@section('content')

<div class="row">
    <div class="col-md-12 py-5">
        <div class="d-flex justify-content-around">
            <div class="text-center payment-success-div">
                <img src="{{ asset('imgs/tick-480.png') }}" alt="" class="img-fluid payment-success-tick">
                <h4>Payment Successful</h4>
                <p>Congratulations, your payment is successfully processed. Your memebership account is active now.</p>
                @if ($transactionId != NULL)
                    <p class="transaction-id">Transaction ID: <span>{{ $transactionId }}</span></p>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
