@extends('layouts.blank')
@section('content')

    @push('scripts')
        <script src="{{ asset('js/razorpay-checkout.js') }}"></script>

        <script>
            const data = {{ Js::from($data) }};

            let options = {
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

            $(document).ready(function() {
                const rzp = new Razorpay(options);
                rzp.open();
                e.preventDefault();
            });
        </script>
    @endpush
@endsection
