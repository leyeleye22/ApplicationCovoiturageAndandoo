<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ForgetPasswordController extends Controller
{
    /**
     * Show the forget password form.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function showForgetPasswordForm()
    {
        return response()->json(['message' => 'Show forget password form']);
    }

    /**
     * Submit the forget password form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitForgetPasswordForm(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:utilisateurs',
        ]);

        $token = Str::random(64);
        $expiry = Carbon::now()->addMinutes(60);
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'expires_at' => $expiry,
            'created_at' => Carbon::now(),
        ]);

        Mail::send('MailConfirmeReset', ['token' => $token], function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Reset Password');
        });

        return response()->json(['message' => 'Email de récupération en cours!']);
    }

    /**
     * Show the reset password form.
     *
     * @param  string  $token
 
     */
    public function showResetPasswordForm($token)
    {
        return view('resetPassword', ['token' => $token]);
    }

    /**
     * Submit the reset password form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitResetPasswordForm(Request $request)
    {
        $request->validate(
            [
                'password' => 'required|regex:/^(?=.*[0-9])(?=.*[a-zA-Z])(?=.*[@#$%^&+=!])(.{8,})$/',
                'password_confirmation' => 'required|regex:/^(?=.*[0-9])(?=.*[a-zA-Z])(?=.*[@#$%^&+=!])(.{8,})$/',
            ],
            [
                'password.required' => 'Le champ mot de passe est requis.',
                'password.regex' => 'Le mot de passe doit contenir au moins 8 caractères, avec au moins une lettre, un chiffre et un caractère spécial (@#$%^&+=!).',
                'password_confirmation.required' => 'Le champ confirmation du mot de passe est requis.',
                'password_confirmation.same' => 'Le champ confirmation du mot de passe doit correspondre au mot de passe.',
            ]
        );

        $tokenData = DB::table('password_reset_tokens')
            ->where('token', $request->token)
            ->first();

        if (!$tokenData) {
            return response()->json(['error' => 'données invalides!'], 422);
        }

        if (now()->gt($tokenData->expires_at)) {
            DB::table('password_reset_tokens')
                ->where('token', $request->token)
                ->delete();
            return response()->json(['error' => 'Le token de réinitialisation de mot de passe a expiré!'], 422);
        }

        DB::table('utilisateurs')
            ->where('email', $tokenData->email)
            ->update(['password' => Hash::make($request->password)]);

        DB::table('password_reset_tokens')
            ->where('token', $request->token)
            ->delete();

        return response()->json(['message' => 'Votre mot de passe a été mis a jour']);
    }
}
