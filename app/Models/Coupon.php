<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    public function coupon_users(): HasMany
    {
        return $this->hasMany(CouponUser::class, 'coupon_id');
    }
}
