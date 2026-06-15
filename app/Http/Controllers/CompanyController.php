<?php

namespace App\Http\Controllers;

use App\Mail\CompanyInvitation;
use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;

class CompanyController extends Controller
{
    public function index()
    {
        abort_if(auth()->user()->role_id !== Role::SUPERADMIN, 403);

        $companies = Company::select(['id', 'ulid', 'company_name', 'company_email', 'company_phone'])->get();

        return Inertia::render('Companies/Index', ['companies' => $companies]);
    }

    public function create()
    {
        abort_if(auth()->user()->role_id !== Role::SUPERADMIN, 403);

        return Inertia::render('Companies/Create');
    }

    public function store(Request $request)
    {
        abort_if(auth()->user()->role_id !== Role::SUPERADMIN, 403);

        $request->validate([
            'name'  => 'required|string',
            'email' => 'required|email|unique:companies,company_email|unique:users,email',
            'phone' => 'nullable|numeric|unique:companies,company_phone',
        ]);

        DB::transaction(function () use ($request) {
            $company = Company::create([
                'company_name'  => $request->name,
                'company_email' => $request->email,
                'company_phone' => $request->phone,
            ]);

            User::create([
                'email'      => $request->email,
                'role_id'    => Role::COMPANY_ADMIN,
                'company_id' => $company->id,
            ]);

            Mail::to($request->email)->send(new CompanyInvitation(
                $request->name,
                URL::temporarySignedRoute('register', now()->addDays(7), ['invite_email' => $request->email])
            ));
        });

        return redirect()->route('companies.index')->with([
            'status'  => 'success',
            'message' => 'Company created and invitation sent.',
        ]);
    }
}
