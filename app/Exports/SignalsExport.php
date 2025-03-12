<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class SignalsExport implements FromCollection, WithHeadings
{
    protected $signals;

    public function __construct(Collection $signals)
    {
        $this->signals = $signals;
    }

    public function collection()
    {
        return $this->signals->map(function ($signal) {
            return [
                'id' => $signal->id,
                'location' => $signal->location,
                'waste_types' => $signal->wasteTypes->pluck('name')->join(', '),
                'volume' => $signal->volume . ' m³',
                'reporter' => $signal->creator->first_name . ' ' . $signal->creator->last_name,
                'status' => $signal->status,
                'date' => $signal->signal_date->format('Y-m-d H:i')
            ];
        });
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
} 