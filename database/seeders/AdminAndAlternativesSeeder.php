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
                'name' => 'Implementasi Web Application Firewall (WAF)',
                'description' => 'Web Application Firewall (WAF) merupakan mekanisme perlindungan yang '
                    . 'ditempatkan di antara pengguna dan aplikasi web untuk memantau, memfilter, serta '
                    . 'mengendalikan lalu lintas HTTP dan HTTPS yang masuk maupun keluar. Solusi ini '
                    . 'berfungsi sebagai lapisan pertahanan tambahan terhadap berbagai ancaman aplikasi '
                    . 'web seperti SQL Injection, Cross-Site Scripting (XSS), Cross-Site Request Forgery '
                    . '(CSRF), Local File Inclusion (LFI), Remote File Inclusion (RFI), dan berbagai '
                    . 'serangan lain yang termasuk dalam OWASP Top 10. Implementasi dapat dilakukan '
                    . 'menggunakan layanan cloud seperti Cloudflare WAF dan AWS WAF maupun solusi '
                    . 'on-premise seperti ModSecurity. Penerapan WAF membantu organisasi mengurangi '
                    . 'risiko eksploitasi aplikasi web tanpa harus melakukan perubahan besar pada kode '
                    . 'sumber aplikasi yang sudah berjalan.',
            ],

            [
                'name' => 'Penerapan Patch Management Rutin',
                'description' => 'Patch Management merupakan proses terstruktur untuk mengidentifikasi, '
                    . 'menguji, mendistribusikan, dan memasang pembaruan keamanan pada sistem operasi, '
                    . 'aplikasi, firmware, database, serta perangkat jaringan. Tujuan utama dari '
                    . 'kegiatan ini adalah menutup kerentanan keamanan yang telah diketahui sebelum '
                    . 'dimanfaatkan oleh pihak yang tidak berwenang. Implementasi mencakup inventarisasi '
                    . 'aset TI, pemantauan pembaruan dari vendor, penjadwalan patch berkala, pengujian '
                    . 'kompatibilitas, hingga verifikasi pasca implementasi. Strategi ini termasuk '
                    . 'salah satu kontrol keamanan paling efektif karena sebagian besar serangan siber '
                    . 'modern memanfaatkan celah keamanan yang sebenarnya telah tersedia patch-nya.',
            ],

            [
                'name' => 'Implementasi Multi-Factor Authentication (MFA)',
                'description' => 'Multi-Factor Authentication (MFA) merupakan metode autentikasi yang '
                    . 'mengharuskan pengguna melakukan verifikasi identitas melalui dua atau lebih faktor '
                    . 'yang berbeda, seperti sesuatu yang diketahui (password), sesuatu yang dimiliki '
                    . '(OTP, aplikasi autentikator, atau token fisik), dan sesuatu yang melekat pada '
                    . 'pengguna (biometrik). Implementasi MFA dapat diterapkan pada email institusi, '
                    . 'portal akademik, VPN, server, aplikasi cloud, maupun akun administrator. '
                    . 'Penerapan MFA secara signifikan mengurangi risiko kompromi akun akibat pencurian '
                    . 'password, phishing, credential stuffing, atau kebocoran data kredensial.',
            ],

            [
                'name' => 'Segmentasi Jaringan dengan VLAN',
                'description' => 'Virtual Local Area Network (VLAN) digunakan untuk membagi jaringan '
                    . 'komputer menjadi beberapa segmen logis yang terpisah meskipun menggunakan '
                    . 'infrastruktur fisik yang sama. Segmentasi memungkinkan pembatasan komunikasi '
                    . 'antar kelompok perangkat sehingga mengurangi risiko penyebaran serangan secara '
                    . 'horizontal (lateral movement) ketika terjadi kompromi pada salah satu segmen. '
                    . 'Dalam lingkungan institusi pendidikan, segmentasi dapat diterapkan pada jaringan '
                    . 'mahasiswa, staf, server, laboratorium, perangkat IoT, serta jaringan tamu. '
                    . 'Selain meningkatkan keamanan, pendekatan ini juga membantu pengelolaan lalu lintas '
                    . 'jaringan dan meningkatkan performa operasional.',
            ],

            [
                'name' => 'Implementasi SIEM (Security Information and Event Management)',
                'description' => 'Security Information and Event Management (SIEM) merupakan platform yang '
                    . 'mengumpulkan, menyimpan, menganalisis, dan mengkorelasikan log keamanan dari '
                    . 'berbagai sumber seperti firewall, server, endpoint, aplikasi, sistem autentikasi, '
                    . 'IDS/IPS, dan perangkat jaringan lainnya. SIEM memungkinkan deteksi ancaman secara '
                    . 'real-time melalui aturan korelasi, analisis perilaku, serta pemberian notifikasi '
                    . 'otomatis terhadap aktivitas mencurigakan. Selain mendukung respons insiden yang '
                    . 'lebih cepat, SIEM juga membantu kebutuhan audit keamanan, investigasi forensik '
                    . 'digital, dan pemenuhan regulasi keamanan informasi.',
            ],

            [
                'name' => 'Sistem Backup dan Disaster Recovery',
                'description' => 'Backup dan Disaster Recovery merupakan strategi untuk menjamin '
                    . 'ketersediaan data dan keberlangsungan layanan ketika terjadi insiden seperti '
                    . 'serangan ransomware, kegagalan perangkat keras, kesalahan manusia, kebakaran, '
                    . 'atau bencana lainnya. Implementasi dapat mengikuti prinsip 3-2-1, yaitu memiliki '
                    . 'tiga salinan data, menggunakan dua jenis media penyimpanan yang berbeda, dan '
                    . 'menyimpan satu salinan di lokasi terpisah. Selain pencadangan data, strategi ini '
                    . 'mencakup penyusunan prosedur pemulihan, penentuan Recovery Time Objective (RTO), '
                    . 'Recovery Point Objective (RPO), serta pengujian pemulihan secara berkala untuk '
                    . 'memastikan data dapat dikembalikan ketika dibutuhkan.',
            ],

            [
                'name' => 'Pelatihan Keamanan Siber (Security Awareness Training)',
                'description' => 'Security Awareness Training merupakan program edukasi yang bertujuan '
                    . 'meningkatkan kesadaran dan perilaku keamanan seluruh pengguna sistem informasi. '
                    . 'Materi pelatihan dapat mencakup pengenalan phishing, social engineering, '
                    . 'pengelolaan kata sandi yang aman, penggunaan perangkat pribadi, perlindungan data, '
                    . 'serta prosedur pelaporan insiden keamanan. Program biasanya dilengkapi dengan '
                    . 'simulasi phishing dan evaluasi berkala untuk mengukur tingkat pemahaman pengguna. '
                    . 'Karena manusia sering menjadi target utama serangan siber, peningkatan kesadaran '
                    . 'pengguna dapat secara signifikan mengurangi kemungkinan keberhasilan serangan.',
            ],

            [
                'name' => 'Implementasi Intrusion Detection/Prevention System (IDS/IPS)',
                'description' => 'Intrusion Detection System (IDS) dan Intrusion Prevention System (IPS) '
                    . 'merupakan teknologi keamanan yang digunakan untuk mendeteksi maupun mencegah '
                    . 'aktivitas mencurigakan pada jaringan atau sistem. IDS berfungsi mengidentifikasi '
                    . 'indikasi serangan dan memberikan notifikasi kepada administrator, sedangkan IPS '
                    . 'dapat mengambil tindakan otomatis seperti memblokir lalu lintas berbahaya atau '
                    . 'menghentikan koneksi yang mencurigakan. Implementasi dapat dilakukan dalam bentuk '
                    . 'Network-Based IDS/IPS maupun Host-Based IDS/IPS. Solusi ini membantu organisasi '
                    . 'meningkatkan kemampuan monitoring keamanan dan mempercepat respons terhadap '
                    . 'serangan yang sedang berlangsung.',
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
