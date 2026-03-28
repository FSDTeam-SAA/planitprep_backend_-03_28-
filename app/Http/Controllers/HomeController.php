<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Country;
use App\Models\Coupon;
use App\Models\Enquiry;
use App\Models\Feature;
use App\Models\Homepage;
use App\Models\NewsletterSubscription;
use App\Models\Package;
use App\Models\Page;
use App\Models\Service;
use App\Models\Setting;
use App\Models\State;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index($alias = null)
    {
        if ($alias != null) {
            $page = Page::where('alias', $alias)->first();

            if ($page != null) {
                $meta_title = $page->meta_title;
                $meta_description = $page->meta_description;

                return view('page', compact('page', 'meta_title', 'meta_description'));
            } else {
                return view('page-not-found');
            }
        }

        $data = Homepage::first();

        // About img
        if ($data->about_image_1 != '') {
            if (Storage::exists('public/'.$data->about_image_1)) {
                $data->about_image_1 = asset(Storage::url($data->about_image_1));
            }
        }

        // Contact img
        if ($data->contact_image_1 != '') {
            if (Storage::exists('public/'.$data->contact_image_1)) {
                $data->contact_image_1 = asset(Storage::url($data->contact_image_1));
            }
        }

        // Intro app img
        if ($data->intro_app_image_1 != '') {
            if (Storage::exists('public/'.$data->intro_app_image_1)) {
                $data->intro_app_image_1 = asset(Storage::url($data->intro_app_image_1));
            }
        }

        if ($data->intro_app_image_2 != '') {
            if (Storage::exists('public/'.$data->intro_app_image_2)) {
                $data->intro_app_image_2 = asset(Storage::url($data->intro_app_image_2));
            }
        }

        if ($data->intro_app_image_3 != '') {
            if (Storage::exists('public/'.$data->intro_app_image_3)) {
                $data->intro_app_image_3 = asset(Storage::url($data->intro_app_image_3));
            }
        }

        // Slider
        if ($data->slider_image_1 != '') {
            if (Storage::exists('public/'.$data->slider_image_1)) {
                $data->slider_image_1 = asset(Storage::url($data->slider_image_1));
            }
        }
        if ($data->slide_image_1 != '') {
            if (Storage::exists('public/'.$data->slide_image_1)) {
                $data->slide_image_1 = asset(Storage::url($data->slide_image_1));
            }
        }
        if ($data->slide_image_2 != '') {
            if (Storage::exists('public/'.$data->slide_image_2)) {
                $data->slide_image_2 = asset(Storage::url($data->slide_image_2));
            }
        }
        if ($data->slide_image_3 != '') {
            if (Storage::exists('public/'.$data->slide_image_3)) {
                $data->slide_image_3 = asset(Storage::url($data->slide_image_3));
            }
        }
        if ($data->slide_image_4 != '') {
            if (Storage::exists('public/'.$data->slide_image_4)) {
                $data->slide_image_4 = asset(Storage::url($data->slide_image_4));
            }
        }
        if ($data->slide_image_5 != '') {
            if (Storage::exists('public/'.$data->slide_image_5)) {
                $data->slide_image_5 = asset(Storage::url($data->slide_image_5));
            }
        }
        if ($data->slide_image_6 != '') {
            if (Storage::exists('public/'.$data->slide_image_6)) {
                $data->slide_image_6 = asset(Storage::url($data->slide_image_6));
            }
        }

        // Service
        $temp = [];
        if ($data->service != '') {
            $temp = explode(',', $data->service);
        }

        $data->service = Service::whereIn('id', $temp)->where('active', 1)->orderBy('rank')->get();

        foreach ($data->service as $item) {
            if ($item->image != '') {
                if (Storage::exists($item->image)) {
                    $item->image = asset(Storage::url($item->image));
                }
            }
        }

        // Testimonial
        $temp = [];
        if ($data->testimonial != '') {
            $temp = explode(',', $data->testimonial);
        }

        $data->testimonial = Testimonial::whereIn('id', $temp)->where('active', 1)->latest()->get();

        foreach ($data->testimonial as $item) {
            if ($item->image != '') {
                if (Storage::exists($item->image)) {
                    $item->image = asset(Storage::url($item->image));
                } else {
                    $item->image = asset('admin/images/defaultUser.png');
                }
            } else {
                $item->image = asset('admin/images/defaultUser.png');
            }
        }

        // Features
        $temp = [];
        if ($data->features != '') {
            $temp = explode(',', $data->features);
        }

        $data->features = Feature::whereIn('id', $temp)->where('active', 1)->latest()->get();

        // Package
        $temp = [];
        if ($data->packages != '') {
            $temp = explode(',', $data->packages);
            $temp = array_map('intval', $temp);
        }

        $packages = Package::whereIn('id', $temp)->get();

        foreach ($packages as $item) {
            if ($item->image != '') {
                if (Storage::exists($item->image)) {
                    $item->image = asset(Storage::url($item->image));
                }
            }
        }

        $data->packages = $packages;

        // Our clients
        $temp = [];
        if ($data->our_clients_image != '') {
            $temp = explode(',', $data->our_clients_image);
        }
        $arr = [];
        foreach ($temp as $item) {
            if ($item != '') {
                if (Storage::disk('public')->exists($item)) {
                    $arr[] = asset(Storage::url($item));
                }
            }
        }
        $data->our_clients_image = $arr;

        $settings = Setting::first();

        if ($settings->apple_store_image != '') {
            if (Storage::exists($settings->apple_store_image)) {
                $settings->apple_store_image = asset(Storage::url($settings->apple_store_image));
            } else {
                $settings->apple_store_image = asset('imgs/app-store.png');
            }
        } else {
            $settings->apple_store_image = asset('imgs/app-store.png');
        }

        if ($settings->android_store_image != '') {
            if (Storage::exists($settings->android_store_image)) {
                $settings->android_store_image = asset(Storage::url($settings->android_store_image));
            } else {
                $settings->android_store_image = asset('imgs/app-store.png');
            }
        } else {
            $settings->android_store_image = asset('imgs/app-store.png');
        }

        return view('homepage', compact('data', 'settings'));
    }

    // ck editor upload file
    public function uploadFile(Request $request)
    {
        if ($request->hasFile('upload')) {
            $fileNameWithExtension = $request->file('upload')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithExtension, PATHINFO_FILENAME);
            $extension = $request->file('upload')->getClientOriginalExtension();
            $fileNameToStore = $fileName.'_'.time().'.'.$extension;
            $request->file('upload')->storeAs('uploads/ckeditor', $fileNameToStore, 'public');

            $CKEditorFuncNum = $request->input('CKEditorFuncNum') ? $request->input('CKEditorFuncNum') : 0;

            if ($CKEditorFuncNum > 0) {
                $url = asset('storage/ckeditor/'.$fileNameToStore);
                $msg = 'Image successfully uploaded';
                $renderHtml = "<script>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$msg')</script>";

                @header('Content-type: text/html; charset=utf-8');
                echo $renderHtml;
            } else {
                $url = asset('storage/uploads/ckeditor/'.$fileNameToStore);
                $msg = 'Image successfully uploaded';
                $renderHtml = "<script>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$msg')</script>";

                return response()->json([
                    'uploaded' => '1',
                    'fileName' => $fileNameToStore,
                    'url' => $url,
                ]);
            }
        }
    }

    // Get countries
    public function getCountries(Request $request)
    {
        $search = $request->searchTerm;
        if ($search == '') {
            $countries = Country::get();
        } else {
            $countries = Country::where('name', 'like', '%'.$search.'%')->limit(5)->get();
        }
        $response = [];
        foreach ($countries as $country) {
            $response[] = [
                'id' => $country->id,
                'text' => $country->name,
            ];
        }

        return response()->json($response);
    }

    // Get states
    public function getStates(Request $request)
    {
        $search = $request->searchTerm;
        $country_id = $request->country_id;
        if ($search == '') {
            $states = State::where('country_id', $country_id)->limit(5)->get();
        } else {
            $states = State::where('name', 'like', '%'.$search.'%')->where('country_id', $country_id)->limit(5)->get();
        }
        $response = [];
        foreach ($states as $state) {
            $response[] = [
                'id' => $state->id,
                'text' => $state->name,
            ];
        }

        return response()->json($response);
    }

    // Get cities
    public function getCities(Request $request)
    {
        $search = $request->searchTerm;
        $state_id = $request->state_id;
        if ($search == '') {
            $cities = City::where('state_id', $state_id)->limit(5)->get();
        } else {
            $cities = City::where('name', 'like', '%'.$search.'%')->where('state_id', $state_id)->limit(5)->get();
        }
        $response = [];
        foreach ($cities as $city) {
            $response[] = [
                'id' => $city->id,
                'text' => $city->name,
            ];
        }

        return response()->json($response);
    }

    // Get coupons
    public function getCoupons(Request $request)
    {
        $search = $request->searchTerm;
        $coupons = Coupon::where('active', '1');
        if ($search != '') {
            $coupons = $coupons->whereRaw('((title like "%'.$search.'%") or (code like "%'.$search.'%"))');
        }
        $coupons = $coupons->limit(15)->get();
        $response = [];
        foreach ($coupons as $coupon) {
            $response[] = [
                'id' => $coupon->id,
                'text' => $coupon->title.' ('.$coupon->code.')',
            ];
        }

        return response()->json($response);
    }

    // Get users
    public function getUsers(Request $request)
    {
        $search = $request->searchTerm;
        $users = User::where('active', 1)
            ->where('full_name', '!=', '');
        if ($search != '') {
            $users = $users->whereRaw('(username like "%'.$search.'%") or (full_name like "%'.$search.'%")');
        }
        $users = $users->limit(15)->get();
        $response = [];
        foreach ($users as $user) {
            $response[] = [
                'id' => $user->id,
                'text' => $user->username.' ('.$user->full_name.')',
            ];
        }

        return response()->json($response);
    }

    public function store_contact_form(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'email' => 'required|email|max:50',
            'phone' => 'required|string|max:20',
            'subject' => 'required|string|max:100',
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return [
                'status' => 'false',
                'errors' => $validator->errors(),
            ];
        }

        $enquiry = new Enquiry;
        $enquiry->name = $request->name;
        $enquiry->email = $request->email;
        $enquiry->phone = $request->phone;
        $enquiry->subject = $request->subject;
        $enquiry->message = $request->message;
        $enquiry->save();

        echo 'done';
        exit;
    }

    public function subscribe_newsletter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:50|unique:App\Models\NewsletterSubscription,email',
        ]);

        if ($validator->fails()) {
            return [
                'status' => 'false',
                'errors' => $validator->errors(),
            ];
        }

        $r = new NewsletterSubscription;
        $r->email = $request->email;
        $r->save();

        echo 'done';
        exit;
    }
}
