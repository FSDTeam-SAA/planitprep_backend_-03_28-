<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatgptAiSetting;
use App\Models\LoginSetting;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $title = 'Settings';
        $settings = Setting::first();

        if ($settings->logo) {
            if (Storage::exists($settings->logo)) {
                $settings->logo = asset(Storage::url($settings->logo));
            } else {
                $settings->logo = asset('admin/images/noimg.jpg');
            }
        } else {
            $settings->logo = asset('admin/images/noimg.jpg');
        }

        if ($settings->mobile_logo) {
            if (Storage::exists($settings->mobile_logo)) {
                $settings->mobile_logo = asset(Storage::url($settings->mobile_logo));
            } else {
                $settings->mobile_logo = asset('admin/images/noimg.jpg');
            }
        } else {
            $settings->mobile_logo = asset('admin/images/noimg.jpg');
        }

        if ($settings->apple_store_image) {
            if (Storage::exists($settings->apple_store_image)) {
                $settings->apple_store_image = asset(Storage::url($settings->apple_store_image));
            }
        }

        if ($settings->android_store_image) {
            if (Storage::exists($settings->android_store_image)) {
                $settings->android_store_image = asset(Storage::url($settings->android_store_image));
            }
        }

        return view('admin.settings.settings', compact('title', 'settings'));
    }

    public function update(Request $request)
    {
        $rules = [
            'logo' => 'nullable|file|max:2048|mimes:jpg,jpeg,png,webp',
            'mobile_logo' => 'nullable|file|max:2048|mimes:jpg,jpeg,png,webp',
            'address' => 'nullable|string|max:250',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:50',
            'facebook' => 'nullable|string|max:250',
            'instagram' => 'nullable|string|max:250',
            'x' => 'nullable|string|max:250',
            'youtube' => 'nullable|string|max:250',
            'marquee' => 'nullable|string|max:250',
            'contact_no' => 'nullable|string|max:20',
            'apple_store_image' => 'nullable|file|max:2048|mimes:jpg,jpeg,png,webp',
            'apple_store_link' => 'nullable|string|max:250',
            'android_store_image' => 'nullable|file|max:2048|mimes:jpg,jpeg,png,webp',
            'android_store_link' => 'nullable|string|max:250',
            'free_upto' => 'required_if:enable_free_membership,1',
        ];

        $request->validate($rules, [
            'free_upto.required_if' => 'The free expiry date is required when Free membership is enabled.',
        ]);

        $r = Setting::first();
        $r->address = $request->address ? $request->address : '';
        $r->phone = $request->phone ? $request->phone : '';
        $r->email = $request->email ? $request->email : '';
        $r->facebook = $request->facebook ? $request->facebook : '';
        $r->instagram = $request->instagram ? $request->instagram : '';
        $r->x = $request->x ? $request->x : '';
        $r->youtube = $request->youtube ? $request->youtube : '';
        $r->marquee = $request->marquee ? $request->marquee : '';
        $r->contact_no = $request->contact_no ? $request->contact_no : '';
        $r->apple_store_link = $request->apple_store_link ? $request->apple_store_link : '';
        $r->android_store_link = $request->android_store_link ? $request->android_store_link : '';
        $r->enable_free_membership = $request->enable_free_membership ? $request->enable_free_membership : 0;
        $r->free_upto = $request->free_upto ? $request->free_upto : null;

        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            $oldFile = $r->logo;

            $logo = $request->file('logo');
            $uploadPath = 'public/uploads';
            $r->logo = $logo->store($uploadPath);

            if ($oldFile && Storage::exists($oldFile)) {
                Storage::delete($oldFile);
            }
        }

        if ($request->hasFile('mobile_logo') && $request->file('mobile_logo')->isValid()) {
            $oldFile = $r->mobile_logo;

            $logo = $request->file('mobile_logo');
            $uploadPath = 'public/uploads';
            $r->mobile_logo = $logo->store($uploadPath);

            if ($oldFile && Storage::exists($oldFile)) {
                Storage::delete($oldFile);
            }
        }

        if ($request->hasFile('apple_store_image') && $request->file('apple_store_image')->isValid()) {
            $oldFile = $r->apple_store_image;

            $image = $request->file('apple_store_image');
            $uploadPath = 'public/uploads';
            $r->apple_store_image = $image->store($uploadPath);

            if ($oldFile && Storage::exists($oldFile)) {
                Storage::delete($oldFile);
            }
        }

        if ($request->hasFile('android_store_image') && $request->file('android_store_image')->isValid()) {
            $oldFile = $r->android_store_image;

            $image = $request->file('android_store_image');
            $uploadPath = 'public/uploads';
            $r->android_store_image = $image->store($uploadPath);

            if ($oldFile && Storage::exists($oldFile)) {
                Storage::delete($oldFile);
            }
        }

        $r->save();

        return redirect()->route('admin.settings')->with('success', 'Settings updated successfully.');
    }

    public function chatgptSettings()
    {
        $title = 'ChatGPT AI  Settings';
        $settings = ChatgptAiSetting::first();

        return view('admin.settings.chatgpt-settings', compact('title', 'settings'));
    }

    public function chatgptSettingsUpdate(Request $request)
    {
        $rules = [
            'api_key' => 'required',
            'model' => 'required',
        ];

        $request->validate($rules, [
            'api_key.required' => 'API Key is required.',
            'model.required' => 'Model is required.',
        ]);

        $r = ChatgptAiSetting::first();
        $r->api_key = $request->api_key ? $request->api_key : '';
        $r->model = $request->model ? $request->model : '';
        $r->save();

        $env_values = array(
            "CHAT_GPT_API_KEY"=> $request->api_key ? $request->api_key : '',
            "CHAT_GPT_MODEL" => $request->model ? $request->model : ''
        );

        $this->setEnvironmentValue($env_values);

        return redirect()->route('admin.chatgptSettings')->with('success', 'Settings updated successfully.');
    }

    public function setEnvironmentValue(array $values)
    {
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);
        if (count($values) > 0) {
            foreach ($values as $envKey => $envValue) {
                $env_str = explode("\n", $str); // In case the searched variable is in the last line without \n
                foreach ($env_str as $k => $v) {
                    $i = 0;
                    $env_key = explode('=', $v);
                    if (strcmp($env_key[0], $envKey) == 0) {
                        $env_str[$k] = $envKey . '=' . $envValue;
                        $i = 0;
                        break;
                    } else {
                        $i = 1;
                    }
                }
                if ($i == 1) {
                    array_push($env_str, $envKey . '=' . $envValue);
                }
                $str = implode("\n", $env_str);
            }
        }
        if (!file_put_contents($envFile, $str)) return false;
        return true;
    }

    public function loginSettings()
    {
        $title = 'Login Settings';
        $settings = LoginSetting::first();

        return view('admin.settings.login-settings', compact('title', 'settings'));
    }

    public function loginSettingsUpdate(Request $request)
    {

        $r = LoginSetting::first();
        if (! $r) {
            $r = new LoginSetting;
        }
        $r->email = $request->email ? $request->email : false;
        $r->phone = $request->phone ? $request->phone : false;
        $r->facebook = $request->facebook ? $request->facebook : false;
        $r->google = $request->google ? $request->google : false;
        $r->apple = $request->apple ? $request->apple : false;
        $r->save();

        return redirect()->route('admin.loginSettings')->with('success', 'Settings updated successfully.');
    }
}
