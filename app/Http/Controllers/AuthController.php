<?php

namespace App\Http\Controllers;

use App\Events\UserCreated;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $http = new \GuzzleHttp\Client;

        try {
            $response = $http->post(config('services.passport.login_endpoint'), [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => config('services.passport.client_id'),
                    'client_secret' => config('services.passport.client_secret'),
                    'username' => $request->email,
                    'password' => $request->password,
                ]
            ]);
            return $response->getBody();
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            //return response($e);

            if ($e->getCode() === 400) {
                return response()->json('Invalid Request. Please enter a username or a password.', $e->getCode());
            } else if ($e->getCode() === 401) {
                return response()->json('Your credentials are incorrect. Please try again', $e->getCode());
            }
            return response()->json('Something went wrong on the server.', $e->getCode());
        }
    }

    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:4',
        ]);

        if (!!User::whereEmail($request->email)->where('confirmed', true)->first()) {
            return response('the email has already been token', 500);
        }

        User::withoutGlobalScopes()
            ->where('email', $request->email)
            ->where('confirmed', false)
            ->delete();

        $confirmation_code = Str::random(10);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'confirmation_code' => $confirmation_code,
            'confirmed' => false
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file')->storeAs('profilesImage', 'user' . $user->id . '.png');
            $user->image()->create(['url' => 'storage/' . $file]);
        }

        event(new UserCreated($user));

        return $user;
    }

    public function confirmEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'confirm_code' => 'required|string'
        ]);

        $user = User::withoutGlobalScopes()
            ->where([
                ['email', $request->email],
                ['confirmed', false]
            ])->first();
        if (!$user) {
            return response(['message' => 'email not found'], 404);
        }
        if ($user->confirmation_code !== $request->confirm_code) {
            return response(['message' => 'confirmation code is not correct'], 500);
        }

        $user->update(['confirmed' => true]);
        return response(['message' => 'verification complete'], 200);
    }


    public function logout()
    {
        auth()->user()->tokens->each(function ($token, $key) {
            $token->delete();
        });

        return response()->json('Logged out successfully', 200);
    }
}
