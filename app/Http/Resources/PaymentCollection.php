<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentCollection extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_membership_id' => $this->user_membership_id,
            'membership' => ($this->user_membership && $this->user_membership->membership) ? $this->user_membership->membership->title : '',
            'month' => ($this->user_membership && $this->user_membership->membership) ? $this->user_membership->membership->month : 0,
            'user_id' => $this->user_id,
            'transaction_id' => $this->transaction_id,
            'payment_method' => $this->payment_method,
            'price' => $this->price,
            'coupon_id' => $this->coupon_id,
            'discount_amount' => $this->discount_amount,
            'status' => $this->status,
            'response' => $this->response,
            'created_at' => date('Y-m-d H:i:s', strtotime($this->created_at)),
        ];
    }
}
