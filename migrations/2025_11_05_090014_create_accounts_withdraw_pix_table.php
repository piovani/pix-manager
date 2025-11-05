<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('accounts_withdraw_pix', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('accounts_withdraw_id');
            $table->string('key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts_withdraw_pix');
    }
};
