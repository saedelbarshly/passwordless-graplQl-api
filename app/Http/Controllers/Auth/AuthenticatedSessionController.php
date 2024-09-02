<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Mail\TestMail;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Auth\LoginRequest;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store()
    {
        request()->validate(['email' => 'required']);
        $user = User::where(['email' => request('email')])->first();

        if(!$user){
            return back()->withErrors(['email' => 'No matching account found.']);
        }

        $link = URL::temporarySignedRoute('login.token',now()->addHour(),['user' => $user->id]);

        // defer(fn() => Mail::to($user->email)->send(new TestMail($link)));

        Mail::to($user->email)->send(new TestMail($link));
        return back()->with(['status' => 'pls check your email to login']);
    }

    public function loginVieToken(User $user)
    {
        Auth::login($user);

        request()->session()->regenerate();
        
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
