<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Enumerable;

class LaporanPosyanduExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $laporans;

    public function __construct($laporans)
    {
        $this->laporans = $laporans;
    }

    public function collection(): Enumerable
    {
        return $this->laporans;
    }

    public function headings(): array
    {
        return [
            'No',
            'Kategori',
            'Posyandu',
            'Nama',
            'NIK',
            'Jenis Kelamin',
            'Umur',
            'Nama Ibu/Suami',
            'Tanggal Periksa',
            'BB (kg)',
            'TB/LiLA (cm)',
            'LK/HB',
            'Tekanan Darah',
            'Status BB/U',
            'Status TB/U',
            'Status BB/TB',
            'Status KEK',
            'Status Anemia',
            'Status TD',
            'Vitamin A',
            'Imunisasi',
            'Tanggal Validasi',
        ];
    }

    public function map($l): array
    {
        static $no = 0;
        $no++;

        $id   = $l->identitas ?? [];
        $data = $l->data_pemeriksaan ?? [];
        $gizi = $l->status_gizi ?? [];

        $isBalita = $l->kategori === 'balita';

        return [
            $no,
            ucfirst($l->kategori),
            $l->posyandu->nama_posyandu ?? '-',
            $id['nama'] ?? '-',
            $id['nik'] ?? '-',
            ($id['jenis_kelamin'] ?? '') === 'L' ? 'Laki-laki' : (($id['jenis_kelamin'] ?? '') === 'P' ? 'Perempuan' : '-'),
            $isBalita ? (($id['umur_bulan'] ?? '-') . ' bln') : (($id['umur'] ?? '-') . ' thn'),
            $isBalita ? ($id['nama_ibu'] ?? '-') : ($id['nama_suami'] ?? '-'),
            $data['tanggal_periksa'] ?? '-',
            $isBalita ? ($data['berat_badan'] ?? '-') : ($data['bb'] ?? '-'),
            $isBalita ? ($data['tinggi_badan'] ?? '-') : ($data['lila'] ?? '-'),
            $isBalita ? ($data['lingkar_kepala'] ?? '-') : ($data['hemoglobin'] ?? '-'),
            $data['tekanan_darah'] ?? '-',
            $gizi['bb_u'] ?? '-',
            $gizi['tb_u'] ?? '-',
            $gizi['bb_tb'] ?? '-',
            $gizi['kek'] ?? '-',
            $gizi['anemia'] ?? '-',
            $gizi['td'] ?? '-',
            $isBalita ? (!empty($data['vitamin_a']) ? 'Ya' : 'Tidak') : '-',
            ($isBalita && is_array($l->imunisasi) && count($l->imunisasi) > 0) ? implode(', ', $l->imunisasi) : '-',
            $l->tanggal_validasi ? $l->tanggal_validasi->format('d/m/Y H:i') : '-',
        ];
    }

    /**
     * PENTING: Return type harus ?array
     */
    public function styles(Worksheet $sheet): ?array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'EC4899'],
                ],
            ],
        ];
    }
}