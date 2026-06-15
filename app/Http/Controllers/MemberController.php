<?php

namespace App\Http\Controllers;

use App\Mail\MemberInvitation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;

class MemberController extends Controller
{
    public function index()
    {
        abort_if(auth()->user()->role_id !== Role::COMPANY_ADMIN, 403);

        $users = User::where('company_id', auth()->user()->company_id)
            ->whereIn('role_id', [Role::COMPANY_ADMIN, Role::COMPANY_MEMBER])
            ->where('id', '!=', auth()->id())
            ->select(['id', 'ulid', 'name', 'email', 'role_id', 'created_at'])
            ->orderBy('role_id')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($u) => [
                'ulid'  => $u->ulid,
                'name'  => $u->name ?? '—',
                'email' => $u->email,
                'role'  => $u->role_id === Role::COMPANY_ADMIN ? 'Admin' : 'Member',
            ]);

        return Inertia::render('Members/Index', ['users' => $users]);
    }

    public function create()
    {
        abort_if(auth()->user()->role_id !== Role::COMPANY_ADMIN, 403);

        return Inertia::render('Members/Create');
    }

    public function store(Request $request)
    {
        abort_if(auth()->user()->role_id !== Role::COMPANY_ADMIN, 403);

        $request->validate([
            'email' => 'required|email|unique:users,email',
            'role'  => 'required|in:admin,member',
        ]);

        $company   = auth()->user()->company;
        $roleId    = $request->role === 'admin' ? Role::COMPANY_ADMIN : Role::COMPANY_MEMBER;
        $roleLabel = $request->role === 'admin' ? 'Admin' : 'Member';

        User::create([
            'email'      => $request->email,
            'role_id'    => $roleId,
            'company_id' => $company->id,
        ]);

        Mail::to($request->email)->send(new MemberInvitation(
            $company->company_name,
            URL::temporarySignedRoute('register', now()->addDays(7), ['invite_email' => $request->email]),
            $roleLabel
        ));

        return redirect()->route('members.index')->with([
            'status'  => 'success',
            'message' => 'Invitation sent.',
        ]);
    }
}
