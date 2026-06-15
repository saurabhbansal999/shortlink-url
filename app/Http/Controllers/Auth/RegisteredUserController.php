<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    public function create(Request $request): Response|RedirectResponse
    {
        $inviteEmail = $request->query('invite_email');

        if ($inviteEmail) {
            if (!$request->hasValidSignature()) {
                return Inertia::render('Auth/Register', ['linkExpired' => true]);
            }

            // Persist the invite email in session so it survives validation-error redirects
            session(['pending_invite' => $inviteEmail]);
        } else {
            $inviteEmail = session('pending_invite');
        }

        if (!$inviteEmail) {
            return Inertia::render('Auth/Register');
        }

        $user = User::where('email', $inviteEmail)->with('company')->first();

        if ($user?->email_verified_at) {
            session()->forget('pending_invite');
            return redirect()->route('login')
                ->with('status', 'This invitation link has already been used. Please log in.');
        }

        $needsCompanySetup = $user?->role_id === Role::COMPANY_ADMIN
            && $user->company?->users()
                ->where('role_id', Role::COMPANY_ADMIN)
                ->whereNotNull('email_verified_at')
                ->doesntExist();

        return Inertia::render('Auth/Register', [
            'invite'            => true,
            'inviteEmail'       => $inviteEmail,
            'companyName'       => $user?->company?->company_name,
            'companyEmail'      => $user?->company?->company_email,
            'companyPhone'      => $user?->company?->company_phone,
            'showCompanyFields' => $needsCompanySetup,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        // Accept invite_email from the form field or session (set when the link was first opened)
        $inviteEmail = $request->input('invite_email') ?: session('pending_invite');

        if (!$inviteEmail) {
            $request->validate([
                'name'     => 'required|string|max:255',
                'email'    => 'required|string|lowercase|email|max:255|unique:' . User::class,
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => $request->password,
            ]);

            event(new Registered($user));
            Auth::login($user);

            return redirect(route('dashboard', absolute: false));
        }

        $user = User::where('email', $inviteEmail)->with('company')->first();

        if (!$user) {
            session()->forget('pending_invite');
            throw ValidationException::withMessages(['invite_email' => 'Invalid invitation.']);
        }

        if ($user->email_verified_at) {
            session()->forget('pending_invite');
            throw ValidationException::withMessages(['invite_email' => 'This invitation has already been used.']);
        }

        $needsCompanySetup = $user->role_id === Role::COMPANY_ADMIN
            && $user->company?->users()
                ->where('role_id', Role::COMPANY_ADMIN)
                ->whereNotNull('email_verified_at')
                ->doesntExist();

        if ($needsCompanySetup) {
            $request->validate([
                'company_name'  => 'required|string|max:255',
                'company_email' => 'required|email|unique:companies,company_email,' . $user->company->id,
                'company_phone' => 'nullable|string|max:20|unique:companies,company_phone,' . $user->company->id,
                'name'          => 'required|string|max:255',
                'email'         => 'required|string|lowercase|email|max:255|unique:users,email,' . $user->id,
                'password'      => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            DB::transaction(function () use ($request, $user) {
                $user->company->update([
                    'company_name'  => $request->company_name,
                    'company_email' => $request->company_email,
                    'company_phone' => $request->company_phone,
                ]);

                $user->update([
                    'name'              => $request->name,
                    'email'             => $request->email,
                    'password'          => $request->password,
                    'email_verified_at' => now(),
                ]);
            });
        } else {
            $request->validate([
                'name'     => 'required|string|max:255',
                'email'    => 'required|string|lowercase|email|max:255|unique:users,email,' . $user->id,
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            $user->update([
                'name'              => $request->name,
                'email'             => $request->email,
                'password'          => $request->password,
                'email_verified_at' => now(),
            ]);
        }

        session()->forget('pending_invite');
        event(new Registered($user->fresh()));
        Auth::login($user->fresh());

        return redirect(route('dashboard', absolute: false));
    }
}
