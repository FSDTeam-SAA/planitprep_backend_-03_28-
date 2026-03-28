<?php

namespace App\Http\Controllers;

use App\Jobs\UserDietPlan;
use App\Models\State;
use App\Models\User;
use App\Notifications\UserNotification;
use Illuminate\Http\Request;

class ScriptController extends Controller
{
    // Get states
    public function activateMealPlans(Request $request)
    {
        $search = $request->searchTerm;
        $country_id = $request->country_id;
        if ($search == '') {
            $states = State::where('active', '1')->where('country_id', $country_id)->limit(5)->get();
        } else {
            $states = State::where('name', 'like', '%'.$search.'%')->where('country_id', $country_id)->where('active', '1')->limit(5)->get();
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

    public function testNotification()
    {
        $user_id = 11;
        $title = 'Test';
        $description = ' testing';
        $type = 'test';
        $param = ['id' => intval($user_id), 'type' => $type];
        $small_file_path = '';
        $user = User::find($user_id);
        $user->notify(new UserNotification($title, $description, $small_file_path, $param));
        echo 'success';
        exit;
    }

    public function dietPlan()
    {
        $user_id = 143;
        UserDietPlan::dispatch($user_id);
        echo 'success';
        exit;
    }
}
