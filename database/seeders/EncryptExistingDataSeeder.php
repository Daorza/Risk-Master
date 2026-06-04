<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Alternative;
use App\Models\Assessment;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class EncryptExistingDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Enkripsi alternatives yang sudah ada
        DB::table('alternatives')->get()->each(function ($row) {
            // Cek apakah sudah terenkripsi dengan mencoba decrypt
            try {
                Crypt::decryptString($row->name);
                // Sudah terenkripsi, skip
            } catch (\Exception $e) {
                // Belum terenkripsi, enkripsi sekarang
                DB::table('alternatives')->where('id', $row->id)->update([
                    'name'        => Crypt::encryptString($row->name),
                    'description' => $row->description
                        ? Crypt::encryptString($row->description)
                        : null,
                ]);
            }
        });

        // Enkripsi assessments yang sudah ada
        DB::table('assessments')->get()->each(function ($row) {
            try {
                Crypt::decryptString($row->title);
            } catch (\Exception $e) {
                DB::table('assessments')->where('id', $row->id)->update([
                    'title'       => Crypt::encryptString($row->title),
                    'description' => $row->description
                        ? Crypt::encryptString($row->description)
                        : null,
                ]);
            }
        });

        // Enkripsi audit_logs yang sudah ada
        DB::table('audit_logs')->get()->each(function ($row) {
            try {
                if ($row->ip_address) {
                    Crypt::decryptString($row->ip_address);
                }
            } catch (\Exception $e) {
                DB::table('audit_logs')->where('id', $row->id)->update([
                    'ip_address' => $row->ip_address
                        ? Crypt::encryptString($row->ip_address)
                        : null,
                    'old_data' => $row->old_data
                        ? Crypt::encryptString($row->old_data)
                        : null,
                    'new_data' => $row->new_data
                        ? Crypt::encryptString($row->new_data)
                        : null,
                ]);
            }
        });

        $this->command->info('Data sensitif berhasil dienkripsi.');
    }
}
