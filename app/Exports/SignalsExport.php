<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SignalsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $signals;

    public function __construct($signals)
    {
        $this->signals = $signals;
    }

    public function collection()
    {
        return $this->signals;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Location',
            'Waste Types',
            'Volume',
            'Reporter',
            'Status',
            'Date'
        ];
    }

    public function map($signal): array
    {
        return [
            $signal->id,
            $signal->location,
            $signal->wasteTypes->pluck('name')->join(', '),
            $signal->volume . ' m³',
            $signal->creator->first_name . ' ' . $signal->creator->last_name,
            $signal->status,
            $signal->signal_date->format('Y-m-d H:i')
        ];
    }
} 