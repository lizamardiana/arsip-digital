<?php

namespace App\Http\Controllers;

use App\Models\SuratMasuk;
use App\Models\User;
use App\Http\Requests\SuratMasukRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SuratMasukController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $search = $request->get('search');
        $tahun = $request->get('tahun');
        $bulan = $request->get('bulan');

        $suratMasuk = SuratMasuk::with(['user'])
            ->when($search, function ($query) use ($search) {
                return $query->where('nomor_surat', 'like', "%{$search}%")
                    ->orWhere('pengirim', 'like', "%{$search}%")
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

        $years = SuratMasuk::selectRaw('YEAR(tanggal_surat) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('surat-masuk.index', compact('suratMasuk', 'years'));
    }

    public function create()
    {
        return view('surat-masuk.create');
    }

    public function store(SuratMasukRequest $request)
    {
        try {
            $data = $request->validated();
            $data['user_id'] = auth()->id();
            $data['nomor_surat'] = $this->generateNomorSurat();

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '_' . Str::slug($data['perihal']) . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('surat-masuk', $fileName, 'public');

                $data['file_path'] = $filePath;
                $data['file_name'] = $fileName;
            }

            SuratMasuk::create($data);

            return redirect()->route('surat-masuk.index')
                ->with('success', 'Surat masuk berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(SuratMasuk $suratMasuk)
    {
        return view('surat-masuk.show', compact('suratMasuk'));
    }

    public function edit(SuratMasuk $suratMasuk)
    {
        if (auth()->id() !== $suratMasuk->user_id && !(auth()->user()->isAdmin ?? false)) {
            abort(403, 'Unauthorized action.');
        }

        return view('surat-masuk.edit', compact('suratMasuk'));
    }

    public function update(SuratMasukRequest $request, SuratMasuk $suratMasuk)
    {
        if (auth()->id() !== $suratMasuk->user_id && !(auth()->user()->isAdmin ?? false)) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $data = $request->validated();

            if ($request->hasFile('file')) {
                if ($suratMasuk->file_path) {
                    Storage::disk('public')->delete($suratMasuk->file_path);
                }

                $file = $request->file('file');
                $fileName = time() . '_' . Str::slug($data['perihal']) . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('surat-masuk', $fileName, 'public');

                $data['file_path'] = $filePath;
                $data['file_name'] = $fileName;
            }

            $suratMasuk->update($data);

            return redirect()->route('surat-masuk.index')
                ->with('success', 'Surat masuk berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(SuratMasuk $suratMasuk)
    {
        if (auth()->id() !== $suratMasuk->user_id && !(auth()->user()->isAdmin ?? false)) {
            abort(403, 'Unauthorized action.');
        }

        try {
            if ($suratMasuk->file_path) {
                Storage::disk('public')->delete($suratMasuk->file_path);
            }

            $suratMasuk->delete();

            return redirect()->route('surat-masuk.index')
                ->with('success', 'Surat masuk berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function download(SuratMasuk $suratMasuk)
    {
        if (!$suratMasuk->file_path) {
            return redirect()->back()
                ->with('error', 'File tidak ditemukan.');
        }

        return response()->download(
            storage_path('app/public/' . $suratMasuk->file_path),
            $suratMasuk->file_name
        );
    }

    // ALTERNATIF: Method dengan headers tambahan
    public function downloadForce(SuratMasuk $suratMasuk)
    {
        if (!$suratMasuk->file_path) {
            return redirect()->back()
                ->with('error', 'File tidak ditemukan.');
        }

        if (!Storage::disk('public')->exists($suratMasuk->file_path)) {
            return redirect()->back()
                ->with('error', 'File tidak ditemukan di server.');
        }

        $filePath = storage_path('app/public/' . $suratMasuk->file_path);
        $fileName = $suratMasuk->file_name;

        // Dapatkan file size menggunakan fungsi PHP native
        $fileSize = filesize($filePath);

        // Dapatkan MIME type menggunakan fungsi PHP native
        $mimeType = mime_content_type($filePath);

        $headers = [
            'Content-Type' => $mimeType ?: 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Content-Length' => $fileSize,
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        return response()->download($filePath, $fileName, $headers);
    }

    private function generateNomorSurat()
    {
        $year = now()->year;
        $lastSurat = SuratMasuk::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastSurat ? intval(substr($lastSurat->nomor_surat, -3)) + 1 : 1;

        return "SM/{$year}/" . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }
}