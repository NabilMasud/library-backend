<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ActivityLogsExport implements FromCollection, WithHeadings
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Activity::with('causer', 'subject')->latest();

        if ($this->filters['log_name'] ?? false) {
            $query->where('log_name', 'like', '%' . $this->filters['log_name'] . '%');
        }

        if ($this->filters['causer'] ?? false) {
            $query->whereHas('causer', function ($q) {
                $q->where('name', 'like', '%' . $this->filters['causer'] . '%');
            });
        }

        if ($this->filters['start_date'] && $this->filters['end_date']) {
            $query->whereBetween('created_at', [$this->filters['start_date'], $this->filters['end_date']]);
        } elseif ($this->filters['start_date']) {
            $query->where('created_at', '>=', $this->filters['start_date']);
        } elseif ($this->filters['end_date']) {
            $query->where('created_at', '<=', $this->filters['end_date']);
        }

        return $query->get()->map(function ($log) {
            return [
                'Log Name' => $log->log_name,
                'Description' => $log->description,
                'Subject' => $log->subject?->name ?? ($log->subject_type ?? '-'),
                'Causer' => $log->causer?->name ?? '-',
                'Properties' => json_encode($log->properties),
                'Date' => $log->created_at->toDateTimeString(),
            ];
        });
    }

    public function headings(): array
    {
        return ['Log Name', 'Description', 'Subject', 'Causer', 'Properties', 'Date'];
    }
}
