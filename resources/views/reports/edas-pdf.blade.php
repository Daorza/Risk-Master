<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan EDAS — {{ $assessment->title }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #1a202c; }
        .header { background: #1e3a5f; color: white; padding: 16px 24px; margin-bottom: 20px; }
        .header h1 { font-size: 16px; font-weight: bold; }
        .header p { font-size: 10px; opacity: 0.8; margin-top: 2px; }
        .meta { padding: 0 24px; margin-bottom: 16px; }
        .meta table { width: 100%; }
        .meta td { padding: 3px 0; font-size: 10px; }
        .meta td:first-child { color: #6b7280; width: 140px; }
        h2 { font-size: 12px; font-weight: bold; color: #1e3a5f; padding: 0 24px; margin-bottom: 8px; }
        .table-wrap { padding: 0 24px; margin-bottom: 20px; }
        table.data { width: 100%; border-collapse: collapse; font-size: 9px; }
        table.data th { background: #1e3a5f; color: white; padding: 6px 8px; text-align: center; }
        table.data th.left { text-align: left; }
        table.data td { padding: 5px 8px; border-bottom: 1px solid #e5e7eb; text-align: center; }
        table.data td.left { text-align: left; }
        table.data tr:nth-child(even) { background: #f9fafb; }
        table.data tr.top { background: #dbeafe; }
        .formula-box { margin: 0 24px 20px; background: #f8fafc; border: 1px solid #e2e8f0;
                        border-radius: 6px; padding: 10px 14px; }
        .formula-box p { font-size: 9px; line-height: 1.6; color: #4b5563; }
        .footer { margin-top: 20px; padding: 12px 24px; border-top: 1px solid #e5e7eb;
                  font-size: 8px; color: #9ca3af; display: flex; justify-content: space-between; }
    </style>
</head>
<body>

<div class="header">
    <h1>Laporan Hasil EDAS — {{ $assessment->title }}</h1>
    <p>Sistem Pendukung Keputusan Mitigasi Risiko Keamanan Informasi</p>
</div>

<div class="meta">
    <table>
        <tr>
            <td>Judul Assessment</td>
            <td>: <strong>{{ $assessment->title }}</strong></td>
        </tr>
        <tr>
            <td>Pemilik</td>
            <td>: {{ $assessment->owner?->name ?? '—' }}</td>
        </tr>
        <tr>
            <td>Status</td>
            <td>: {{ $assessment->status_label }}</td>
        </tr>
        <tr>
            <td>Tanggal Kalkulasi</td>
            <td>: {{ $results->first()?->calculated_at?->format('d M Y, H:i') ?? now()->format('d M Y, H:i') }}</td>
        </tr>
        <tr>
            <td>Jumlah Alternatif</td>
            <td>: {{ $results->count() }}</td>
        </tr>
        <tr>
            <td>Jumlah Kriteria</td>
            <td>: {{ $criteria->count() }}</td>
        </tr>
    </table>
</div>

<h2>Kriteria Evaluasi</h2>
<div class="table-wrap">
    <table class="data">
        <thead>
            <tr>
                <th class="left">Nama Kriteria</th>
                <th>Tipe</th>
                <th>Bobot</th>
                <th class="left">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($criteria as $c)
            <tr>
                <td class="left">{{ $c->name }}</td>
                <td>{{ strtoupper($c->type) }}</td>
                <td>{{ $c->weight_percent }}</td>
                <td class="left">{{ Str::limit($c->description ?? '—', 80) }}</td>
            </tr>
            @endforeach
            <tr>
                <td class="left"><strong>Total</strong></td>
                <td></td>
                <td><strong>{{ number_format($criteria->sum('weight') * 100, 1) }}%</strong></td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>

<h2>Peringkat Hasil EDAS</h2>
<div class="table-wrap">
    <table class="data">
        <thead>
            <tr>
                <th>Rank</th>
                <th class="left">Alternatif</th>
                <th>PDA</th>
                <th>NDA</th>
                <th>SP</th>
                <th>SN</th>
                <th>NSP</th>
                <th>NSN</th>
                <th>AS Score</th>
                <th>Kualitas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $r)
            <tr class="{{ $r->rank === 1 ? 'top' : '' }}">
                <td><strong>{{ $r->rank }}</strong></td>
                <td class="left">{{ $r->alternative?->name }}</td>
                <td>{{ number_format($r->pda, 4) }}</td>
                <td>{{ number_format($r->nda, 4) }}</td>
                <td>{{ number_format($r->sp, 4) }}</td>
                <td>{{ number_format($r->sn, 4) }}</td>
                <td>{{ number_format($r->nsp, 4) }}</td>
                <td>{{ number_format($r->nsn, 4) }}</td>
                <td><strong>{{ number_format($r->appraisal_score, 4) }}</strong></td>
                <td>{{ $r->quality_label }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@if($results->isNotEmpty())
@php $top = $results->first(); @endphp
<h2>Rekomendasi</h2>
<div class="formula-box">
    <p>
        Berdasarkan metode EDAS, alternatif terbaik adalah
        <strong>{{ $top->alternative?->name }}</strong>
        dengan Appraisal Score tertinggi sebesar
        <strong>{{ number_format($top->appraisal_score, 4) }}</strong>
        ({{ $top->quality_label }}).
    </p>
    @if($top->alternative?->description)
        <p style="margin-top:4px; color:#6b7280;">{{ $top->alternative->description }}</p>
    @endif
</div>
@endif

<div class="formula-box">
    <p><strong>Keterangan Formula EDAS:</strong></p>
    <p>
        AV_j = Rata-rata nilai tiap kriteria &nbsp;|&nbsp;
        PDA = Jarak positif dari rata-rata &nbsp;|&nbsp;
        NDA = Jarak negatif dari rata-rata
    </p>
    <p>
        SP = Σ(w × PDA) &nbsp;|&nbsp;
        SN = Σ(w × NDA) &nbsp;|&nbsp;
        NSP = SP / max(SP) &nbsp;|&nbsp;
        NSN = 1 − SN / max(SN) &nbsp;|&nbsp;
        AS = (NSP + NSN) / 2
    </p>
</div>

<div class="footer">
    <span>Risk Master — SPK Mitigasi Risiko Keamanan Informasi</span>
    <span>Dicetak: {{ now()->format('d M Y H:i') }}</span>
</div>

</body>
</html>
