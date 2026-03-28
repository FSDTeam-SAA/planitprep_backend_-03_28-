<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    use HasFactory;

    public function getImageAttribute($value)
    {
        // Assuming the images are stored in the public disk (storage/app/public)
        // and your image files are accessible via the 'public' disk's URL
        return asset(\Storage::url('public/goals').'/'.$value);
    }
}
