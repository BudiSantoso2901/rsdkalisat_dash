<?php

namespace App\Console\Commands;

use App\Models\DashboardUser;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateDashboardAccount extends Command
{
    protected $signature = 'dashboard:create-account';

    protected $description = 'Membuat akun Admin atau User Dashboard SIMRS';

    public function handle(): int
    {
        $this->info('========================================');
        $this->info('     PEMBUATAN AKUN DASHBOARD SIMRS');
        $this->info('========================================');

        $role = $this->choice(
            'Pilih role akun',
            ['admin', 'user'],
            'user'
        );

        // User pertama hanya boleh dibuat setelah Admin tersedia.
        if (
            $role === 'user' &&
            !DashboardUser::where('role', 'admin')->exists()
        ) {
            $this->error('Buat akun Admin terlebih dahulu.');
            return self::FAILURE;
        }

        $nama = trim((string) $this->ask('Nama lengkap'));
        $username = Str::lower(trim((string) $this->ask('Username')));

        if ($nama === '' || $username === '') {
            $this->error('Nama dan username wajib diisi.');
            return self::FAILURE;
        }

        if (!preg_match('/^[a-z0-9._-]{4,80}$/', $username)) {
            $this->error('Username minimal 4 karakter. Gunakan huruf kecil, angka, titik, _ atau -.');
            return self::FAILURE;
        }

        if (DashboardUser::where('username', $username)->exists()) {
            $this->error('Username sudah digunakan.');
            return self::FAILURE;
        }

        // Profesi hanya berlaku untuk role User.
        $profesi = null;

        if ($role === 'user') {
            $profesi = $this->choice(
                'Pilih profesi User',
                ['perawat', 'bidan', 'dokter']
            );
        }

        $password = $this->secret('Masukkan password (minimal 10 karakter)');
        $confirmation = $this->secret('Konfirmasi password');

        if (strlen($password) <10) {
            $this->error('Password minimal 10 karakter.');
            return self::FAILURE;
        }

        if ($password !== $confirmation) {
            $this->error('Konfirmasi password tidak cocok.');
            return self::FAILURE;
        }

        // Model DashboardUser menggunakan hashed password cast.
        // Password diisi dalam bentuk asli dan otomatis di-hash.
        DashboardUser::create([
            'nama' => $nama,
            'username' => $username,
            'password' => $password,
            'role' => $role,
            'profesi' => $profesi,
            'status' => true,
        ]);

        $this->newLine();

        $this->info('AKUN BERHASIL DIBUAT');

        $this->table(
            ['Informasi', 'Data'],
            [
                ['Nama', $nama],
                ['Username', $username],
                ['Role', ucfirst($role)],
                ['Profesi', $profesi ? ucfirst($profesi) : '-'],
                ['Status', 'Aktif'],
            ]
        );

        return self::SUCCESS;
    }
}
