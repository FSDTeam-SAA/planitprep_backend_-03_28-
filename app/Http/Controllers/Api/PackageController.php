<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use App\Models\Package;

class PackageController extends Controller
{
    public function getPackages()
    {
        $packages = Package::where('active', 1)->orderBy('rank')->get();
        $features = Feature::where('active', 1)->orderBy('rank')->get();

        return response()->json(['status' => true, 'packages' => $packages, 'features' => $features]);
    }
}
