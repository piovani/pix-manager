<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;
use Ramsey\Uuid\Nonstandard\Uuid;
use Hyperf\DbConnection\Db;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tableName = 'accounts';

        Schema::create($tableName, function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->decimal('balance',10,2)->default(0);
        });

        DB::table($tableName)->insert([
            'id' => Uuid::uuid4()->toString(),
            'name' => 'Usuario Padrao',
            'balance' => 1000.00,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
