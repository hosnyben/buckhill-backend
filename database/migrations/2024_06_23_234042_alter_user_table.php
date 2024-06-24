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
            $table->string('uuid', 36)->unique()->after('id');
            $table->string('last_name')->after('first_name');
            $table->boolean('is_admin')->default(false)->after('last_name');
            $table->string('avatar', 36)->nullable()->constrained('files', 'uuid')->after('password');
            $table->string('address')->after('avatar');
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
            $table->dropColumn('avatar');
            $table->dropColumn('address');
            $table->dropColumn('phone_number');
            $table->dropColumn('is_marketing');
            $table->dropColumn('last_login_at');
            $table->string('remember_token', 100)->nullable()->after('password');
        });
    }
};
