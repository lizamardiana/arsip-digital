<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SuratMasuk;
use App\Models\SuratKeluar;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CheckDatabaseCommand extends Command
{
    protected $signature = 'db:check';
    protected $description = 'Check database structure and data';

    public function handle()
    {
        $this->info('🔍 Checking database structure and data...');

        // Check tables existence
        $tables = ['users', 'surat_masuk', 'surat_keluar'];

        foreach ($tables as $table) {
            try {
                $count = DB::table($table)->count();
                $this->info("✅ Table {$table}: {$count} records");
            } catch (\Exception $e) {
                $this->error("❌ Table {$table}: Does not exist or error - " . $e->getMessage());
            }
        }

        // Check sample data
        $this->info("\n📊 Sample Data:");

        $this->info("\n📈 Dashboard Statistics:");
        $this->info("Total Surat Masuk: " . SuratMasuk::count());
        $this->info("Total Surat Keluar: " . SuratKeluar::count());

        // Check current month data
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $masukBulanIni = SuratMasuk::whereMonth('tanggal_terima', $currentMonth)
            ->whereYear('tanggal_terima', $currentYear)
            ->count();

        $keluarBulanIni = SuratKeluar::whereMonth('tanggal_kirim', $currentMonth)
            ->whereYear('tanggal_kirim', $currentYear)
            ->count();

        $this->info("Surat Masuk Bulan Ini: {$masukBulanIni}");
        $this->info("Surat Keluar Bulan Ini: {$keluarBulanIni}");

        // Check if we have any data at all
        if (SuratMasuk::count() == 0 && SuratKeluar::count() == 0) {
            $this->error("\n🚨 PERHATIAN: Database kosong! Tidak ada data surat.");
            $this->info("💡 Solusi: Input data manual melalui menu Surat Masuk/Surat Keluar.");
        } else {
            $this->info("\n🎉 Database berisi data. Statistik dashboard seharusnya muncul.");
        }

        return Command::SUCCESS;
    }
}