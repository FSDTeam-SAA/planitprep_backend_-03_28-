<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $status = $this->active;
        // status 1:unused, 0:used, 2:expired
        if ($this->coupon_users[0]->expiry_date < date('Y-m-d H:i:s')) {
            $status = 2;
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'code' => $this->code,
            'limit' => $this->limit,
            'start_date' => date('d M y H:i A', strtotime($this->start_date)),
            'end_date' => date('d M y H:i A', strtotime($this->coupon_users[0]->expiry_date)),
            'discount_type' => $this->discount_type,
            'amount' => strval($this->amount),
            'description' => strip_tags($this->description),
            'active' => $this->active,
            'status' => $status,
        ];
    }
}
