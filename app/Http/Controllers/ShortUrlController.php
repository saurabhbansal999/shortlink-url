<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShortUrlController extends Controller
{
    private function scopedQuery()
    {
        if (auth()->user()->role_id === Role::COMPANY_ADMIN) {
            $ids = User::where('company_id', auth()->user()->company_id)->pluck('id');
            return ShortUrl::whereIn('user_id', $ids);
        }

        return ShortUrl::where('user_id', auth()->id());
    }

    public function index()
    {
        abort_if(!in_array(auth()->user()->role_id, [Role::SUPERADMIN, Role::COMPANY_ADMIN, Role::COMPANY_MEMBER]), 403);

        if (auth()->user()->role_id === Role::SUPERADMIN) {
            $urls = ShortUrl::with('user:id,company_id', 'user.company:id,company_name')
                ->orderByDesc('created_at')
                ->get()
                ->map(fn($u) => [
                    'ulid'         => $u->ulid,
                    'short_url'    => $u->short_code ? url('/s/' . $u->short_code) : null,
                    'company_name' => $u->user->company->company_name ?? '—',
                ]);

            return Inertia::render('Urls/Index', [
                'urls'        => $urls,
                'isAdmin'     => false,
                'isSuperAdmin'=> true,
            ]);
        }

        $isAdmin = auth()->user()->role_id === Role::COMPANY_ADMIN;

        $urls = $this->scopedQuery()
            ->with('user:id,name,email')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($u) => [
                'ulid'         => $u->ulid,
                'original_url' => $u->original_url,
                'short_url'    => $u->short_code ? url('/s/' . $u->short_code) : null,
                'created_by'   => $isAdmin ? ($u->user->name ?? $u->user->email) : null,
            ]);

        return Inertia::render('Urls/Index', [
            'urls'        => $urls,
            'isAdmin'     => $isAdmin,
            'isSuperAdmin'=> false,
        ]);
    }

    public function store(Request $request)
    {
        abort(403);
    }

    public function shorten(string $ulid)
    {
        abort(403);
    }

    public function generateAll()
    {
        abort(403);
    }
}
