<?php

namespace App\Http\Controllers;

use App\Notifications\PasswordResetRequest;
use App\Notifications\PasswordResetSuccess;
use App\PasswordReset;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    //
    public function create(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email'
        ]);
        $user = User::where('email', $request->email)->first();

        if (!$user)
            return response()->json([
                'message' => 'We can\'t find a user with that e-mail address.'
            ], 404);

        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => Str::random(60)
            ]
        );

        if ($user && $passwordReset)
            $user->notify(
                new PasswordResetRequest($passwordReset->token)
            );

        return response()->json([
            'message' => 'We have e-mailed your password reset link!expired after 12 hours'
        ]);
    }

    /**
     * Find token password reset
     *
     * @param  [string] $token
     * @return [string] message
     * @return [json] passwordReset object
     */
    public function find($token)
    {
        $passwordReset = PasswordReset::where('token', $token)->first();
        if (!$passwordReset)
            abort(404);

        if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
            $passwordReset->delete();
            abort(404);
        }

        return view('reset_password')->with(['token' => $token]);
    }

    /**
     * Reset password
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @param  [string] token
     * @return [string] message
     * @return [json] user object
     */
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|confirmed',
            'password_token' => 'required|string'
        ]);

        $passwordReset = PasswordReset::where([
            ['token', $request->password_token],
            ['email', $request->email]
        ])->first();

        if (!$passwordReset)
            return back()->with(['error_message' => 'This password reset token is invalid.'])
                ->withInput();

        $user = User::where('email', $passwordReset->email)->first();

        if (!$user)
            return back()->with(['error_message' => 'We can\'t find a user with that e - mail address . '])
                ->withInput();

        $user->password = bcrypt($request->password);
        $user->save();
        $passwordReset->delete();
        $user->notify(new PasswordResetSuccess());
        return redirect(route('home') . '/login');
    }

}
