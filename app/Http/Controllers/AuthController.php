<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseBuilder;
use App\Http\Requests\AdminLoginRequest;
use App\Http\Requests\AlumniLoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    function loginAdmin(AdminLoginRequest $request): JsonResponse
    {

        $isSuccessLogin = Auth::guard('admin')->attempt($request->validated());

        if (!$isSuccessLogin) {
            return ResponseBuilder::fail()
                ->message('Gagal login, pastikan data user valid')
                ->build();
        }
        $user = $request->user('admin');
        $user->tokens()->delete();

        $data = [
            'token' => $user->createToken("auth_token")->plainTextToken,
            'user' => $user
        ];

        return ResponseBuilder::success()
            ->data($data)
            ->message('Sukses login')
            ->build();
    }
    function loginAlumni(AlumniLoginRequest $request)
    {
        $isSuccessLogin = Auth::guard('alumni')->attempt($request->validated());

        if (!$isSuccessLogin) {
            return ResponseBuilder::fail()
                ->message('gagal login, pastikan data user valid')
                ->build();
        }

        $user = $request->user('alumni');
        $user->tokens()->delete();

        $data = [
            'token' => $user->createToken("auth_token")->plainTextToken,
            'user' => $user
        ];

        return ResponseBuilder::success()
            ->data($data)
            ->message('Sukses login')
            ->build();
    }

    /**
     * Handle web login for both alumni and admin
     */
    public function login(\Illuminate\Http\Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $loginField = $request->input('email');
        $password = $request->input('password');
        $remember = $request->filled('remember');

        // Try admin login (username)
        if (Auth::guard('admin')->attempt(['username' => $loginField, 'password' => $password], $remember)) {
            $request->session()->regenerate();
            return redirect()->intended('/admin/dashboard');
        }

        // Try alumni login (email)
        if (Auth::guard('alumni')->attempt(['email' => $loginField, 'password' => $password], $remember)) {
            $request->session()->regenerate();
            return redirect()->intended('/alumni/dashboard');
        }

        // Try company login (email)
        if (Auth::guard('company')->attempt(['email' => $loginField, 'password' => $password], $remember)) {
            $request->session()->regenerate();
            return redirect()->intended('/company/dashboard');
        }

        return back()->withErrors([
            'email' => 'Kredensial tidak valid.',
        ])->onlyInput('email');
    }

    /**
     * Handle web logout
     */
    public function logout(\Illuminate\Http\Request $request)
    {
        // Logout from all guards
        Auth::guard('admin')->logout();
        Auth::guard('alumni')->logout();
        Auth::guard('company')->logout();

        // Invalidate and regenerate session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Anda telah berhasil logout.');
    }

    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Show register form
     */
    public function showRegisterForm()
    {
        $jurusans = \App\Models\Jurusan::all();
        return view('auth.register', compact('jurusans'));
    }

    /**
     * Show company register form
     */
    public function showCompanyRegisterForm()
    {
        $jurusans = \App\Models\Jurusan::all();
        return view('auth.register-company', compact('jurusans'));
    }

    /**
     * Handle alumni registration
     */
    public function register(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:alumnis',
            'password' => 'required|string|min:8|confirmed',
            'tgl_lahir' => 'required|date',
            'tahun_mulai' => 'required|integer|min:2000|max:' . date('Y'),
            'tahun_lulus' => 'required|integer|min:2000|max:' . (date('Y') + 10),
            'no_tlp' => 'required|string|max:20',
            'alamat' => 'required|string',
            'jurusan_id' => 'required|exists:jurusans,id',
        ]);

        $alumni = \App\Models\Alumni::create([
            'nama' => $request->input('nama'),
            'email' => $request->input('email'),
            'password' => $request->input('password'), // Will be auto-hashed by model
            'tgl_lahir' => $request->input('tgl_lahir'),
            'tahun_mulai' => $request->input('tahun_mulai'),
            'tahun_lulus' => $request->input('tahun_lulus'),
            'no_tlp' => $request->input('no_tlp'),
            'alamat' => $request->input('alamat'),
            'jurusan_id' => $request->input('jurusan_id'),
        ]);

        Auth::guard('alumni')->login($alumni);

        return redirect('/home');
    }

    /**
     * Handle company registration
     */
    public function registerCompany(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:companies',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'website' => 'nullable|url',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:jurusans,id',
            'established_year' => 'nullable|integer|min:1950|max:' . date('Y'),
            'company_size' => 'nullable|in:1-10,11-50,51-100,101-500,500+',
            'contact_person' => 'required|string|max:255',
            'contact_person_phone' => 'required|string|max:20',
        ]);

        \App\Models\Company::create([
            'company_name' => $request->input('company_name'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'website' => $request->input('website'),
            'description' => $request->input('description'),
            'category_id' => $request->input('category_id'),
            'established_year' => $request->input('established_year'),
            'company_size' => $request->input('company_size'),
            'contact_person' => $request->input('contact_person'),
            'contact_person_phone' => $request->input('contact_person_phone'),
            'status' => 'pending',
        ]);

        return redirect('/login')->with('success', 'Pendaftaran berhasil! Akun Anda sedang dalam proses verifikasi.');
    }

    /**
     * Send password reset link
     */
    public function sendPasswordResetLink(\Illuminate\Http\Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = \Illuminate\Support\Facades\Password::sendResetLink(
            $request->only('email')
        );

        return $status === \Illuminate\Support\Facades\Password::RESET_LINK_SENT
                    ? back()->with(['status' => __($status)])
                    : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Reset password
     */
    public function resetPassword(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = \Illuminate\Support\Facades\Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => \Illuminate\Support\Facades\Hash::make($password)
                ])->setRememberToken(\Illuminate\Support\Str::random(60));

                $user->save();

                \Illuminate\Auth\Events\PasswordReset::dispatch($user);
            }
        );

        return $status === \Illuminate\Support\Facades\Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withErrors(['email' => [__($status)]]);
    }
}
