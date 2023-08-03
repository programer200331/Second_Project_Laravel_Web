<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\AdminLoginEmail;
use App\Models\Admin;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password as FacadesPassword;
use Illuminate\Validation\Rules\Password;

class WebAuthController extends Controller
{
    /**
     * Method: Get
     * Show The Login Screen
     */
    public function showLogin()
    {
        return response()->view('cms.auth.login');
    }

    /**
     * Method: Post
     */
    public function login(Request $request)
    {
        $validator = Validator($request->all(), [
            'email' => 'required|string|email|exists:admins,email',
            'password' => 'required|string',
            'remember' => 'nullable|boolean',
        ]);

        if (!$validator->fails()) {
            // $credentials = ['email' => $request->input('email'), 'password' => $request->input('password')];
            if (Auth::guard('admin')->attempt($request->only(['email', 'password']), $request->input('remember'))) {
                // Mail::send();
                // Mailable::
                /* *********** */
                // $admin = Admin::where('email', '=', $request->input('email'))->first();
                // Mail::to($admin)->send(new AdminLoginEmail());
                return response()->json(['status' => true, 'message' => 'Login Successfully', 'redirect_url' => '/cms/admin/categories'], Response::HTTP_OK);
            } else {
                return response()->json(['status' => false, 'message' => 'Login failed. Please check your email and password.'], Response::HTTP_BAD_REQUEST);
            }
        } else {
            return response()->json(['status' => false, 'message' => $validator->getMessageBag()->first()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Method: Get
     * Show Verify Email Screen
     */
    public function showVerifyEmail()
    {
        return response()->view('cms.auth.verify_email');
    }

    /**
     * Method: Get
     * Send Email Verification
     */
    public function sendVerificationEmail(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();
        return redirect()->back();
    }

    /**
     * Method: Get
     * Perform Email Verify
     */
    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill();
        return redirect()->route('categories.index');
    }

    /**
     * Method: Get
     * Show Edit Password Screen
     */
    public function editPassword()
    {
        return response()->view('cms.auth.edit_password');
    }

    /**
     * Method: Put
     * Perform Update Password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required|string|current_password:admin',
            'new_password' => [
                'required', 'confirmed',
                Password::min(8) // ثمانية حروف على الأقل
                    ->letters() // تحتوي على حروف
                    ->symbols() // و رموز
                    ->numbers() // و أرقام
                    ->mixedCase() // و أحرف كبيرة وصغيرة
                    ->uncompromised(), // ليست من الكلمات الضعيفة
            ],
        ]);

        $admin = $request->user('admin');
        $admin->password = Hash::make($request->input('new_password'));
        $saved = $admin->save();
        return redirect()->back();
    }

    /**
     * Method: Get
     */
    public function logout(Request $request)
    {
        $admin = $request->user('admin');
        // $admin->logout();
        Auth::guard('admin')->logout();
        // auth('admin')->logout();
        $request->session()->invalidate();
        return redirect()->guest('/cms/admin/auth/login');
    }

    /**
     * *****************************************
     * ****** Reset Password Operations ********
     * *****************************************
     */

    /**
     * Method: Get
     * Show Forgot Password Screen
     */
    public function forgotPassword()
    {
        return response()->view('cms.auth.forgot_password');
    }

    /**
     * Method: Post
     * Send Reset Email
     */
    public function sendResetEmail(Request $request)
    {
        $validator = Validator($request->all(), [
            'email' => 'required|email|exists:admins,email',
        ]);

        if (!$validator->fails()) {
            $link = FacadesPassword::sendResetLink(['email' => $request->input('email')]);
            return $link == FacadesPassword::RESET_LINK_SENT
                ? response()->json(['status' => true, 'message' => __($link)], Response::HTTP_OK)
                : response()->json(['status' => false, 'message' => __($link)], Response::HTTP_BAD_REQUEST);
        } else {
            return response()->json(['status' => false, 'message' => $validator->getMessageBag()->first()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Method: Get
     * show Reset Password Screen
     */
    public function showResetPassword(Request $request, $token)
    {
        return response()->view('cms.auth.reset_password', ['token' => $token, 'email' => $request->input('email')]);
    }

    /**
     * Method: Post
     * Perform Reset Password
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator($request->all(), [
            'password' => [
                'required', 'string', 'confirmed',
                Password::min(8) // ثمانية حروف على الأقل
                    ->letters() // تحتوي على حروف
                    ->symbols() // و رموز
                    ->numbers() // و أرقام
                    ->mixedCase() // و أحرف كبيرة وصغيرة
                    ->uncompromised(), // ليست من الكلمات الضعيفة
            ],
            'email' => 'required|email',
            'token' => 'required'
        ]);

        if (!$validator->fails()) {
            $statusLink = FacadesPassword::reset($request->all(), function ($user, $password) {
                $user->forceFill(['password' => Hash::make($password)]);
                $user->save();
                event(new PasswordReset($user));
            });

            return $statusLink == FacadesPassword::PASSWORD_RESET
                ? response()->json(['status' => true, 'message' => __($statusLink)], Response::HTTP_OK)
                : response()->json(['status' => false, 'message' => __($statusLink)], Response::HTTP_BAD_REQUEST);
        } else {
            return response()->json(['status' => false, 'message' => $validator->getMessageBag()->first()], Response::HTTP_BAD_REQUEST);
        }
    }
}
