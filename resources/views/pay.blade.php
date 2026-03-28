@extends('layouts.blank')
@section('content')

<script src="{{ asset('js/razorpay-checkout.js') }}"></script>

<div id="pay-now-div3" class="row">
    <div class="col-md-12 h-100">
        <div class="d-flex justify-content-center align-items-center">
            <div class="my-5">
                <div class="bg-primary text-white p-3">
                    Checkout
                </div>
                <div class="text-dark p-4 bg-white">
                    Hi <strong>{{ $data['customer_name'] }}</strong>, Kindly click the <em>Pay Now</em> button below to complete your payment.
                    <table class="table table-borderless mt-4">
                        <tr>
                            <td>
                                Plan
                            </td>
                            <td>
                                {{ $data['plan']['title'] }} {{ $data['plan']['month'] }} month
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Amount
                            </td>
                            <td>
                                <i class="fa fa-inr" aria-hidden="true"></i>
                                {{ number_format(($data['amount'] / 100), 2) }}
                            </td>
                        </tr>
                    </table>

                    <div class="d-flex justify-content-around mt-5 mb-3">
                        <button id="rzp-button" class="btn px-5 py-2 bg-primary text-white">Pay Now</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const data = {
        order_id: '{{ $data["order_id"] }}',
        amount: '{{ $data["amount"] }}',
        currency: '{{ $data["currency"] }}',
        customer_name: '{{ $data["customer_name"] }}',
        customer_email: '{{ $data["customer_email"] }}',
        customer_phone: '{{ $data["customer_phone"] }}',
    };

    const options = {
        key: '{{ env("RAZORPAY_KEY") }}',
        amount: data.amount,
        currency: data.currency,
        order_id: data.order_id,
        name: data.company_name,
        description: 'Test Transaction',
        image: data.logo,
        prefill: {
            name: data.customer_name,
            email: data.customer_email,
            contact: data.customer_phone
        },
        handler: function (response) {
            let url = '{{ route("check-payment") }}';
            $('#rzp-button').addClass('d-none');
            $.post(url, {
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'payment_id': response.razorpay_payment_id,
            }, function(data, status) {
                if (data == 'done') {
                    window.location.href = '{{ route("payment-success") }}' + '?transaction=' + response.razorpay_payment_id;
                } else {
                    window.location.href = '{{ route("payment-failure") }}';
                }
            });
        },
        theme: {
            color: '#3399cc'
        }
    };

    const rzpButton = document.getElementById('rzp-button');

    rzpButton.onclick = function (e) {
        const rzp = new Razorpay(options);
        rzp.open();
        e.preventDefault();
    };
</script>

@endsection
