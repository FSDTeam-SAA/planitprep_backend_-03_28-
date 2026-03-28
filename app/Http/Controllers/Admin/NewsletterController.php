<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Newsletter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewsletterController extends Controller
{
    public function index()
    {
        $title = 'Newsletter';

        $newsletter = Newsletter::first();

        return view('admin.newsletter', compact('newsletter', 'title'));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $r = Newsletter::first();
        $r->description = $request->description;
        $r->save();

        session()->flash('message', 'Newsletter saved successfully.');

        return to_route('admin.newsletter');
    }
}
