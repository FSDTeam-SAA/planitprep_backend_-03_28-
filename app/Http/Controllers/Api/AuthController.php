<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\User;
use App\Models\UserMembership;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Get phone number and return otp.
     */
    public function authenticate(Request $request)
    {

        // this should print the `\Craftsys\Msg91\OTP\OTPService` of some default configuration values
        $validator = Validator::make($request->all(), [
            'user_input' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'msg' => $this->errorStr($validator->errors()->all()),
            ]);
        }

        $user_input = $request->user_input;

        // Delete OTP against given phone and generate a fresh OTP.
        Otp::where('user_input', $user_input)->delete();

        $otpRecord = new Otp;
        $otpRecord->user_input = $user_input;
        $otpRecord->otp = mt_rand(100000, 999999);
        $otpRecord->ttl = Carbon::now()->addMinutes(1);
        $otpRecord->save();

        // Check if user exist against given phone number.
        if (filter_var($user_input, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $user_input)->first();
            $type = 'email';
        } else {
            $user = User::where('phone', $user_input)->first();
            $type = 'mobile phone';

        }
        if (! $user) {
            // User does not exist then create user.
            $user = new User;
            if (filter_var($user_input, FILTER_VALIDATE_EMAIL)) {
                $user->email = $user_input;
            } else {
                $user->phone = $user_input;
            }

            $user->active = 1;
            $user->save();

            return response()->json([
                'status' => true,
                'msg' => 'OTP has been sent on you '.$type,
                // 'data' => $user
            ]);
        } else {
            if ($user->active == 0) {
                return response()->json([
                    'status' => false,
                    'msg' => 'Your account is Deactivated.',
                ]);
            }

            return response()->json([
                'status' => true,
                'msg' => 'OTP has been sent on you '.$type,
                // 'data' => $user,
            ]);
        }
    }

    /*public function socialLogin(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'msg' => $this->errorStr($validator->errors()->all()),
            ]);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // User does not exist then create user.
            $user = new User;
            $user->email = $request->email;
            $user->active = 1;
            $user->save();
            Auth::loginUsingId($user->id);
                    $token = $user->createToken('login_token')->plainTextToken;
                    return $this->respondWithToken($token);
            return response()->json([
                'status' => true,
                'msg' => 'OTP has been sent on you mobile phone.',
                // 'data' => $user
            ]);
        } else {
            if ($user->active == 0) {
                return response()->json([
                    'status' => false,
                    'msg' => 'Your account is Deactivated.'
                ]);
            }

            return response()->json([
                'status' => true,
                'msg' => 'OTP has been sent on you mobile phone.',
                // 'data' => $user,
            ]);
        }
    }*/

    public function socialLogin(Request $request)
    {
        // Validate required fields in the request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',  // Ensure email is valid
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'msg' => $this->errorStr($validator->errors()->all()),  // Returning validation errors
            ]);
        }

        $email = $request->email;
        $loginType = $request->login_type;
        // $functions = new Functions();
        $isRecord = false;
        $user = null;

        // If login type is "A" (Apple)
        if ($loginType == 'A') {
            // Handle user lookup based on ios_uuid
            if (isset($request->ios_uuid)) {
                $user = User::where('ios_uuid', $request->ios_uuid)->first();
                if ($user) {
                    // Update login type and continue
                    \DB::table('users')->where('id', $user->id)->update(['login_type' => $loginType]);
                    $isRecord = true;
                } elseif (isset($email)) {
                    // If user doesn't exist with ios_uuid, check by email
                    $user = User::where('email', $email)->first();
                    if ($user) {
                        // Update user with ios_uuid and login_type
                        \DB::table('users')->where('id', $user->id)->update(['ios_uuid' => $request->ios_uuid, 'login_type' => $loginType]);
                        $isRecord = true;
                    }
                }
            }
        } else {
            // For other login types, check by email
            $user = User::where('email', $email)->first();
            if ($user) {
                $isRecord = true;
            } else {
                $isRecord = false;
            }
        }

        // If the user is found or created
        if ($isRecord) {
            // If user exists, check if the account is active
            if ($user->active == 1) {
                Auth::loginUsingId($user->id);
                $accessToken = $user->createToken('login_token')->plainTextToken;

                if (! $accessToken) {
                    return response()->json(['status' => false, 'msg' => 'Error generating access token. Please try again.']);
                }
                // Update the user's last active time and other info
                /*$now = date("Y-m-d H:i:s");
                \DB::table('users')->where('id', $user->id)->update([
                    'time_zone' => $request->time_zone,
                    'updated_at' => $now
                ]);*/

                // Prepare user data for response
                return $this->respondWithToken($accessToken);
                $userContent = $this->respondWithToken($accessToken);

                return response()->json([
                    'status' => true,
                    'msg' => 'User logged in successfully',
                    'content' => $userContent,
                ]);
            } else {
                // Account is inactive
                return response()->json(['status' => false, 'msg' => 'Your account is deactivated.']);
            }
        } else {
            // User does not exist, create a new user
            $newUser = new User;
            $newUser->email = $request->email;
            $newUser->active = 1;
            $newUser->save();

            Auth::loginUsingId($newUser->id);
            // Generate access token for the newly created user
            $accessToken = $newUser->createToken('login_token')->plainTextToken;
            if (! $accessToken) {
                return response()->json(['status' => false, 'msg' => 'Error generating access token. Please try again.']);
            }

            return $this->respondWithToken($accessToken);
            // Prepare user data for response
            $userContent = $this->respondWithToken($accessToken);

            return response()->json([
                'status' => true,
                'msg' => 'User registered successfully',
                'content' => $userContent,
            ]);
        }
    }

    /**
     * Return validation errors as a formatted list.
     */
    public function errorStr($errors)
    {
        $str = '';
        $errorsCount = count($errors);

        for ($i = 0; $i < $errorsCount; $i++) {
            if ($i > 0) {
                $str .= '<br>';
            }
            $str .= $errors[$i];
        }

        return $str;
    }

    public function verify_otp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_input' => 'required',
            'otp' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'msg' => $this->errorStr($validator->errors()->all()),
            ]);
        } else {
            $user_input = $request->user_input;
            if (filter_var($user_input, FILTER_VALIDATE_EMAIL)) {
                $user = User::where('email', $user_input)->first();
                $type = 'email';
            } else {
                $user = User::where('phone', $user_input)->first();
                $type = 'mobile phone';
            }
            if ($user) {
                if ($request->otp == 123456) {
                    $user->tokens()->delete();
                    Auth::loginUsingId($user->id);
                    $token = $user->createToken('login_token')->plainTextToken;

                    return $this->respondWithToken($token);
                } else {
                    return response()->json([
                        'status' => false,
                        'msg' => 'Invalid OTP',
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'msg' => 'Invalid user!',
                ]);
            }
        }
    }

    public function refresh()
    {
        $user = Auth::user();
        $user = User::find($user->id);
        $user->tokens()->delete();
        $token = $user->createToken('login_token')->plainTextToken;

        return $this->respondWithToken($token);
    }

    protected function respondWithToken($token)
    {
        $userId = Auth::user()->id;
        $user = User::where('id', $userId)->first();
        $user->token = $token;

        if ($user->image != '') {
            $user->image = asset(Storage::url('public/users/'.$userId.'/'.$user->image));
        } else {
            $user->image = asset('assets/images/defaultUser.png');
        }
        $membership = UserMembership::where('user_id', $userId)->orderBy('id', 'desc')->first();
        $user->membership_start_date = '';
        $user->membership_end_date = '';
        $countryName = $user->country->name ?? '';
        $stateName = $user->state->name ?? '';
        $cityName = $user->city->name ?? '';
        $user->country_name = $countryName;
        $user->state_name = $stateName;
        $user->city_name = $cityName;

        if ($membership) {
            $user->membership_start_date = $membership->start_date;
            $user->membership_end_date = $membership->end_date;
            $responseObj = [
                'status' => true,
                'data' => $user,
                'membership' => true,
            ];
        } else {
            $responseObj = [
                'status' => true,
                'data' => $user,
                'membership' => false,
            ];
        }

        return response()->json($responseObj);
    }

    public function resend_otp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_input' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'msg' => $this->errorStr($validator->errors()->all()),
            ]);
        } else {
            $user_input = $request->user_input;

            Otp::where('user_input', $user_input)->delete();
            $otpRecord = new Otp;
            $otpRecord->user_input = $user_input;
            $otpRecord->otp = mt_rand(100000, 999999);
            $otpRecord->ttl = Carbon::now()->addMinutes(1);
            $otpRecord->save();

            return response()->json([
                'status' => true,
                'msg' => 'OTP Sent!',
            ]);
        }
    }
}
