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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    public function create(Request $request): Response|RedirectResponse
    {
        if (!$request->hasValidSignature()) {
            return Inertia::render('Auth/Register');
        }

        $inviteEmail = $request->query('invite_email');
        $user        = User::where('email', $inviteEmail)->with('company')->first();

        if ($user?->email_verified_at) {
            return redirect()->route('login')
                ->with('status', 'This invitation link has already been used. Please log in.');
        }

        $needsCompanySetup = $user?->role_id === Role::COMPANY_ADMIN
            && $user->company->users()
                ->where('role_id', Role::COMPANY_ADMIN)
                ->whereNotNull('email_verified_at')
                ->doesntExist();

        return Inertia::render('Auth/Register', [
            'invite'            => true,
            'inviteEmail'       => $inviteEmail,
            'companyName'       => $user?->company?->company_name,
            'companyEmail'      => $user?->company?->company_email,
            'companyPhone'      => $user?->company?->company_phone,
            'inviteUrl'         => $request->fullUrl(),
            'showCompanyFields' => $needsCompanySetup,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $inviteUrl = $request->input('invite_url');
        $isInvite  = $inviteUrl && URL::hasValidSignature(Request::create($inviteUrl));

        if (!$isInvite) {
            $request->validate([
                'name'     => 'required|string|max:255',
                'email'    => 'required|string|lowercase|email|max:255|unique:' . User::class,
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);

            event(new Registered($user));
            Auth::login($user);

            return redirect(route('dashboard', absolute: false));
        }

        $user = User::where('email', $request->input('invite_email'))->with('company')->first();

        if (!$user) {
            throw ValidationException::withMessages(['invite_email' => 'Invalid invitation.']);
        }

        if ($user->email_verified_at) {
            throw ValidationException::withMessages(['invite_email' => 'This invitation has already been used.']);
        }

        $needsCompanySetup = $user->role_id === Role::COMPANY_ADMIN
            && $user->company->users()
                ->where('role_id', Role::COMPANY_ADMIN)
                ->whereNotNull('email_verified_at')
                ->doesntExist();

        if ($needsCompanySetup) {
            $request->validate([
                'company_name'  => 'required|string|max:255',
                'company_email' => 'required|email|unique:companies,company_email,' . $user->company->id,
                'company_phone' => 'nullable|numeric|unique:companies,company_phone,' . $user->company->id,
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
                    'password'          => Hash::make($request->password),
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
                'password'          => Hash::make($request->password),
                'email_verified_at' => now(),
            ]);
        }

        event(new Registered($user->fresh()));
        Auth::login($user->fresh());

        return redirect(route('dashboard', absolute: false));
    }
}
