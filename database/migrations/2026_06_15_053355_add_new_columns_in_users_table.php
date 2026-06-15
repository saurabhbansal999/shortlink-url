<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->ulid()->after('id')->nullable();
            $table->foreignId("role_id")->after("ulid")->nullable()->comment('1 = SUPER-ADMIN , 2 = COMPANY_ADMIN , 3 = COMPANY_MEMBER');
            $table->foreignId("company_id")->after("role_id")->nullable()->comment('Company ID for the user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['ulid', 'role_id', 'company_id']);
        });
    }
};
