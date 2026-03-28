<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appliance extends Model
{
    use HasFactory;

    protected $table = 'appliances';

    // --- Add these lines to use your custom column names ---
    const CREATED_AT = 'createdate';
    const UPDATED_AT = 'modifydate';
    // ----------------------------------------------------

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'isActive',
    ];
}