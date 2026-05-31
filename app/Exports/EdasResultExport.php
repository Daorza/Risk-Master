<?php

namespace App\Exports;

use App\Models\Assessment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\EdasResult;

class EdasResultExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public function __construct(private readonly Assessment $assessment) {}

    public function collection()
    {
        return $this->assessment->rankedResults()
            ->with('alternative:id,name,description')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Rank',
            'Alternatif',
            'Deskripsi',
            'PDA',
            'NDA',
            'SP',
            'SN',
            'NSP',
            'NSN',
            'Appraisal Scrore (AS)',
            'Kualitas',
        ];
    }

    public function map($row): array
    {
        return [
            $row->rank,
            $row->alternative?->name,
            $row->alternative?->description,
            number_format($row->pda, 6),
            number_format($row->nda, 6),
            number_format($row->sp, 6),
            number_format($row->sn, 6),
            number_format($row->nsp, 6),
            number_format($row->nsn, 6),
            number_format($row->appraisal_score, 6),
            $row->quality_label,
        ];
    }

    public function title(): string
    {
        return 'Hasil EDAS';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
