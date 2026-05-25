<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $criteria = [
            [
                'name'        => 'Efektivitas Mitigasi',
                'description' => 'Mengukur seberapa efektif alternatif solusi dalam meredam atau '
                    . 'menghilangkan risiko keamanan yang diidentifikasi. Penilaian mencakup '
                    . 'kemampuan pencegahan, deteksi, dan respons terhadap ancaman. '
                    . 'Skala penilaian: 1 (tidak efektif) – 10 (sangat efektif).',
                'type'        => 'benefit',
                'weight'      => 0.3000, // Bobot tertinggi — efektivitas adalah faktor utama
            ],
            [
                'name'        => 'Biaya Implementasi',
                'description' => 'Total estimasi biaya yang dibutuhkan untuk mengimplementasikan '
                    . 'alternatif, termasuk biaya pengadaan perangkat/lisensi, biaya tenaga ahli, '
                    . 'biaya pelatihan, dan biaya operasional rutin. '
                    . 'Skala penilaian: 1 (sangat mahal) – 10 (sangat murah/gratis).',
                'type'        => 'cost',   // Cost: nilai tinggi = biaya rendah = lebih baik
                'weight'      => 0.2500,
            ],
            [
                'name'        => 'Kompleksitas Implementasi',
                'description' => 'Mengukur tingkat kesulitan teknis dalam mengimplementasikan '
                    . 'alternatif, termasuk kebutuhan keahlian khusus, waktu yang diperlukan, '
                    . 'dan dampak terhadap infrastruktur yang sudah ada. '
                    . 'Skala penilaian: 1 (sangat kompleks) – 10 (sangat mudah diimplementasikan).',
                'type'        => 'cost',   // Cost: nilai tinggi = kompleksitas rendah = lebih baik
                'weight'      => 0.2000,
            ],
            [
                'name'        => 'Kecepatan Respons',
                'description' => 'Seberapa cepat alternatif dapat diimplementasikan dan mulai '
                    . 'memberikan perlindungan. Termasuk waktu deployment, konfigurasi awal, '
                    . 'dan waktu hingga sistem aktif melindungi jaringan. '
                    . 'Skala penilaian: 1 (sangat lambat, > 1 bulan) – 10 (sangat cepat, < 1 hari).',
                'type'        => 'benefit',
                'weight'      => 0.1500,
            ],
            [
                'name'        => 'Kepatuhan Regulasi',
                'description' => 'Sejauh mana alternatif mendukung pemenuhan standar dan regulasi '
                    . 'keamanan yang berlaku, seperti ISO 27001, NIST Cybersecurity Framework, '
                    . 'Peraturan BSSN, atau kebijakan keamanan internal institusi. '
                    . 'Skala penilaian: 1 (tidak mendukung) – 10 (sepenuhnya memenuhi standar).',
                'type'        => 'benefit',
                'weight'      => 0.1000,
            ],
        ];

        // Gunakan upsert agar seeder bisa dijalankan ulang tanpa error duplikat
        // Update weight dan description jika nama sudah ada (admin bisa ubah nanti)
        DB::table('criteria')->upsert(
            array_map(fn($c) => array_merge($c, [
                'created_at' => now(),
                'updated_at' => now(),
            ]), $criteria),
            uniqueBy: ['name'],
            update: ['description', 'weight', 'updated_at']
        );

        $this->command->info('✓ ' . count($criteria) . ' kriteria berhasil di-seed.');
        $this->command->line('  Total bobot: ' . array_sum(array_column($criteria, 'weight')));
        $this->command->line('  (Idealnya total bobot = 1.0000)');
    }
}
