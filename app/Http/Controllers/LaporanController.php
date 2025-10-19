<?php

namespace App\Http\Controllers;

use App\Models\SuratMasuk;
use App\Models\SuratKeluar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index()
    {
        // Set default dates for the form
        $defaultStartDate = date('Y-m-01'); // First day of current month
        $defaultEndDate = date('Y-m-d'); // Today

        return view('laporan.index', compact('defaultStartDate', 'defaultEndDate'));
    }

    public function generate(Request $request)
    {
        // Validasi input
        $request->validate([
            'jenis' => 'required|in:masuk,keluar',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $jenis = $request->jenis;
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        // Debug info
        \Log::info("Generating report: {$jenis} from {$startDate} to {$endDate}");

        // Ambil data berdasarkan jenis dengan format tanggal yang benar
        if ($jenis === 'masuk') {
            $data = SuratMasuk::with(['user'])
                ->whereDate('tanggal_terima', '>=', $startDate)
                ->whereDate('tanggal_terima', '<=', $endDate)
                ->orderBy('tanggal_terima', 'asc')
                ->get();
            $view = 'laporan.surat-masuk';

            \Log::info("Found {$data->count()} surat masuk records");
        } else {
            $data = SuratKeluar::with(['user'])
                ->whereDate('tanggal_kirim', '>=', $startDate)
                ->whereDate('tanggal_kirim', '<=', $endDate)
                ->orderBy('tanggal_kirim', 'asc')
                ->get();
            $view = 'laporan.surat-keluar';

            \Log::info("Found {$data->count()} surat keluar records");
        }

        return view($view, compact('data', 'startDate', 'endDate', 'jenis'));
    }

    public function exportExcel(Request $request)
    {
        // Validasi parameter - tanggal jadi optional
        $request->validate([
            'jenis' => 'required|in:masuk,keluar',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $jenis = $request->jenis;
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        // Ambil data berdasarkan jenis
        if ($jenis === 'masuk') {
            $query = SuratMasuk::with(['user']);

            if ($startDate) {
                $query->whereDate('tanggal_terima', '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate('tanggal_terima', '<=', $endDate);
            }

            $data = $query->orderBy('tanggal_terima', 'asc')->get();

            if ($startDate && $endDate) {
                $filename = "laporan-surat-masuk-{$startDate}-hingga-{$endDate}.csv";
            } else {
                $filename = "semua-surat-masuk.csv";
            }
        } else {
            $query = SuratKeluar::with(['user']);

            if ($startDate) {
                $query->whereDate('tanggal_kirim', '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate('tanggal_kirim', '<=', $endDate);
            }

            $data = $query->orderBy('tanggal_kirim', 'asc')->get();

            if ($startDate && $endDate) {
                $filename = "laporan-surat-keluar-{$startDate}-hingga-{$endDate}.csv";
            } else {
                $filename = "semua-surat-keluar.csv";
            }
        }

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        return $this->generateCSV($data, $jenis, $headers);
    }

    public function exportPdf(Request $request)
    {
        // Untuk PDF, kita gunakan view yang sama dengan generate
        // User bisa print dari browser
        return $this->generate($request);
    }

    /**
     * Generate CSV file untuk export
     */
    private function generateCSV($data, $jenis, $headers)
    {
        $callback = function () use ($data, $jenis) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fwrite($file, "\xEF\xBB\xBF");

            // Header CSV
            if ($jenis === 'masuk') {
                fputcsv($file, [
                    'No',
                    'Nomor Surat',
                    'Tanggal Surat',
                    'Tanggal Terima',
                    'Pengirim',
                    'Perihal',
                ], ';');

                // Data
                foreach ($data as $index => $item) {
                    fputcsv($file, [
                        $index + 1,
                        $item->nomor_surat,
                        $item->tanggal_surat->format('d/m/Y'),
                        $item->tanggal_terima->format('d/m/Y'),
                        $this->escapeCsv($item->pengirim),
                        $this->escapeCsv($item->perihal),
                    ], ';');
                }
            } else {
                fputcsv($file, [
                    'No',
                    'Nomor Surat',
                    'Tanggal Surat',
                    'Tanggal Kirim',
                    'Tujuan',
                    'Perihal',
                ], ';');

                // Data
                foreach ($data as $index => $item) {
                    fputcsv($file, [
                        $index + 1,
                        $item->nomor_surat,
                        $item->tanggal_surat->format('d/m/Y'),
                        $item->tanggal_kirim->format('d/m/Y'),
                        $this->escapeCsv($item->tujuan),
                        $this->escapeCsv($item->perihal),
                    ], ';');
                }
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Escape string untuk CSV
     */
    private function escapeCsv($value)
    {
        // Jika value mengandung koma, titik koma, atau quote, wrap dengan quotes
        if (preg_match('/[,"\n\r]/', $value)) {
            $value = '"' . str_replace('"', '""', $value) . '"';
        }
        return $value;
    }

    /**
     * Quick report for dashboard
     */
    public function quickReport(Request $request)
    {
        $jenis = $request->get('jenis', 'masuk');
        $period = $request->get('period', 'month'); // month, week, today

        $now = Carbon::now();

        switch ($period) {
            case 'today':
                $startDate = $now->format('Y-m-d');
                $endDate = $now->format('Y-m-d');
                break;
            case 'week':
                $startDate = $now->startOfWeek()->format('Y-m-d');
                $endDate = $now->endOfWeek()->format('Y-m-d');
                break;
            case 'month':
            default:
                $startDate = $now->startOfMonth()->format('Y-m-d');
                $endDate = $now->endOfMonth()->format('Y-m-d');
                break;
        }

        if ($jenis === 'masuk') {
            $count = SuratMasuk::whereDate('tanggal_terima', '>=', $startDate)
                ->whereDate('tanggal_terima', '<=', $endDate)
                ->count();
        } else {
            $count = SuratKeluar::whereDate('tanggal_kirim', '>=', $startDate)
                ->whereDate('tanggal_kirim', '<=', $endDate)
                ->count();
        }

        return response()->json([
            'jenis' => $jenis,
            'period' => $period,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'count' => $count
        ]);
    }
}