<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Role extends Model
{
    const SUPERADMIN     = 1;
    const COMPANY_ADMIN  = 2;
    const COMPANY_MEMBER = 3;

    protected $fillable = ['slug', 'name'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->ulid)) {
                $model->ulid = (string) Str::ulid();
            }
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }
}
