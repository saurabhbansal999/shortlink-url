<?php

use App\Models\Company;
use App\Models\Role;
use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

beforeEach(function () {
    DB::table('roles')->insert([
        ['id' => Role::SUPERADMIN,     'ulid' => (string) Str::ulid(), 'name' => 'Super Admin',   'slug' => 'super-admin'],
        ['id' => Role::COMPANY_ADMIN,  'ulid' => (string) Str::ulid(), 'name' => 'Company Admin',  'slug' => 'company-admin'],
        ['id' => Role::COMPANY_MEMBER, 'ulid' => (string) Str::ulid(), 'name' => 'Company Member', 'slug' => 'company-member'],
    ]);
});

// creation restrictions

test('super admin cannot create a short url', function () {
    $superAdmin = User::factory()->create(['role_id' => Role::SUPERADMIN]);

    $this->actingAs($superAdmin)
        ->post(route('urls.store'), ['url' => 'https://example.com'])
        ->assertForbidden();
});

test('company admin cannot create a short url', function () {
    $company = Company::factory()->create();
    $admin   = User::factory()->create(['role_id' => Role::COMPANY_ADMIN, 'company_id' => $company->id]);

    $this->actingAs($admin)
        ->post(route('urls.store'), ['url' => 'https://example.com'])
        ->assertForbidden();
});

test('company member cannot create a short url', function () {
    $company = Company::factory()->create();
    $member  = User::factory()->create(['role_id' => Role::COMPANY_MEMBER, 'company_id' => $company->id]);

    $this->actingAs($member)
        ->post(route('urls.store'), ['url' => 'https://example.com'])
        ->assertForbidden();
});

// listing access

test('super admin sees all short urls across every company', function () {
    $companyA = Company::factory()->create();
    $companyB = Company::factory()->create();

    $memberA = User::factory()->create(['role_id' => Role::COMPANY_MEMBER, 'company_id' => $companyA->id]);
    $memberB = User::factory()->create(['role_id' => Role::COMPANY_MEMBER, 'company_id' => $companyB->id]);

    ShortUrl::factory()->create(['user_id' => $memberA->id]);
    ShortUrl::factory()->create(['user_id' => $memberB->id]);

    $superAdmin = User::factory()->create(['role_id' => Role::SUPERADMIN]);

    $this->actingAs($superAdmin)
        ->get(route('urls.index'))
        ->assertOk()
        ->assertInertia(fn ($page) =>
            $page->component('Urls/Index')
                 ->has('urls', 2)
                 ->where('isSuperAdmin', true)
        );
});

test('company admin only sees short urls from their own company', function () {
    $companyA = Company::factory()->create();
    $companyB = Company::factory()->create();

    $admin   = User::factory()->create(['role_id' => Role::COMPANY_ADMIN,  'company_id' => $companyA->id]);
    $memberA = User::factory()->create(['role_id' => Role::COMPANY_MEMBER, 'company_id' => $companyA->id]);
    $memberB = User::factory()->create(['role_id' => Role::COMPANY_MEMBER, 'company_id' => $companyB->id]);

    ShortUrl::factory()->create(['user_id' => $memberA->id]);
    ShortUrl::factory()->create(['user_id' => $memberB->id]);

    $this->actingAs($admin)
        ->get(route('urls.index'))
        ->assertOk()
        ->assertInertia(fn ($page) =>
            $page->component('Urls/Index')
                 ->has('urls', 1)
                 ->where('isAdmin', true)
                 ->where('isSuperAdmin', false)
        );
});

test('company member only sees their own short urls', function () {
    $company = Company::factory()->create();

    $member1 = User::factory()->create(['role_id' => Role::COMPANY_MEMBER, 'company_id' => $company->id]);
    $member2 = User::factory()->create(['role_id' => Role::COMPANY_MEMBER, 'company_id' => $company->id]);

    ShortUrl::factory()->create(['user_id' => $member1->id]);
    ShortUrl::factory()->create(['user_id' => $member2->id]);

    $this->actingAs($member1)
        ->get(route('urls.index'))
        ->assertOk()
        ->assertInertia(fn ($page) =>
            $page->component('Urls/Index')
                 ->has('urls', 1)
                 ->where('isAdmin', false)
                 ->where('isSuperAdmin', false)
        );
});

// public redirect

test('a short url publicly redirects to the original url', function () {
    $company = Company::factory()->create();
    $user    = User::factory()->create(['role_id' => Role::COMPANY_MEMBER, 'company_id' => $company->id]);

    ShortUrl::factory()->create([
        'user_id'      => $user->id,
        'original_url' => 'https://original.example.com/some/path',
        'short_code'   => 'abc123',
    ]);

    $this->get(route('short.redirect', 'abc123'))
        ->assertRedirect('https://original.example.com/some/path');
});
