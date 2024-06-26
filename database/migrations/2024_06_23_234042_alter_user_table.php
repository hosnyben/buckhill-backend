<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('name', 'first_name');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->uuid('uuid')->unique()->after('id')->index();
            $table->string('last_name')->after('first_name');
            $table->boolean('is_admin')->default(false)->after('last_name');
            $table->uuid('avatar_uuid')->nullable()->after('password');
            $table->string('address')->after('avatar_uuid');
            $table->string('phone_number')->after('address');
            $table->boolean('is_marketing')->default(false)->after('phone_number');
            $table->timestamp('last_login_at')->nullable();
            $table->dropColumn('remember_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('uuid');
            $table->renameColumn('first_name', 'name');
            $table->dropColumn('last_name');
            $table->dropColumn('is_admin');
            $table->dropColumn('avatar_uuid');
            $table->dropColumn('address');
            $table->dropColumn('phone_number');
            $table->dropColumn('is_marketing');
            $table->dropColumn('last_login_at');
            $table->string('remember_token', 100)->nullable()->after('password');
        });
    }
};
