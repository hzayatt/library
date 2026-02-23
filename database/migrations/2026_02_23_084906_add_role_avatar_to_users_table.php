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
            $table->string('avatar')->nullable()->after('email');
            $table->string('google_id')->nullable()->unique()->after('avatar');
            $table->string('phone')->nullable()->after('google_id');
            $table->text('address')->nullable()->after('phone');
            $table->date('membership_expiry')->nullable()->after('address');
            $table->boolean('is_active')->default(true)->after('membership_expiry');
            $table->string('password')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'google_id', 'phone', 'address', 'membership_expiry', 'is_active']);
        });
    }
};
