<?php

namespace App\Http\Controllers;

use App\Models\SuratKeluar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SuratKeluarController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $tahun = $request->get('tahun');
        $bulan = $request->get('bulan');

        $suratKeluar = SuratKeluar::with(['user'])
            ->when($search, function ($query) use ($search) {
                return $query->where('nomor_surat', 'like', "%{$search}%")
                    ->orWhere('tujuan', 'like', "%{$search}%")
                    ->orWhere('perihal', 'like', "%{$search}%");
            })
            ->when($tahun, function ($query) use ($tahun) {
                return $query->whereYear('tanggal_surat', $tahun);
            })
            ->when($bulan, function ($query) use ($bulan) {
                return $query->whereMonth('tanggal_surat', $bulan);
            })
            ->latest()
            ->paginate(10);

        $years = SuratKeluar::selectRaw('YEAR(tanggal_surat) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('surat-keluar.index', compact('suratKeluar', 'years'));
    }

    public function create()
    {
        return view('surat-keluar.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor_surat' => 'required|string|max:255|unique:surat_keluar',
            'tanggal_surat' => 'required|date',
            'tanggal_kirim' => 'required|date',
            'tujuan' => 'required|string|max:255',
            'perihal' => 'required|string|max:500',
            'isi_ringkas' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);

        try {
            $data = $request->all();
            $data['user_id'] = auth()->id();

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '_' . Str::slug($data['perihal']) . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('surat-keluar', $fileName, 'public');

                $data['file_path'] = $filePath;
                $data['file_name'] = $fileName;
            }

            SuratKeluar::create($data);

            return redirect()->route('surat-keluar.index')
                ->with('success', 'Surat keluar berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(SuratKeluar $suratKeluar)
    {
        return view('surat-keluar.show', compact('suratKeluar'));
    }

    public function edit(SuratKeluar $suratKeluar)
    {
        return view('surat-keluar.edit', compact('suratKeluar'));
    }

    public function update(Request $request, SuratKeluar $suratKeluar)
    {
        $request->validate([
            'nomor_surat' => 'required|string|max:255|unique:surat_keluar,nomor_surat,' . $suratKeluar->id,
            'tanggal_surat' => 'required|date',
            'tanggal_kirim' => 'required|date',
            'tujuan' => 'required|string|max:255',
            'perihal' => 'required|string|max:500',
            'isi_ringkas' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);

        try {
            $data = $request->all();

            if ($request->hasFile('file')) {
                // Hapus file lama jika ada
                if ($suratKeluar->file_path) {
                    Storage::disk('public')->delete($suratKeluar->file_path);
                }

                $file = $request->file('file');
                $fileName = time() . '_' . Str::slug($data['perihal']) . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('surat-keluar', $fileName, 'public');

                $data['file_path'] = $filePath;
                $data['file_name'] = $fileName;
            }

            $suratKeluar->update($data);

            return redirect()->route('surat-keluar.index')
                ->with('success', 'Surat keluar berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(SuratKeluar $suratKeluar)
    {
        try {
            // Hapus file jika ada
            if ($suratKeluar->file_path) {
                Storage::disk('public')->delete($suratKeluar->file_path);
            }

            $suratKeluar->delete();

            return redirect()->route('surat-keluar.index')
                ->with('success', 'Surat keluar berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function download(SuratKeluar $suratKeluar)
    {
        if (!$suratKeluar->file_path) {
            return redirect()->back()
                ->with('error', 'File tidak ditemukan.');
        }

        return response()->download(
            storage_path('app/public/' . $suratKeluar->file_path),
            $suratKeluar->file_name
        );
    }

    private function generateNomorSurat()
    {
        $year = now()->year;
        $lastSurat = SuratKeluar::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastSurat ? intval(substr($lastSurat->nomor_surat, -3)) + 1 : 1;

        return "SK/{$year}/" . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }
}