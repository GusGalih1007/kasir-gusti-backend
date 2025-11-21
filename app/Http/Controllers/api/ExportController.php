<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Order;
use Maatwebsite\Excel\Facades\Excel;
use App\Service\TransactionExport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use ILluminate\Support\Facades\Log;

class ExportController extends Controller
{
    /**
     * Export to PDF
     */
    public function exportTransactionsPdf(Request $request)
    {
        try {
            $data = $this->getFilteredData($request);
            $filters = $this->getFilters($request);

            $pdf = PDF::loadView('export.transaction-pdf', [
                'data' => $data,
                'filters' => $filters,
                'totalAmount' => $data->sum('total_amount'),
                'totalPayment' => $data->sum('payment.amount'),
                'totalTransactions' => $data->count()
            ])->setPaper('a4', 'landscape');

            $filename = 'transactions_' . date('Y_m_d_His') . '.pdf';

            return $pdf->download($filename);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to export PDF: ' . $e->getMessage());
        }
    }

    /**
     * Export to Excel
     */
    public function exportTransactionsExcel(Request $request)
    {
        try {
            $data = $this->getFilteredData($request);
            $filters = $this->getFilters($request);

            $filename = 'transactions_' . date('Y_m_d_His') . '.xlsx';

            return Excel::download(new TransactionExport($data, $filters), $filename);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to export Excel: ' . $e->getMessage());
        }
    }

    /**
     * Print Report
     */
    public function exportTransactionsPrint(Request $request)
    {
        try {
            $data = $this->getFilteredData($request);
            $filters = $this->getFilters($request);

            return view('export.transaction-print', [
                'data' => $data,
                'filters' => $filters,
                'totalAmount' => $data->sum('total_amount'),
                'totalPayment' => $data->sum('payment.amount'),
                'totalTransactions' => $data->count()
            ]);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to generate print view: ' . $e->getMessage());
        }
    }

    /**
     * Export single transaction receipt PDF
     */
    public function exportReceiptPdf($orderId)
    {
        try {
            $order = Order::with([
                'customer.member',
                'userCreator',
                'detail.variant.product.category',
                'payment'
            ])->findOrFail($orderId);

            // Convert order_date to Carbon instance if it's string
            if (is_string($order->order_date)) {
                $order->order_date = Carbon::parse($order->order_date);
            }

            $pdf = PDF::loadView('export.receipt-pdf', compact('order'))
                ->setPaper([0, 0, 226.77, 700], 'portrait'); // 80mm receipt paper size

            $filename = 'receipt_' . $order->order_id . '_' . date('Y_m_d_His') . '.pdf';

            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error('Receipt export error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to export receipt: ' . $e->getMessage());
        }
    }

    /**
     * Get filtered data based on request
     */
    private function getFilteredData(Request $request)
    {
        $query = Order::with(['customer.member', 'userId', 'payment']);

        // Apply date filters
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('order_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('order_date', '<=', $request->end_date);
        }

        if ($request->has('payment_method') && $request->payment_method) {
            $query->whereHas('payment', function ($q) use ($request) {
                $q->where('payment_method', $request->payment_method);
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        return $query->orderBy('order_date', 'desc')->get();
    }

    /**
     * Get filter information for export
     */
    private function getFilters(Request $request)
    {
        $filters = [];

        if ($request->has('start_date') && $request->start_date) {
            $filters['start_date'] = $request->start_date;
        }

        if ($request->has('end_date') && $request->end_date) {
            $filters['end_date'] = $request->end_date;
        }

        if ($request->has('payment_method') && $request->payment_method) {
            $filters['payment_method'] = $request->payment_method;
        }

        if ($request->has('status') && $request->status) {
            $filters['status'] = $request->status;
        }

        return $filters;
    }

    /**
     * Export sales report by date range
     */
    public function exportSalesReport(Request $request)
    {
        try {
            $data = $this->getSalesReportData($request);
            $filters = $this->getFilters($request);

            $pdf = PDF::loadView('export.sales-report-pdf', [
                'data' => $data,
                'filters' => $filters,
                'summary' => $this->getSalesSummary($data)
            ]);

            $filename = 'sales_report_' . date('Y_m_d_His') . '.pdf';

            return $pdf->download($filename);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to export sales report: ' . $e->getMessage());
        }
    }

    /**
     * Get sales report data
     */
    private function getSalesReportData(Request $request)
    {
        $query = Order::with(['customer', 'payment', 'detail.variant.product'])
            ->where('status', 'completed');

        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('order_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('order_date', '<=', $request->end_date);
        }

        return $query->orderBy('order_date')->get();
    }

    /**
     * Get sales summary
     */
    private function getSalesSummary($data)
    {
        return [
            'total_sales' => $data->sum('payment.amount'),
            'total_orders' => $data->count(),
            'average_order' => $data->count() > 0 ? $data->sum('payment.amount') / $data->count() : 0,
            'cash_sales' => $data->where('payment.payment_method', 'cash')->sum('payment.amount'),
            'midtrans_sales' => $data->where('payment.payment_method', 'midtrans')->sum('payment.amount')
        ];
    }
}
