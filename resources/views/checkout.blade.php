@extends('layouts.blank')
@section('content')

    <div class="row min-vh-100 d-flex justify-content-center align-items-center">
        <div class="col-md-12 d-flex justify-content-center">
            <div class="payment-form-container bg-white">
                <h5 class="p-3 bg-main-color text-white">Checkout</h5>
                <input hidden name="amount" id="amount" value="{{ $data['amount'] }}" required readonly>
                <div class="row p-4">
                    <div class="col-md-6 col-xxl-7">
                        <h5>Customer Information</h5>
                        <hr>
                        <div class="mb-3 mt-3">
                            <label class="form-label text-secondary">Full Name</label>
                            <input id="username" name="fullname" type="text" class="form-control" maxlength="50" value="{{ $data['username'] }}" autofocus>
                        </div>
                        <div class="row">
                            <div class="mb-3">
                                <label for="yourEmail" class="form-label text-secondary">Email address</label>
                                <input id="email" name="email" type="email" class="form-control" id="yourEmail" aria-describedby="emailHelp" maxlength="50" value="{{ $data['email'] }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-secondary">Phone</label>
                                <input id="phone" name="phone" type="text" class="form-control phone-input" maxlength="20" value="{{ $data['phone'] }}">
                            </div>
                        </div>
                        <div id="emailHelp" class="form-text">We'll never share your personal information with anyone else.</div>
                    </div>
                    <div class="col-md-6 col-xxl-5 border-start">
                        <h5>Your Cart</h5>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <div>
                                <span class="fw-bold">{{ $data['plan'] }}</span>
                            </div>
                            <div>
                                <i class="fa fa-inr" aria-hidden="true"></i>
                                <span class="fw-bold">{{ number_format($data['amount'], 2) }}</span>
                            </div>
                        </div>
                        <div class="mb-3 checkout-coupon-div">
                            <div class="input-group mb-3">
                                <input id="coupon-ele" name="phone" type="text" class="form-control" maxlength="20" placeholder="Coupon if you have">
                                <button id="coupon-btn" class="btn coupon-btn" type="button" id="apply-coupon-btn">Apply</button>
                            </div>
                            <div id="coupon-msg" class="ps-1">&nbsp;</div>
                        </div>
                        <div>
                            <table class="table table-borderless">
                                <tr>
                                    <td>Subtotal</td>
                                    <td class="text-end">
                                        <i class="fa fa-inr" aria-hidden="true"></i>
                                        <span id="subtotal-span">
                                            {{ number_format($data['amount'], 2) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">Discount</td>
                                    <td class="text-end">
                                        <i class="fa fa-inr" aria-hidden="true"></i>
                                        <span id="discount-span">0.00</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">Total</td>
                                    <td class="text-end">
                                        <i class="fa fa-inr" aria-hidden="true"></i>
                                        <span id="total-span">{{ number_format($data['amount'], 2) }}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="d-flex justify-content-around mt-4">
                            <button id="complete-order-btn" class="btn px-5 py-2 bg-primary text-white">Complete Order</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                $('#coupon-btn').click(function() {
                    $.ajax({
                        url: "{{ route('apply-coupon') }}",
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            coupon: $('#coupon-ele').val(),
                            amount: '{{ $data["amount"] }}',
                        },
                        success: function(res) {
                            if (res == 0) {
                                $('#coupon-ele').val('');
                                $('#coupon-msg').removeClass('text-success');
                                $('#coupon-msg').addClass('text-danger');
                                $('#coupon-msg').text('Invalid coupon!');
                            } else {
                                $('#coupon-msg').text('Coupon applied.');
                                $('#discount-span').text(res.discount);
                                $('#total-span').text(res.amount);
                            }
                        }
                    });
                });

                $('#complete-order-btn').click(function() {
                    $.ajax({
                        url: "{{ route('complete-order') }}",
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            plan_id: '{{ $data["plan_id"] }}',
                            coupon: $('#coupon-ele').val(),
                        },
                        success: function(res) {
                            let url = '{{ route("complete-payment", ":id") }}';
                            url = url.replace(':id', res);
                            let email = $('#email').val();
                            let phone = $('#phone').val();
                            url += `?email=${encodeURIComponent(email)}&phone=${encodeURIComponent(phone)}`;
                            window.location.href = url;
                        }
                    });
                });
            });
        </script>
    @endpush

@endsection
