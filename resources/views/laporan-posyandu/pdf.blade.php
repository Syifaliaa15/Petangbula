<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Posyandu</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 8px; color: #333; }
        .header { text-align: center; border-bottom: 3px solid #ec4899; padding-bottom: 10px; margin-bottom: 12px; }
        .header h1 { font-size: 15px; color: #831843; font-weight: bold; margin-bottom: 4px; }
        .header p { font-size: 9px; color: #6b7280; }
        
        .info-table { width: 100%; border-collapse: collapse; background: #fce7f3; margin-bottom: 10px; border-radius: 6px; }
        .info-table td { padding: 6px 10px; font-size: 8.5px; }
        .info-table strong { color: #831843; }

        table.data { width: 100%; border-collapse: collapse; font-size: 7.5px; }
        table.data th { background: #ec4899; color: #fff; padding: 5px 3px; text-align: left; font-weight: bold; border: 1px solid #be185d; font-size: 7.5px; }
        table.data td { padding: 4px 3px; border: 1px solid #fbcfe8; vertical-align: top; }
        table.data tr:nth-child(even) td { background: #fff5f8; }

        .badge-normal  { color: #065f46; font-weight: bold; }
        .badge-kurang  { color: #92400e; font-weight: bold; }
        .badge-buruk   { color: #991b1b; font-weight: bold; }
        .badge-lebih   { color: #1d4ed8; font-weight: bold; }

        .footer { margin-top: 20px; padding-top: 10px; border-top: 1px solid #fbcfe8; text-align: center; font-size: 8px; color: #9ca3af; }
        .text-center { text-align: center; }
    </style>
</head>
<body>

@php
    $badgeClass = function ($label) {
        $l = strtolower((string) $label);
        if (str_contains($l, 'buruk') || str_contains($l, 'sangat') || str_contains($l, 'anemia') || str_contains($l, 'hipertensi')) return 'buruk';
        if (str_contains($l, 'lebih') || str_contains($l, 'obesitas') || str_contains($l, 'tinggi')) return 'lebih';
        if (str_contains($l, 'normal') || str_contains($l, 'baik') || str_contains($l, 'tidak risiko') || str_contains($l, 'tidak anemia')) return 'normal';
        return 'kurang';
    };
@endphp

    <!-- HEADER -->
    <div class="header">
        <h1>LAPORAN POSYANDU</h1>
        <p>Desa Sindangpanon - Sistem Informasi PETANGBULA</p>
    </div>

    <!-- INFO BOX -->
    <table class="info-table">
        <tr>
            <td width="25%"><strong>Periode:</strong> {{ $periodeLabel ?? ($bulanLabel . ' ' . $tahun) }}</td>
            <td width="25%"><strong>Posyandu:</strong> {{ $posyanduLabel }}</td>
            <td width="25%"><strong>Filter:</strong> {{ $filterInfo ?? 'Semua Data' }}</td>
            <td width="25%"><strong>Total Data:</strong> {{ $laporans->count() }}</td>
        </tr>
        <tr>
            <td colspan="4">
                <strong>Dicetak:</strong> {{ $tanggalCetak ?? now()->translatedFormat('d F Y H:i') }} | 
                <strong>Oleh:</strong> {{ $dicetakOleh ?? Auth::user()->name }}
            </td>
        </tr>
    </table>

    <!-- TABEL DATA -->
    @if($laporans->count() > 0)
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 20px;">No</th>
                    <th style="width: 40px;">Kategori</th>
                    <th style="width: 55px;">Posyandu</th>
                    <th style="width: 90px;">Identitas</th>
                    <th style="width: 110px;">Data Periksa & KIA</th>
                    <th style="width: 80px;">Status Gizi</th>
                    <th style="width: 80px;">Tindakan Bidan</th>
                    <th style="width: 60px;">Validasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($laporans as $index => $l)
                    @php
                        $id   = is_array($l->identitas ?? null)         ? $l->identitas         : [];
                        $data = is_array($l->data_pemeriksaan ?? null)  ? $l->data_pemeriksaan  : [];
                        $gizi = is_array($l->status_gizi ?? null)       ? $l->status_gizi       : [];
                        $imun = is_array($l->imunisasi ?? null)         ? $l->imunisasi         : [];
                        $tindakan = $data['tindakan_bidan'] ?? [];
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">
                            <strong style="color: {{ $l->kategori === 'balita' ? '#1d4ed8' : '#be185d' }};">
                                {{ ucfirst($l->kategori) }}
                            </strong>
                        </td>
                        <td>{{ $l->posyandu->nama_posyandu ?? '-' }}</td>
                        <td>
                            <strong>{{ $id['nama'] ?? '-' }}</strong><br>
                            NIK: {{ $id['nik'] ?? '-' }}<br>
                            @if($l->kategori === 'balita')
                                {{ $id['umur_bulan'] ?? '-' }} bln · {{ ($id['jenis_kelamin'] ?? '-') === 'L' ? 'L' : 'P' }}<br>
                                Ibu: {{ $id['nama_ibu'] ?? '-' }}
                            @else
                                {{ $id['umur'] ?? '-' }} thn · Hamil Ke-{{ $id['hamil_ke'] ?? '-' }}<br>
                                Suami: {{ $id['nama_suami'] ?? '-' }}
                            @endif
                        </td>
                        <td>
                            @if($l->kategori === 'balita')
                                BB: <strong>{{ $data['berat_badan'] ?? '-' }}</strong> kg | 
                                TB: <strong>{{ $data['tinggi_badan'] ?? '-' }}</strong> cm<br>
                                LIKA: {{ $data['lingkar_kepala'] ?? '-' }} cm | 
                                LILA: {{ $data['lila'] ?? '-' }} cm<br>
                                Ukur: {{ ($data['cara_ukur'] ?? '') == 'berdiri' ? 'Berdiri' : 'Telentang' }} | 
                                ASI: {{ !empty($data['asi_eksklusif']) ? 'Ya' : 'Tidak' }}
                            @else
                                BB: <strong>{{ $data['bb'] ?? '-' }}</strong> kg | 
                                TB: <strong>{{ $data['tinggi_badan'] ?? '-' }}</strong> cm<br>
                                LiLA: {{ $data['lila'] ?? '-' }} cm | 
                                LP: {{ $data['lingkar_perut'] ?? '-' }} cm<br>
                                HB: {{ $data['hemoglobin'] ?? '-' }} | 
                                TD: {{ $data['tekanan_darah'] ?? '-' }}<br>
                                TFU: {{ $data['tfu'] ?? '-' }} cm | 
                                TBJ: {{ $data['taksiran_bb_janin'] ?? '-' }} gr
                            @endif
                        </td>
                        <td>
                            @if($l->kategori === 'balita')
                                <span class="badge-{{ $badgeClass($gizi['bb_u'] ?? '') }}">BB/U: {{ $gizi['bb_u'] ?? '-' }}</span><br>
                                <span class="badge-{{ $badgeClass($gizi['tb_u'] ?? '') }}">TB/U: {{ $gizi['tb_u'] ?? '-' }}</span><br>
                                <span class="badge-{{ $badgeClass($gizi['bb_tb'] ?? '') }}">BB/TB: {{ $gizi['bb_tb'] ?? '-' }}</span>
                            @else
                                <span class="badge-{{ $badgeClass($gizi['kek'] ?? '') }}">KEK: {{ $gizi['kek'] ?? '-' }}</span><br>
                                <span class="badge-{{ $badgeClass($gizi['anemia'] ?? '') }}">HB: {{ $gizi['anemia'] ?? '-' }}</span><br>
                                <span class="badge-{{ $badgeClass($gizi['td'] ?? '') }}">TD: {{ $gizi['td'] ?? '-' }}</span>
                            @endif
                        </td>
                        <td>
                            @if(!empty($tindakan) && is_array($tindakan))
                                <ul style="margin: 0; padding-left: 10px;">
                                    @foreach($tindakan as $t)
                                        <li>{{ $t }}</li>
                                    @endforeach
                                </ul>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <strong style="color: #065f46;">✅ Valid</strong><br>
                            {{ $l->validator->name ?? '-' }}<br>
                            {{ $l->tanggal_validasi ? $l->tanggal_validasi->format('d/m/y H:i') : '-' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div style="text-align: center; padding: 40px; color: #9ca3af;">
            Tidak ada data laporan untuk periode ini.
        </div>
    @endif

    <!-- FOOTER -->
    <div class="footer">
        <p>Dicetak otomatis dari Sistem Informasi PETANGBULA &copy; {{ date('Y') }}</p>
    </div>

</body>
</html>