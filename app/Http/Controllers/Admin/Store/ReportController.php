<?php

namespace App\Http\Controllers\Admin\Store;

use App\Services\Store\ReportExportService;
use App\Services\Store\ReportService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{

    public function __construct(
        protected ReportService $reportService,
        protected ReportExportService $reportExportService,
    ) {
    }

    /**
     * Generates sales report (minimal & detailed)
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function sales(Request $request)
    {
        $data = $this->reportService->generateSalesReport($request->get('store'), $request->get('start'), $request->get('end'), $request->get('type'));
        $filters = [
            'start' => $request->input('start'),
            'end' => $request->input('end'),
            'store' => $request->input('store'),
            'type' => $request->get('type')
        ];
        if ($filters['type'] == 'minimal') {
            return view('backend.store.report.sales.minimal', compact('data','filters'));
        } else {
            return view('backend.store.report.sales.detailed', compact('data','filters'));
        }
    }

    /**
     * Generates inventory report
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function inventory(Request $request)
    {
        $store = $request->get('store');
        $data = $this->reportService->generateInventoryReport($store);
        return view('backend.store.report.inventory', compact('data','store'));
    }

    /**
     * Exports the inventory report as a downloadable XLSX file with auto-sized columns.
     */
    public function exportInventory(Request $request): BinaryFileResponse
    {
        $store = (int) $request->get('store', 0);
        $data = $this->reportService->generateInventoryReport($store);

        $filename = 'inventory-report-'.now()->format('Y-m-d').'.xlsx';
        $path = $this->createTempXlsxPath();

        $this->reportExportService->writeInventoryReport($data, $path);

        return response()->download($path, $filename)->deleteFileAfterSend();
    }

    /**
     * Exports a sales report (detailed or minimal) as a downloadable XLSX file with auto-sized columns.
     */
    public function exportSales(Request $request): BinaryFileResponse
    {
        $type = $request->get('type') === 'minimal' ? 'minimal' : 'detailed';
        $filters = [
            'start' => $request->input('start'),
            'end' => $request->input('end'),
            'store' => $request->input('store'),
            'type' => $type,
        ];
        $data = $this->reportService->generateSalesReport($filters['store'], $filters['start'], $filters['end'], $type);

        $filename = $this->buildSalesFilename($filters);
        $path = $this->createTempXlsxPath();

        if ($type === 'minimal') {
            $this->reportExportService->writeMinimalSalesReport($data, $filters, $path);
        } else {
            $this->reportExportService->writeDetailedSalesReport($data, $filters, $path);
        }

        return response()->download($path, $filename)->deleteFileAfterSend();
    }

    /**
     * Builds a temp file path used to stage the XLSX before sending it to the browser.
     */
    private function createTempXlsxPath(): string
    {
        $path = tempnam(sys_get_temp_dir(), 'report_');
        if ($path === false) {
            throw new \RuntimeException('Unable to create temporary file for report export.');
        }

        return $path;
    }

    /**
     * @param array<string, mixed> $filters
     */
    private function buildSalesFilename(array $filters): string
    {
        $start = $filters['start'] ?: now()->format('Y-m-d');
        $end = $filters['end'] ?: now()->format('Y-m-d');
        $store = ((int) ($filters['store'] ?? 0) === 0) ? 'all' : $filters['store'];

        return "sales-report-{$filters['type']}-store{$store}-{$start}-to-{$end}.xlsx";
    }

}
