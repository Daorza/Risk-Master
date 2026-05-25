<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Alternative;

class AdminAndAlternativesSeeder extends Seeder
{
    public function run(): void
    {
        // ── Buat admin default ────────────────────────────────────────────────
        $admin = User::firstOrCreate(
            ['email' => 'admin@riskmaster.id'],
            [
                'name'     => 'Administrator',
                'password' => Hash::make('password'), // Ganti setelah deploy!
                'role'     => 'admin',
            ]
        );

        $this->command->info('✓ Admin user: admin@riskmaster.id (password: password)');
        $this->command->warn('  ⚠ Segera ganti password admin setelah pertama login!');

        // ── Seed alternatif template dari admin ───────────────────────────────
        $alternatives = [
            [
                'name'        => 'Implementasi Web Application Firewall (WAF)',
                'description' => 'Memasang WAF untuk memfilter dan memantau lalu lintas HTTP/HTTPS '
                    . 'masuk ke aplikasi web. Melindungi dari serangan OWASP Top 10 seperti SQL '
                    . 'injection, XSS, dan CSRF. Bisa berupa solusi cloud (Cloudflare, AWS WAF) '
                    . 'atau on-premise (ModSecurity).',
            ],
            [
                'name'        => 'Penerapan Patch Management Rutin',
                'description' => 'Menetapkan kebijakan dan prosedur pembaruan patch keamanan secara '
                    . 'berkala untuk semua sistem operasi, aplikasi, dan firmware perangkat jaringan. '
                    . 'Termasuk inventarisasi aset, jadwal patching, dan pengujian sebelum deployment.',
            ],
            [
                'name'        => 'Implementasi Multi-Factor Authentication (MFA)',
                'description' => 'Menerapkan autentikasi dua atau lebih faktor untuk semua akses '
                    . 'ke sistem kritis, terutama akses VPN, panel admin, dan email. Mencegah '
                    . 'kompromi akun meski password bocor. Dapat menggunakan TOTP (Google Authenticator), '
                    . 'SMS OTP, atau hardware token.',
            ],
            [
                'name'        => 'Segmentasi Jaringan dengan VLAN',
                'description' => 'Membagi jaringan kampus menjadi segmen-segmen logis menggunakan VLAN '
                    . 'untuk membatasi lateral movement jika terjadi insiden keamanan. Segmentasi '
                    . 'minimal: jaringan mahasiswa, jaringan staf, jaringan server, dan jaringan IoT.',
            ],
            [
                'name'        => 'Implementasi SIEM (Security Information and Event Management)',
                'description' => 'Memasang sistem SIEM untuk agregasi log, deteksi anomali, dan '
                    . 'alerting real-time dari berbagai sumber (firewall, IDS, server, endpoint). '
                    . 'Memungkinkan respons insiden yang lebih cepat dan forensik digital yang lebih baik.',
            ],
            [
                'name'        => 'Sistem Backup dan Disaster Recovery',
                'description' => 'Implementasi strategi backup 3-2-1 (3 salinan, 2 media berbeda, '
                    . '1 offsite) untuk semua data kritis. Disertai prosedur dan pengujian disaster '
                    . 'recovery berkala untuk memastikan data dapat dipulihkan dalam RTO dan RPO yang ditetapkan.',
            ],
            [
                'name'        => 'Pelatihan Keamanan Siber (Security Awareness Training)',
                'description' => 'Program edukasi rutin untuk seluruh civitas akademika tentang '
                    . 'ancaman siber terkini, phishing simulation, best practice penggunaan password, '
                    . 'dan prosedur pelaporan insiden. Mencakup pelatihan awal dan refresher berkala.',
            ],
            [
                'name'        => 'Implementasi Intrusion Detection/Prevention System (IDS/IPS)',
                'description' => 'Memasang IDS/IPS di titik-titik strategis jaringan untuk mendeteksi '
                    . 'dan/atau memblokir serangan secara otomatis berdasarkan signature dan anomali '
                    . 'traffic. Dapat berupa network-based (NIDS) atau host-based (HIDS).',
            ],
        ];

        $inserted = 0;
        foreach ($alternatives as $alt) {
            Alternative::firstOrCreate(
                ['name' => $alt['name']],
                array_merge($alt, [
                    'source'     => 'admin',
                    'created_by' => $admin->id,
                ])
            );
            $inserted++;
        }

        $this->command->info("✓ {$inserted} alternatif template berhasil di-seed.");
    }
}
