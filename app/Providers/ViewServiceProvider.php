<?php

namespace App\Providers;

use App\Models\Page;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        try {
            $settings = Setting::first();

            // Default logo paths
            $logo = asset('imgs/logo.png');
            $mobile_logo = asset('imgs/logo.png');
            $pages = Page::where('active', 1)->select('id', 'title', 'alias')->get();

            // If custom logo is set in settings, check if it exists
            if ($settings && $settings->logo != '') {
                if (Storage::exists($settings->logo)) {
                    $logo = asset(Storage::url($settings->logo));
                }
            }
            if ($settings && $settings->mobile_logo != '') {
                if (Storage::exists($settings->mobile_logo)) {
                    $mobile_logo = asset(Storage::url($settings->mobile_logo));
                }
            }

            if ($settings) {
                // Share the logo data globally with all views
                View::share('logo', $logo);
                View::share('mobile_logo', $mobile_logo);
                View::share('facebook', $settings->facebook);
                View::share('instagram', $settings->instagram);
                View::share('twitter', $settings->x);
                View::share('youtube', $settings->youtube);
                View::share('phone', $settings->phone);
                View::share('email', $settings->email);
                View::share('address', $settings->address);
                View::share('marquee', $settings->marquee);
                View::share('pages', $pages);
            }
        } catch (\Exception $e) {
        }
    }
}
