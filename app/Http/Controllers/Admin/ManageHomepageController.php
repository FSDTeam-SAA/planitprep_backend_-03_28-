<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use App\Models\Homepage;
use App\Models\Package;
use App\Models\Service;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ManageHomepageController extends Controller
{
    public function index()
    {
        $data = Homepage::first();

        // About img
        if ($data->about_image_1 != '') {
            $data->about_image_1 = asset(Storage::url($data->about_image_1));
        }

        // Contact img
        if ($data->contact_image_1 != '') {
            $data->contact_image_1 = asset(Storage::url($data->contact_image_1));
        }

        // Intro app img
        if ($data->intro_app_image_1 != '') {
            $data->intro_app_image_1 = asset(Storage::url($data->intro_app_image_1));
        }

        if ($data->intro_app_image_2 != '') {
            $data->intro_app_image_2 = asset(Storage::url($data->intro_app_image_2));
        }

        if ($data->intro_app_image_3 != '') {
            $data->intro_app_image_3 = asset(Storage::url($data->intro_app_image_3));
        }

        // Slider img
        if ($data->slider_image_1 != '') {
            $data->slider_image_1 = asset(Storage::url($data->slider_image_1));
        }
        if ($data->slide_image_1 != '') {
            $data->slide_image_1 = asset(Storage::url($data->slide_image_1));
        }
        if ($data->slide_image_2 != '') {
            $data->slide_image_2 = asset(Storage::url($data->slide_image_2));
        }
        if ($data->slide_image_3 != '') {
            $data->slide_image_3 = asset(Storage::url($data->slide_image_3));
        }
        if ($data->slide_image_4 != '') {
            $data->slide_image_4 = asset(Storage::url($data->slide_image_4));
        }
        if ($data->slide_image_5 != '') {
            $data->slide_image_5 = asset(Storage::url($data->slide_image_5));
        }
        if ($data->slide_image_6 != '') {
            $data->slide_image_6 = asset(Storage::url($data->slide_image_6));
        }

        // Service
        $services = Service::select('id', 'title')->where('active', 1)->get();

        if ($data['service'] != '') {
            $data['service'] = explode(',', $data['service']);
        } else {
            $data['service'] = [];
        }

        // Testimonials
        $testimonials = Testimonial::select('id', 'name', 'designation', 'rating', 'review')->where('active', 1)->get();

        if ($data['testimonial'] != '') {
            $data['testimonial'] = explode(',', $data['testimonial']);
        } else {
            $data['testimonial'] = [];
        }

        $our_clients_image_urls = $data['our_clients_image'];

        if ($our_clients_image_urls != '') {
            $our_clients_image_urls = explode(',', $our_clients_image_urls);
            $our_clients_image_urls = array_reverse($our_clients_image_urls);
        } else {
            $our_clients_image_urls = [];
        }

        $packages = Package::where('active', 1)->get();

        if ($data['packages'] != '') {
            $data['packages'] = explode(',', $data['packages']);
        } else {
            $data['packages'] = [];
        }

        // Features
        $features = Feature::select('id', 'title')->where('active', 1)->get();

        if ($data['features'] != '') {
            $data['features'] = explode(',', $data['features']);
        } else {
            $data['features'] = [];
        }

        return view('admin.manage-homepage', compact('data', 'services', 'testimonials', 'our_clients_image_urls', 'packages', 'features'));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'about_image_1' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,webp,svg',
            'about_title' => 'nullable|string|max:191',
            'about_description' => 'nullable',
            'about_button_text' => 'nullable|string|max:191',
            'about_button_url' => 'nullable|string|max:191',

            'intro_app_image_1' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,webp,svg',
            'intro_app_image_2' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,webp,svg',
            'intro_app_image_3' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,webp,svg',
            'intro_app_title' => 'nullable|string|max:191',
            'intro_app_description' => 'nullable',
            'intro_app_button_text' => 'nullable|string|max:191',
            'intro_app_button_url' => 'nullable|string|max:191',

            'contact_image_1' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,webp,svg',
            'contact_title' => 'nullable|string|max:191',
            'contact_description' => 'nullable',

            'slider_heading_1' => 'nullable|string|max:191',
            'slider_heading_3' => 'nullable|string|max:191',
            'slider_image_1' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,webp,svg',
            'slider_image_2' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,webp,svg',
            'slide_image_1' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,webp,svg',
            'slide_image_2' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,webp,svg',
            'slide_image_3' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,webp,svg',
            'slide_image_4' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,webp,svg',
            'slide_image_5' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,webp,svg',
            'slide_image_6' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,webp,svg',

            'service' => 'nullable',
            'testimonial' => 'nullable',

            'our_clients_text' => 'nullable',

            'our_packages_text' => 'nullable',
            'package' => 'nullable',

            'feature' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $r = Homepage::first();

        // Update About
        if ($request->file('about_image_1') != null) {
            $image = $request->file('about_image_1');
            $uploadPath = 'uploads/homepage';
            $r->about_image_1 = $image->store($uploadPath, 'public');
        }

        $r->about_title = $request->about_title ? $request->about_title : '';
        $r->about_description = $request->about_description ? $request->about_description : '';
        $r->about_button_text = $request->about_button_text ? $request->about_button_text : '';
        $r->about_button_url = $request->about_button_url ? $request->about_button_url : '';

        // Update Contact
        if ($request->file('contact_image_1') != null) {
            $image = $request->file('contact_image_1');
            $uploadPath = 'uploads/homepage';
            $r->contact_image_1 = $image->store($uploadPath, 'public');
        }

        $r->contact_title = $request->contact_title ? $request->contact_title : '';
        $r->contact_description = $request->contact_description ? $request->contact_description : '';

        // Update Intro App
        if ($request->file('intro_app_image_1') != null) {
            $image = $request->file('intro_app_image_1');
            $uploadPath = 'uploads/homepage';
            $r->intro_app_image_1 = $image->store($uploadPath, 'public');
        }
        if ($request->file('intro_app_image_2') != null) {
            $image = $request->file('intro_app_image_2');
            $uploadPath = 'uploads/homepage';
            $r->intro_app_image_2 = $image->store($uploadPath, 'public');
        }
        if ($request->file('intro_app_image_3') != null) {
            $image = $request->file('intro_app_image_3');
            $uploadPath = 'uploads/homepage';
            $r->intro_app_image_3 = $image->store($uploadPath, 'public');
        }

        $r->intro_app_title = $request->intro_app_title ? $request->intro_app_title : '';
        $r->intro_app_description = $request->intro_app_description ? $request->intro_app_description : '';
        $r->intro_app_button_text = $request->intro_app_button_text ? $request->intro_app_button_text : '';
        $r->intro_app_button_url = $request->intro_app_button_url ? $request->intro_app_button_url : '';

        // Update slider
        $r->slider_heading_1 = $request->slider_heading_1 ? $request->slider_heading_1 : '';
        $r->slider_heading_2 = $request->slider_heading_2 ? $request->slider_heading_2 : '';
        $r->slider_heading_3 = $request->slider_heading_3 ? $request->slider_heading_3 : '';

        if ($request->file('slider_image_1') != null) {
            $image = $request->file('slider_image_1');
            $uploadPath = 'uploads/homepage';
            $r->slider_image_1 = $image->store($uploadPath, 'public');
        }

        if ($request->file('slide_image_1') != null) {
            $image = $request->file('slide_image_1');
            $uploadPath = 'uploads/homepage';
            $r->slide_image_1 = $image->store($uploadPath, 'public');
        }
        if ($request->file('slide_image_2') != null) {
            $image = $request->file('slide_image_2');
            $uploadPath = 'uploads/homepage';
            $r->slide_image_2 = $image->store($uploadPath, 'public');
        }
        if ($request->file('slide_image_3') != null) {
            $image = $request->file('slide_image_3');
            $uploadPath = 'uploads/homepage';
            $r->slide_image_3 = $image->store($uploadPath, 'public');
        }
        if ($request->file('slide_image_4') != null) {
            $image = $request->file('slide_image_4');
            $uploadPath = 'uploads/homepage';
            $r->slide_image_4 = $image->store($uploadPath, 'public');
        }
        if ($request->file('slide_image_5') != null) {
            $image = $request->file('slide_image_5');
            $uploadPath = 'uploads/homepage';
            $r->slide_image_5 = $image->store($uploadPath, 'public');
        }
        if ($request->file('slide_image_6') != null) {
            $image = $request->file('slide_image_6');
            $uploadPath = 'uploads/homepage';
            $r->slide_image_6 = $image->store($uploadPath, 'public');
        }

        // Update service ids
        if ($request->filled('service')) {
            $r->service = implode(',', $request->service);
        } else {
            $r->service = '';
        }

        // Update testimonial ids
        if ($request->filled('testimonial')) {
            $r->testimonial = implode(',', $request->testimonial);
        } else {
            $r->testimonial = '';
        }

        if ($request->filled('our_clients_text')) {
            $r->our_clients_text = $request->our_clients_text;
        } else {
            $r->our_clients_text = '';
        }

        if ($request->filled('our_packages_text')) {
            $r->our_packages_text = $request->our_packages_text;
        } else {
            $r->our_packages_text = '';
        }

        // Update testimonial ids
        if ($request->filled('package')) {
            $r->packages = implode(',', $request->package);
        } else {
            $r->packages = '';
        }

        // Update testimonial ids
        if ($request->filled('feature')) {
            $r->features = implode(',', $request->feature);
        } else {
            $r->features = '';
        }

        $r->save();

        session()->flash('message', 'Homepage data updated successfully.');

        return to_route('admin.manage-homepage');
    }

    public function upload_photo(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $image = $request->file('photo');
            $uploadPath = 'uploads/homepage';
            $client_image = $image->store($uploadPath, 'public');

            $r = Homepage::first();

            if ($r->our_clients_image == '') {
                $r->our_clients_image = $client_image;
            } else {
                $temp = explode(',', $r->our_clients_image);
                $temp[] = $client_image;
                $r->our_clients_image = implode(',', $temp);
            }

            $r->save();

            // Create html
            $r = Homepage::first();
            $arr = $r->our_clients_image;
            $arr = explode(',', $arr);
            $arr = array_reverse($arr);
            $html = '';

            foreach ($arr as $url) {
                $html .= '
                    <div class="our-clients-image-container m-3">
                        <i class="fa fa-times" aria-hidden="true" onclick="deleteClientImage(this, \''.$url.'\')"></i>
                        <img src="'.asset(Storage::url($url)).'" alt="" class="thumb-2 shadow-lg">
                    </div>
                ';
            }
        }

        return [
            'status' => 'true',
            'html' => $html,
        ];
    }

    public function delete_our_clients_image(Request $request)
    {
        $url = $request->url;

        $r = Homepage::first();
        $str = $r->our_clients_image;

        $arr = explode(',', $str);
        $arr = array_diff($arr, [$url]);
        $arr = array_values($arr);
        $updatedStr = implode(',', $arr);

        $r->our_clients_image = $updatedStr;
        $r->save();

        Storage::disk('public')->delete($url);

        echo 'done';
        exit;
    }
}
