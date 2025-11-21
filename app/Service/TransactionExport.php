<?php

namespace App\Service;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $data;
    protected $filters;

    public function __construct($data, $filters)
    {
        $this->data = $data;
        $this->filters = $filters;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Transaction ID',
            'Customer Name',
            'Membership',
            'Total Bill',
            'Total Payment',
            'Payment Method',
            'Cashier',
            'Status',
            'Transaction Date'
        ];
    }

    public function map($order): array
    {
        return [
            $order->order_id,
            $order->customer->first_name . ' ' . $order->customer->last_name,
            $order->customer->member->membership ?? 'Non-Member',
            $order->total_amount,
            $order->payment->amount,
            ucfirst($order->payment->payment_method),
            $order->userId->username,
            $this->getStatusText($order->status),
            $order->order_date
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Set header style
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E6E6FA']
            ]
        ]);

        // Auto size columns
        foreach(range('A','I') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Transactions';
    }

    private function getStatusText($status)
    {
        switch ($status) {
            case 'completed':
                return 'Complete';
            case 'pending':
                return 'Pending';
            default:
                return 'Failed';
        }
    }
}