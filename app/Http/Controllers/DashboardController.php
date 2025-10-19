<?php

namespace App\Http\Controllers;

use App\Models\SuratMasuk;
use App\Models\SuratKeluar;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            $year = $request->get('year', date('Y'));

            // Data untuk dashboard
            $stats = $this->getDashboardStats();
            $recentSuratMasuk = $this->getRecentSuratMasuk();
            $recentSuratKeluar = $this->getRecentSuratKeluar();
            $chartData = $this->getChartData($year);

            // Debug info
            \Log::info('Dashboard Data:', [
                'stats' => $stats,
                'recent_masuk_count' => $recentSuratMasuk->count(),
                'recent_keluar_count' => $recentSuratKeluar->count()
            ]);

            return view('dashboard.index', compact('stats', 'recentSuratMasuk', 'recentSuratKeluar', 'chartData'));

        } catch (\Exception $e) {
            \Log::error('Dashboard Error: ' . $e->getMessage());

            // Fallback data
            return view('dashboard.index', $this->getFallbackData());
        }
    }

    private function getDashboardStats()
    {
        try {
            $currentMonth = now()->month;
            $currentYear = now()->year;

            $totalSuratMasuk = SuratMasuk::count();
            $totalSuratKeluar = SuratKeluar::count();

            $suratMasukBulanIni = SuratMasuk::whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->count();

            $suratKeluarBulanIni = SuratKeluar::whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->count();

            $suratBaru = SuratMasuk::where('created_at', '>=', now()->subDays(7))
                ->count();

            return [
                'total_surat_masuk' => $totalSuratMasuk,
                'total_surat_keluar' => $totalSuratKeluar,
                'surat_masuk_bulan_ini' => $suratMasukBulanIni,
                'surat_keluar_bulan_ini' => $suratKeluarBulanIni,
                'surat_baru' => $suratBaru,
            ];

        } catch (\Exception $e) {
            \Log::error("Error in getDashboardStats: " . $e->getMessage());
            return $this->getDefaultStats();
        }
    }

    private function getRecentSuratMasuk()
    {
        try {
            return SuratMasuk::with(['user'])
                ->latest()
                ->limit(5)
                ->get();

        } catch (\Exception $e) {
            \Log::error("Error in getRecentSuratMasuk: " . $e->getMessage());
            return collect();
        }
    }

    private function getRecentSuratKeluar()
    {
        try {
            return SuratKeluar::with(['user'])
                ->latest()
                ->limit(5)
                ->get();

        } catch (\Exception $e) {
            \Log::error("Error in getRecentSuratKeluar: " . $e->getMessage());
            return collect();
        }
    }

    private function getChartData($year = null)
    {
        try {
            $chartYear = $year ?? now()->year;

            // Dapatkan tahun yang tersedia
            $yearsMasuk = SuratMasuk::select(DB::raw('YEAR(created_at) as year'))
                ->distinct()
                ->pluck('year')
                ->filter()
                ->toArray();

            $yearsKeluar = SuratKeluar::select(DB::raw('YEAR(created_at) as year'))
                ->distinct()
                ->pluck('year')
                ->filter()
                ->toArray();

            $availableYears = array_unique(array_merge($yearsMasuk, $yearsKeluar));
            rsort($availableYears);

            // Jika tahun yang diminta tidak ada, gunakan tahun terbaru
            if (!in_array((int) $chartYear, $availableYears) && !empty($availableYears)) {
                $chartYear = max($availableYears);
            }

            $suratMasukData = [];
            $suratKeluarData = [];

            for ($month = 1; $month <= 12; $month++) {
                $masukCount = SuratMasuk::whereYear('created_at', $chartYear)
                    ->whereMonth('created_at', $month)
                    ->count();

                $keluarCount = SuratKeluar::whereYear('created_at', $chartYear)
                    ->whereMonth('created_at', $month)
                    ->count();

                $suratMasukData[] = $masukCount;
                $suratKeluarData[] = $keluarCount;
            }

            return [
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                'surat_masuk' => $suratMasukData,
                'surat_keluar' => $suratKeluarData,
                'year' => $chartYear,
                'available_years' => $availableYears
            ];

        } catch (\Exception $e) {
            \Log::error("Error in getChartData: " . $e->getMessage());
            return $this->getDefaultChartData();
        }
    }

    private function getDefaultStats()
    {
        return [
            'total_surat_masuk' => 0,
            'total_surat_keluar' => 0,
            'surat_masuk_bulan_ini' => 0,
            'surat_keluar_bulan_ini' => 0,
            'surat_baru' => 0,
        ];
    }

    private function getDefaultChartData()
    {
        return [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            'surat_masuk' => array_fill(0, 12, 0),
            'surat_keluar' => array_fill(0, 12, 0),
            'year' => now()->year,
            'available_years' => []
        ];
    }

    private function getFallbackData()
    {
        return [
            'stats' => $this->getDefaultStats(),
            'recentSuratMasuk' => collect(),
            'recentSuratKeluar' => collect(),
            'chartData' => $this->getDefaultChartData()
        ];
    }

    // Method untuk debugging
    public function checkData()
    {
        $suratMasukCount = SuratMasuk::count();
        $suratKeluarCount = SuratKeluar::count();
        $recentMasuk = SuratMasuk::latest()->first();
        $recentKeluar = SuratKeluar::latest()->first();

        return response()->json([
            'surat_masuk_total' => $suratMasukCount,
            'surat_keluar_total' => $suratKeluarCount,
            'recent_surat_masuk' => $recentMasuk,
            'recent_surat_keluar' => $recentKeluar,
            'recent_5_masuk' => SuratMasuk::latest()->limit(5)->get()->toArray(),
            'recent_5_keluar' => SuratKeluar::latest()->limit(5)->get()->toArray(),
        ]);
    }
}