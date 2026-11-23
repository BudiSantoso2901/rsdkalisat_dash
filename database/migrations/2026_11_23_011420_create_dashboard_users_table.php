<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dashboard_users', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('nama', 150);
            $table->string('username', 80)->unique();

            // Password wajib disimpan dalam bentuk hash
            $table->string('password');

            // Role utama: admin atau user
            $table->string('role', 20)->default('user');

            // Profesi khusus untuk role user
            // Nilai: perawat, bidan, dokter
            $table->string('profesi', 20)->nullable();

            // 1 = aktif, 0 = nonaktif
            $table->boolean('status')->default(true);

            $table->timestamp('last_login_at')->nullable();

            $table->timestamps();

            $table->rememberToken();

            $table->index(['role', 'profesi', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dashboard_users');
    }
};