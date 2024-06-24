<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Rename the password_reset_tokens table to password_resets
        Schema::rename('password_reset_tokens', 'password_resets');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('password_resets', 'password_reset_tokens');
    }
};
