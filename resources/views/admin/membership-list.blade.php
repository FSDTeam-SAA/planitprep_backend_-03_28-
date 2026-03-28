@php
    $colors = ['bg-red-500', 'bg-blue-500', 'bg-green-500', 'bg-yellow-500', 'bg-purple-500'];
@endphp

<div>
    <div>
        <div class="mt-2 mb-3 checkout-coupon-div">
            <div class="input-group">
                <span class="input-group-text w-116px">Coupon</span>
                <select id="coupon-select" class="form-control">
                    <option selected value="NULL">Not Selected</option>
                    @forelse ($coupons as $coupon)
                        <option value="{{ $coupon['code'] }}">{{ $coupon['code'] }} ( {{ $coupon['discount_type'] }} {{ $coupon['amount'] }} )</option>
                    @empty
                        {{--  --}}
                    @endforelse
                </select>
            </div>
            <div id="coupon-msg" class="ps-1">&nbsp;</div>
        </div>
    </div>
    @forelse ($memberships as $item)
        <div class="{{ $colors[$loop->index % count($colors)] }} text-white my-2">
            <a href="javascript:void(0)" class="text-white" onclick="generatePaymentLink({{ $user_id }}, {{ $item->price }}, {{ $item->id }})">
                <div class="p-2 text-center f-16">
                    {{ $item->title }}
                    &nbsp;&nbsp;
                    {{ $item->month }} month
                    &nbsp;&nbsp;
                    <i class="fa fa-inr" aria-hidden="true"></i>{{ $item->price }}
                </div>
            </a>
        </div>
    @empty
        {{--  --}}
    @endforelse
</div>
