@php
    $groupedRecords = $records->groupBy(function($item) {
        return ($item->student && $item->student->schoolClass) 
            ? $item->student->schoolClass->name 
            : 'Tanpa Kelas/Data Tidak Valid';
    })->sortKeys(); 
@endphp

<!DOCTYPE html>
<html>
<head>
    <title>Laporan Presensi Siswa</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            line-height: 1.6;
        }
        /* Kop Surat */
        .header {
            text-align: center;
            border-bottom: 2px solid #444;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            text-transform: uppercase;
            font-size: 20px;
        }
        .header p {
            margin: 5px 0;
            font-size: 12px;
        }
        
        /* Judul Laporan */
        .report-title {
            text-align: center;
            margin-bottom: 30px;
        }
        .report-title h2 {
            margin: 0;
            color: #2c3e50;
        }
        .report-title p {
            font-size: 13px;
            color: #7f8c8d;
        }

        /* Styling Tabel */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 12px;
            page-break-inside: auto;
        }
        tr{
            page-break-inside: avoid;
            page-break-after: auto;
        }
        th {
            background-color: #34495e;
            color: white;
            text-transform: uppercase;
            padding: 10px;
            border: 1px solid #2c3e50;
        }
        td {
            padding: 8px;
            border: 1px solid #bdc3c7;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        /* Badge Status */
        .status {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
        }
        .status-hadir { color: #27ae60; }
        .status-terlambat { color: #f39c12; }

        .class-title {
            background-color: #ecf0f1;
            padding: 5px 10px;
            border-left: 5px solid #3498db;
            margin-top: 20px;
        }

    </style>
</head>
<body>

    <div class="header">
        <h1>Sistem Informasi Presensi Sekolah</h1>
        <p>Jl. Pendidikan No. 123, Kota Anda | Telp: (021) 123456</p>
    </div>

    <div class="report-title">
        <h2>LAPORAN KEHADIRAN SISWA</h2>
        
        <p>Periode: 
            <strong>
                {{ $startDate ? \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') : 'Awal' }} 
                s/d 
                {{ $endDate ? \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') : 'Sekarang' }}
            </strong>
        </p>
        
        <p>Tanggal Cetak: {{ now()->translatedFormat('d F Y H:i') }}</p>
    </div>

    @foreach($records->groupBy('student.schoolClass.name') as $className => $attendances)
        <div class="class-title">
            <h3>Kelas: {{ $className }}</h3>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="50%">Nama Siswa</th>
                    <th width="20%">Waktu Presensi</th>
                    <th width="25%">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendances as $index => $record)
                <tr>
                    <td align="center">{{ $index + 1 }}</td>
                    <td>{{ $record->student->name }}</td>
                    <td align="center">{{ $record->created_at->format('H:i:s') }}</td>
                    <td align="center">
                        <span class="status status-{{ strtolower($record->status) }}">
                            {{ $record->status }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Jangan kasih page break di kelas terakhir --}}
        @if (!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach

</body>
</html>