<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Menonaktifkan transaksi otomatis Laravel untuk migrasi ini.
     * Cukup gunakan public tanpa deklarasi tipe data 'bool'
     */
    public $withinTransaction = false;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            DB::statement('ALTER TABLE addresses ALTER COLUMN postal_code TYPE integer USING postal_code::integer');
        } catch (\Exception $e) {
            throw new \RuntimeException(
                "Gagal mengubah kolom. Pastikan tidak ada data string/non-angka di kolom postal_code.\n" . 
                "Error asli: " . $e->getMessage()
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE addresses ALTER COLUMN postal_code TYPE varchar(255) USING postal_code::varchar');
    }
};