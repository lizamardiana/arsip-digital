<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SuratMasuk extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'surat_masuk';

    protected $fillable = [
        'nomor_surat',
        'tanggal_surat',
        'tanggal_terima',
        'pengirim',
        'perihal',
        'ringkasan',
        'file_path',
        'file_name',
        'user_id',
    ];

    protected $casts = [
        'tanggal_surat' => 'date',
        'tanggal_terima' => 'date',
    ];

    // Relationships

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            return $query->where('nomor_surat', 'like', '%' . $search . '%')
                ->orWhere('pengirim', 'like', '%' . $search . '%')
                ->orWhere('perihal', 'like', '%' . $search . '%');
        });


        $query->when($filters['tahun'] ?? false, function ($query, $tahun) {
            return $query->whereYear('tanggal_surat', $tahun);
        });

        $query->when($filters['bulan'] ?? false, function ($query, $bulan) {
            return $query->whereMonth('tanggal_surat', $bulan);
        });
    }


    public function getFileExtensionAttribute()
    {
        return pathinfo($this->file_name, PATHINFO_EXTENSION);
    }

    public function getFileIconAttribute()
    {
        $ext = $this->file_extension;
        $icons = [
            'pdf' => 'far fa-file-pdf text-danger',
            'doc' => 'far fa-file-word text-primary',
            'docx' => 'far fa-file-word text-primary',
            'jpg' => 'far fa-file-image text-success',
            'jpeg' => 'far fa-file-image text-success',
            'png' => 'far fa-file-image text-success',
        ];

        return $icons[$ext] ?? 'far fa-file text-secondary';
    }
}