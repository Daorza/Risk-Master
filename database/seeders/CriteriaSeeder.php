<?php

namespace Database\Seeders;

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
                'description' => 'Mengukur seberapa efektif alternatif solusi dalam mengurangi atau menghilangkan risiko keamanan yang diidentifikasi.',
                'type'        => 'benefit',
                'weight'      => 0.1000,
            ],
            [
                'name'        => 'Biaya Implementasi',
                'description' => 'Total biaya yang dibutuhkan untuk mengimplementasikan solusi, termasuk perangkat, lisensi, pelatihan, dan operasional.',
                'type'        => 'cost',
                'weight'      => 0.3500,
            ],
            [
                'name'        => 'Kompleksitas Penerapan',
                'description' => 'Tingkat kesulitan teknis dan kebutuhan sumber daya dalam proses implementasi solusi.',
                'type'        => 'cost',
                'weight'      => 0.2500,
            ],
            [
                'name'        => 'Kecepatan Implementasi',
                'description' => 'Seberapa cepat solusi dapat diterapkan dan mulai memberikan manfaat keamanan.',
                'type'        => 'benefit',
                'weight'      => 0.1500,
            ],
            [
                'name'        => 'Kepatuhan Regulasi',
                'description' => 'Kemampuan solusi dalam mendukung pemenuhan standar dan regulasi keamanan yang berlaku.',
                'type'        => 'benefit',
                'weight'      => 0.1000,
            ],
            [
                'name'        => 'Kesiapan Adopsi Pengguna',
                'description' => 'Tingkat kesiapan pengguna dalam menerima dan menggunakan solusi tanpa hambatan yang signifikan.',
                'type'        => 'benefit',
                'weight'      => 0.0300,
            ],
            [
                'name'        => 'Stabilitas Vendor',
                'description' => 'Reputasi, dukungan jangka panjang, serta keberlanjutan penyedia solusi atau vendor.',
                'type'        => 'benefit',
                'weight'      => 0.0200,
            ],
        ];

        DB::table('criteria')->upsert(
            array_map(fn($c) => array_merge($c, [
                'created_at' => now(),
                'updated_at' => now(),
            ]), $criteria),
            uniqueBy: ['name'],
            update: [
                'description',
                'type',
                'weight',
                'updated_at'
            ]
        );

        $this->command->info('✓ ' . count($criteria) . ' kriteria berhasil di-seed.');
        $this->command->line('  Total bobot: ' . number_format(
            array_sum(array_column($criteria, 'weight')),
            4
        ));
    }
}
