<?php

namespace App\Http\Controllers;

use App\Http\Controllers\SmsController;
use App\Mail\Reset;
use App\Mail\SendEmail;
use App\Mail\EmailVerification;
use App\Models\balance;
use App\Models\Campany;
use App\Models\User;
use App\Models\VenderBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    /**
     * Show the login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle user login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        // Check if the account is locked
        if ($user->locked_until && $user->locked_until > now()) {
            $minutes = ceil($user->locked_until->diffInMinutes(now()));
            return back()->withErrors([
                'email' => "Your account is locked. Please try again in {$minutes} minutes.",
            ])->onlyInput('email');
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $request->session()->regenerate();

            // Reset failed attempts and locked_until on successful login
            $user->failed_attempts = 0;
            $user->locked_until = null;
            $user->save();

            // Check email verification for customers on every login
            if ($user->role === 'customer') {
                // Generate verification code and send email
                $verificationCode = $user->generateVerificationCode();

                try {
                    Mail::to($user->email)->send(new EmailVerification($user, $verificationCode));
                } catch (\Exception $e) {
                    Log::error('Failed to send verification email: ' . $e->getMessage());
                }

                // Store user ID in session for verification instead of logging out
                $request->session()->put('verification_user_id', $user->id);
                $request->session()->put('verification_email', $user->email);

                return redirect()->route('email.verification.show')
                    ->with('email', $user->email)
                    ->with('status', 'Please verify your email address. A verification code has been sent to your email.');
            }

            // Check for MFA setup for specific roles
            if (in_array($user->role, ['admin', 'bus_campany', 'vender'])) {
                if ($user->two_factor_secret == null) {
                    return redirect()->route('two-factor.setup')->with('status', 'Please enable Two-Factor Authentication for your account.');
                }else{
                    return redirect()->route('two-factor.login')->with('status', 'Please enable Two-Factor Authentication for your account.');
                }
            }

            if ($user->role === 'bus_campany' || $user->role === 'local_bus_owner') {
                return redirect()->route('index')->with('success', 'Logged in successfully.');
            } else if ($user->role === 'admin') {
                return redirect()->route('system.index')->with('success', 'Login successful.');
            } else if ($user->role === 'vender') {
                return redirect()->route('vender.index')->with('success', 'Login successful.');
            } else if ($user->role === 'customer') {
                return redirect()->route('customer.index')->with('success', 'Login successful.');
            }

            return redirect()->route('home')->with('success', 'Logged in successfully.');
        }

        // Increment failed attempts on failed login
        $user->increment('failed_attempts');

        if ($user->failed_attempts >= 5) {
            $user->locked_until = now()->addMinutes(5); // Lock for 5 minutes
            $user->save();
            return back()->withErrors([
                'email' => 'Too many failed login attempts. Your account has been locked for 5 minutes.',
            ])->onlyInput('email');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Show the registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle user registration.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:bus_campany,admin,vender,customer'], // Fixed typo
            'campany' => ['required_if:role,bus_campany', 'string', 'max:255', 'nullable', 'min:1'],
            'payment_number' => ['required_if:role,bus_campany', 'string', 'max:255', 'nullable', 'min:1'],
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'contact' => $request->contact,
            ]);

            if ($request->role === 'bus_campany') {
                if (empty($request->campany) || empty($request->payment_number)) {
                    throw new \Exception('Company name and payment number are required for bus company role.');
                }

                $company = Campany::create([
                    'user_id' => $user->id,
                    'name' => $request->campany,
                    'payment_number' => $request->payment_number,
                ]);

                balance::create([
                    'campany_id' => $company->id,
                    'amount' => 0,
                ]);
            }

            if ($request->role === 'vender') {
                VenderBalance::create([
                    'user_id' => $user->id,
                    'amount' => 0,
                ]);
            }



            DB::commit();

            Auth::login($user);

            if ($user->role === 'bus_campany') {
                return redirect()->route('index')->with('success', 'Registration successful.');
            } else if ($user->role === 'admin') {
                return redirect()->route('system.index')->with('success', 'Registration successful.');
            } else if ($user->role === 'vender') {
                return redirect()->route('vender.index')->with('success', 'Registration successful.');
            } else if ($user->role === 'customer') {
                return redirect()->route('customer.index')->with('success', 'Registration successful.');
            }

            return redirect()->route('home')->with('success', 'Registration successful.');
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            Log::error('Registration failed: ' . $e->getMessage() . ' | Stack: ' . $e->getTraceAsString());
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                return back()->withErrors(['email' => 'This email is already registered.'])->withInput();
            }
            return back()->withErrors(['email' => 'Registration failed. Please try again.'])->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registration failed: ' . $e->getMessage() . ' | Stack: ' . $e->getTraceAsString());
            return back()->withErrors(['email' => 'Registration failed. Please try again.'])->withInput();
        }
    }

    /**
     * Handle user logout.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        //Auth::user()->two_factor_secret = null;
        //Auth::user()->two_factor_recovery_codes = null;
        Auth::user()->two_factor_confirmed_at = null;
        Auth::user()->save();

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken(); 

        return redirect('/')->with('success', 'Logged out successfully.');
    }


    public function showResetForm()
    {
        return view('auth.password_reset');
    }


    public function email(Request $request)
    {
        // Validate the email input
        $validated = $request->validate([
            'email' => 'required|email'
        ]);

        // Find user by email
        $user = User::where('email', $request->email)->first();

        // Return error if user not found
        if (!$user) {
            return back()->with('error', 'Email not found.');
        }

        // Generate a random reset code
        $code = Str::random(6);

        $user->remember_token = $code;
        $user->save();
        // Send email with reset code
        try {
            Mail::to($user->email)->send(new Reset($user));
        } catch (\Throwable $th) {
            return back()->with('error', 'fail:'.$th->getMessage());
        }

        // Return success response
        return redirect()->route('reset.otp')->with('status', 'Password reset code sent to your email.');
    }

    public function phone(Request $request)
    {
        // Validate the phone input
        $validated = $request->validate([
            'phone' => 'required|numeric' // Adjust validation rules based on your phone format
        ]);

        // Find user by phone number
        $data = User::where('contact', $request->phone)->first();

        // Return error if user not found
        if (!$data) {
            return back()->with('error','Phone number not found.');
        }

        // Standardize phone number: if it starts with '0' or is 9 digits, prepend '255'
        $phone = $data->contact;
        if (str_starts_with($phone, '0')) {
            $phone = '255' . ltrim($phone, '0');
        }

        if(strlen($phone) === 9)
        {
            $phone = '255' . $phone;
        }

        // Generate a random reset code
        $code = Str::random(6);
        $data->remember_token = $code;
        $data->save();

        // Prepare SMS message
        $message = "Dear {$data->name},Reset Code: {$code}" ;

        // Send SMS using standardized phone number
        $sms = new SmsController();
        try {
            $sms->sms_send($phone, $message);
        } catch (\Throwable $th) {
            return back()->with('error', 'fail:'.$th->getMessage());
        }

        // Return success response
        return redirect()->route('reset.otp')->with('status', 'Password reset code sent to your email.');
    }

    public function showOtpForm()
    {
        return view('auth.otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = User::where('remember_token', $request->code)->first();
        if (!$user) {
            return back()->with('error', 'Invalid code. Please try again.');
        }

        // Optionally, clear the remember_token after successful verification
        //$user->remember_token = null;
        $user->save();

        // Redirect to a password reset form, passing the user ID or a token
        return view('auth.password_reset_form', compact('user'))
            ->with('success', 'Code verified successfully. Please set a new password.');
    }

    public function showResetFormWithId(Request $request)
    {
        // Validate the user ID
        $user = User::find($request->id);
        if (!$user) {
            return redirect()->route('reset.form')->withErrors(['user' => 'User not found.']);
        }
        //validate password
        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        $user->password = Hash::make($request->password);
        $user->remember_token = null; // Clear the reset token after use
        $user->save();

        return redirect()->route('login')->with('success', 'Password reset successfully. You can now log in with your new password.');
    }

    /**
     * Show email verification form
     */
    public function showEmailVerificationForm()
    {
        return view('auth.email-verification');
    }

    /**
     * Verify email with code
     */
    public function verifyEmail(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|string|size:6',
        ]);

        // Get user ID from session
        $userId = $request->session()->get('verification_user_id');
        
        if (!$userId) {
            return redirect()->route('login')->withErrors(['email' => 'Verification session expired. Please login again.']);
        }

        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('login')->withErrors(['email' => 'User not found.']);
        }

        if (!$user->isVerificationCodeValid($request->verification_code)) {
            return back()->withErrors(['verification_code' => 'Invalid or expired verification code.'])->withInput();
        }

        // Mark email as verified
        $user->markEmailAsVerified();

        // Clear verification session data
        $request->session()->forget(['verification_user_id', 'verification_email']);

        // Log the user in
        Auth::login($user);
        $request->session()->regenerate();

        // Redirect based on user role
        if ($user->role === 'bus_campany' || $user->role === 'local_bus_owner') {
            return redirect()->route('index')->with('success', 'Email verified successfully. You are now logged in.');
        } else if ($user->role === 'admin') {
            return redirect()->route('system.index')->with('success', 'Email verified successfully. You are now logged in.');
        } else if ($user->role === 'vender') {
            return redirect()->route('vender.index')->with('success', 'Email verified successfully. You are now logged in.');
        } else if ($user->role === 'customer') {
            return redirect()->route('customer.index')->with('success', 'Email verified successfully. You are now logged in.');
        }

        return redirect()->route('home')->with('success', 'Email verified successfully. You are now logged in.');
    }

    /**
     * Resend verification code
     */
    public function resendVerificationCode(Request $request)
    {
        // Get user ID from session
        $userId = $request->session()->get('verification_user_id');
        
        if (!$userId) {
            return redirect()->route('login')->withErrors(['email' => 'Verification session expired. Please login again.']);
        }

        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('login')->withErrors(['email' => 'User not found.']);
        }

        // Generate new verification code
        $verificationCode = $user->generateVerificationCode();

        try {
            Mail::to($user->email)->send(new EmailVerification($user, $verificationCode));
            return back()
                ->with('email', $user->email)
                ->with('status', 'A new verification code has been sent to your email.');
        } catch (\Exception $e) {
            Log::error('Failed to resend verification email: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Failed to send verification email. Please try again.'])->withInput();
        }
    }
}
