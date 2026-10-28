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
    protected array $meta;

    public function __construct($laporans, array $meta = [])
    {
        $this->laporans = $laporans;
        $this->meta     = $meta;
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
            'JK',
            'Umur',
            'Nama Ibu/Suami',
            'Tanggal Periksa',
            'BB (kg)',
            'TB (cm)',
            'LIKA / LP (cm)',
            'LILA (cm)',
            'Cara Ukur',
            'ASI / PMT',
            'HB / TD',
            'TFU (cm)',
            'TBJ (gr)',
            'Status BB/U',
            'Status TB/U',
            'Status BB/TB',
            'Status KEK',
            'Status Anemia',
            'Status TD',
            'Tindakan Bidan',
            'Tanggal Validasi',
        ];
    }

    public function map($l): array
    {
        static $no = 0;
        $no++;

        $id   = is_array($l->identitas ?? null)        ? $l->identitas        : [];
        $data = is_array($l->data_pemeriksaan ?? null) ? $l->data_pemeriksaan : [];
        $gizi = is_array($l->status_gizi ?? null)      ? $l->status_gizi      : [];
        
        $isBalita = $l->kategori === 'balita';
        
        $tindakan = $data['tindakan_bidan'] ?? [];
        $tindakanText = (!empty($tindakan) && is_array($tindakan)) ? implode(', ', $tindakan) : '-';

        return [
            $no,
            ucfirst($l->kategori),
            $l->posyandu->nama_posyandu ?? '-',
            $id['nama'] ?? '-',
            $id['nik'] ?? '-',
            ($id['jenis_kelamin'] ?? '') === 'L' ? 'Laki-laki' : ((($id['jenis_kelamin'] ?? '') === 'P') ? 'Perempuan' : '-'),
            $isBalita ? (($id['umur_bulan'] ?? '-') . ' bln') : (($id['umur'] ?? '-') . ' thn'),
            $isBalita ? ($id['nama_ibu'] ?? '-') : ($id['nama_suami'] ?? '-'),
            $data['tanggal_periksa'] ?? '-',
            $isBalita ? ($data['berat_badan'] ?? '-') : ($data['bb'] ?? '-'),
            $isBalita ? ($data['tinggi_badan'] ?? '-') : ($data['tinggi_badan'] ?? '-'),
            $isBalita ? ($data['lingkar_kepala'] ?? '-') : ($data['lingkar_perut'] ?? '-'),
            $isBalita ? ($data['lila'] ?? '-') : ($data['lila'] ?? '-'),
            $isBalita ? ($data['cara_ukur'] ?? '-') : '-',
            $isBalita 
                ? (!empty($data['asi_eksklusif']) ? 'ASI: Ya' : 'ASI: Tidak') 
                : (!empty($data['pmt_pemulihan']) ? 'PMT: Ya' : 'PMT: Tidak'),
            $isBalita ? '-' : ($data['hemoglobin'] ?? '-') . ' / ' . ($data['tekanan_darah'] ?? '-'),
            $isBalita ? '-' : ($data['tfu'] ?? '-'),
            $isBalita ? '-' : ($data['taksiran_bb_janin'] ?? '-'),
            $gizi['bb_u'] ?? '-',
            $gizi['tb_u'] ?? '-',
            $gizi['bb_tb'] ?? '-',
            $gizi['kek'] ?? '-',
            $gizi['anemia'] ?? '-',
            $gizi['td'] ?? '-',
            $tindakanText,
            $l->tanggal_validasi ? $l->tanggal_validasi->format('d/m/Y H:i') : '-',
        ];
    }

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