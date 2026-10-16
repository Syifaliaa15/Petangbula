<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Posyandu</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #ec4899;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header h1 {
            font-size: 16px;
            color: #831843;
            font-weight: bold;
            margin-bottom: 4px;
        }
        .header p {
            font-size: 10px;
            color: #6b7280;
        }
        .info-box {
            background: #fce7f3;
            padding: 8px 12px;
            border-radius: 6px;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            font-size: 9px;
        }
        .info-box strong { color: #831843; }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5px;
        }
        th {
            background: #ec4899;
            color: #fff;
            padding: 5px 3px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #be185d;
            font-size: 7px;
        }
        td {
            padding: 4px 3px;
            border: 1px solid #fbcfe8;
            vertical-align: top;
        }
        tr:nth-child(even) td { background: #fff5f8; }
        .badge-baik    { color: #065f46; font-weight: bold; }
        .badge-kurang  { color: #92400e; font-weight: bold; }
        .badge-buruk   { color: #991b1b; font-weight: bold; }
        .badge-lebih   { color: #1d4ed8; font-weight: bold; }
        .badge-normal  { color: #065f46; font-weight: bold; }
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #fbcfe8;
            text-align: center;
            font-size: 8px;
            color: #9ca3af;
        }
        .text-center { text-align: center; }
    </style>
</head>
<body>

    <!-- HEADER -->
    <div class="header">
        <h1>LAPORAN POSYANDU</h1>
        <p>Desa Sindangpanon - Sistem Informasi PETANGBULA</p>
    </div>

    <!-- INFO BOX -->
    <div class="info-box">
        <div><strong>Periode:</strong> {{ $bulanLabel }} {{ $tahun }}</div>
        <div><strong>Posyandu:</strong> {{ $posyanduLabel }}</div>
        <div><strong>Total Data:</strong> {{ $laporans->count() }}</div>
        <div><strong>Dicetak:</strong> {{ $tanggalCetak }}</div>
    </div>

    <!-- TABEL -->
    @if($laporans->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 20px;">No</th>
                    <th style="width: 40px;">Kategori</th>
                    <th style="width: 60px;">Posyandu</th>
                    <th style="width: 90px;">Identitas</th>
                    <th style="width: 70px;">Data Periksa</th>
                    <th style="width: 80px;">Status Gizi</th>
                    <th style="width: 60px;">Imunisasi/Vitamin</th>
                    <th style="width: 60px;">Validasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($laporans as $index => $l)
                    @php
                        $id   = $l->identitas ?? [];
                        $data = $l->data_pemeriksaan ?? [];
                        $gizi = $l->status_gizi ?? [];
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
                                {{ $id['umur_bulan'] ?? '-' }} bln · 
                                {{ ($id['jenis_kelamin'] ?? '-') === 'L' ? 'L' : 'P' }}<br>
                                Ibu: {{ $id['nama_ibu'] ?? '-' }}
                            @else
                                {{ $id['umur'] ?? '-' }} thn · Hamil Ke-{{ $id['hamil_ke'] ?? '-' }}<br>
                                Suami: {{ $id['nama_suami'] ?? '-' }}
                            @endif
                        </td>
                        <td>
                            @if($l->kategori === 'balita')
                                BB: <strong>{{ $data['berat_badan'] ?? '-' }}</strong> kg<br>
                                TB: <strong>{{ $data['tinggi_badan'] ?? '-' }}</strong> cm<br>
                                LK: {{ $data['lingkar_kepala'] ?? '-' }} cm
                            @else
                                BB: <strong>{{ $data['bb'] ?? '-' }}</strong> kg<br>
                                LiLA: <strong>{{ $data['lila'] ?? '-' }}</strong> cm<br>
                                HB: {{ $data['hemoglobin'] ?? '-' }} g/dL<br>
                                TD: {{ $data['tekanan_darah'] ?? '-' }}
                            @endif
                        </td>
                        <td>
                            @if($l->kategori === 'balita')
                                <span class="badge-{{ str_contains(strtolower($gizi['bb_u'] ?? ''), 'baik') ? 'baik' : (str_contains(strtolower($gizi['bb_u'] ?? ''), 'buruk') ? 'buruk' : (str_contains(strtolower($gizi['bb_u'] ?? ''), 'lebih') ? 'lebih' : 'kurang')) }}">
                                    BB/U: {{ $gizi['bb_u'] ?? '-' }}
                                </span><br>
                                <span class="badge-{{ str_contains(strtolower($gizi['tb_u'] ?? ''), 'normal') ? 'normal' : 'kurang' }}">
                                    TB/U: {{ $gizi['tb_u'] ?? '-' }}
                                </span><br>
                                <span class="badge-{{ str_contains(strtolower($gizi['bb_tb'] ?? ''), 'normal') ? 'normal' : 'kurang' }}">
                                    BB/TB: {{ $gizi['bb_tb'] ?? '-' }}
                                </span>
                            @else
                                <span class="badge-{{ str_contains(strtolower($gizi['kek'] ?? ''), 'tidak') ? 'normal' : 'kurang' }}">
                                    KEK: {{ $gizi['kek'] ?? '-' }}
                                </span><br>
                                <span class="badge-{{ str_contains(strtolower($gizi['anemia'] ?? ''), 'tidak') ? 'normal' : 'buruk' }}">
                                    HB: {{ $gizi['anemia'] ?? '-' }}
                                </span><br>
                                <span class="badge-{{ str_contains(strtolower($gizi['td'] ?? ''), 'normal') ? 'normal' : 'kurang' }}">
                                    TD: {{ $gizi['td'] ?? '-' }}
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($l->kategori === 'balita')
                                @if(!empty($l->imunisasi))
                                    {{ implode(', ', $l->imunisasi) }}
                                @else
                                    -
                                @endif
                                @if(!empty($data['vitamin_a']))
                                    <br><strong style="color: #f59e0b;">💊 Vit A: Ya</strong>
                                @endif
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