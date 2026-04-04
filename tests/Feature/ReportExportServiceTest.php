<?php

namespace Tests\Feature;

use App\Services\Store\ReportExportService;
use OpenSpout\Reader\XLSX\Reader;
use Tests\TestCase;

class ReportExportServiceTest extends TestCase
{
    private string $tempPath = '';

    protected function setUp(): void
    {
        parent::setUp();
        $this->tempPath = tempnam(sys_get_temp_dir(), 'report_test_');
    }

    protected function tearDown(): void
    {
        if ($this->tempPath !== '' && file_exists($this->tempPath)) {
            unlink($this->tempPath);
        }
        parent::tearDown();
    }

    public function test_inventory_report_writes_xlsx_with_headers_rows_and_totals(): void
    {
        $service = new ReportExportService();
        $data = [
            'totals' => [
                'cost' => 125.50,
                'value' => 250.75,
            ],
            'products' => [
                10 => [
                    'name' => 'Premium Widget',
                    'category' => 'accessory',
                    'cost' => 5.00,
                    'price' => 10.00,
                    'stock' => 15,
                ],
                11 => [
                    'name' => 'Battery Pack',
                    'category' => 'battery',
                    'cost' => 7.25,
                    'price' => 14.50,
                    'stock' => 7,
                ],
            ],
        ];

        $service->writeInventoryReport($data, $this->tempPath);

        $this->assertFileExists($this->tempPath);
        $this->assertGreaterThan(0, filesize($this->tempPath));

        $sheets = $this->readSheets($this->tempPath);
        $this->assertCount(1, $sheets);

        $rows = $sheets[0]['rows'];
        $this->assertSame(['Product', 'Category', 'Cost', 'Price', 'Stock', 'Total Cost', 'Total Value'], $rows[0]);

        $rowsByName = [];
        foreach (array_slice($rows, 1) as $row) {
            $rowsByName[$row[0]] = $row;
        }
        $this->assertArrayHasKey('Premium Widget', $rowsByName);
        $this->assertSame('Accessory', $rowsByName['Premium Widget'][1]);
        $this->assertSame('5.00', $rowsByName['Premium Widget'][2]);
        $this->assertSame('15', (string) $rowsByName['Premium Widget'][4]);
        $this->assertSame('75.00', $rowsByName['Premium Widget'][5]);

        $this->assertArrayHasKey('Totals', $rowsByName);
        $this->assertSame('125.50', $rowsByName['Totals'][5]);
        $this->assertSame('250.75', $rowsByName['Totals'][6]);
    }

    public function test_inventory_report_auto_sizes_columns_to_longest_value(): void
    {
        $service = new ReportExportService();
        $data = [
            'totals' => ['cost' => 0, 'value' => 0],
            'products' => [
                1 => [
                    'name' => 'A Very Long Product Name That Exceeds The Header Width',
                    'category' => 'accessory',
                    'cost' => 1.00,
                    'price' => 2.00,
                    'stock' => 1,
                ],
            ],
        ];

        $service->writeInventoryReport($data, $this->tempPath);

        $widths = $this->readColumnWidths($this->tempPath);
        $this->assertNotEmpty($widths, 'expected at least one column width to be set');

        $productColumnWidth = $widths[0] ?? null;
        $this->assertNotNull($productColumnWidth);
        $this->assertGreaterThan(
            mb_strlen('Product') + 1,
            $productColumnWidth,
            'product column should be widened to fit the long product name, not just the header text'
        );
    }

    public function test_detailed_sales_report_writes_multiple_sheets(): void
    {
        $service = new ReportExportService();
        $data = $this->sampleDetailedSalesData();
        $filters = ['start' => '2026-01-01', 'end' => '2026-01-31', 'store' => 1, 'type' => 'detailed'];

        $service->writeDetailedSalesReport($data, $filters, $this->tempPath);

        $sheets = $this->readSheets($this->tempPath);
        $sheetNames = array_column($sheets, 'name');
        $this->assertSame(['Summary', 'Employee Sales', 'Hourly Sales', 'Bottles Sold'], $sheetNames);

        $summaryRows = $sheets[0]['rows'];
        $summaryByKey = [];
        foreach ($summaryRows as $row) {
            if (isset($row[0]) && $row[0] !== '') {
                $summaryByKey[$row[0]] = $row[1] ?? null;
            }
        }
        $this->assertSame('2026-01-01 to 2026-01-31', $summaryByKey['Report Period']);
        $this->assertSame('Hudsonville', $summaryByKey['Store']);
        $this->assertSame('500.00', $summaryByKey['Gross Sales']);
        $this->assertSame('5', (string) $summaryByKey['Total Orders']);

        $employeeRows = $sheets[1]['rows'];
        $this->assertSame(['Employee', 'Sales'], $employeeRows[0]);
        $employeeByName = [];
        foreach (array_slice($employeeRows, 1) as $row) {
            $employeeByName[$row[0]] = $row[1];
        }
        $this->assertSame('300.00', $employeeByName['Alice']);
        $this->assertSame('200.00', $employeeByName['Bob']);
    }

    public function test_minimal_sales_report_writes_summary_sheet(): void
    {
        $service = new ReportExportService();
        $data = ['subtotal' => 100.00, 'gross' => 106.00];
        $filters = ['start' => '2026-02-01', 'end' => '2026-02-28', 'store' => 0, 'type' => 'minimal'];

        $service->writeMinimalSalesReport($data, $filters, $this->tempPath);

        $sheets = $this->readSheets($this->tempPath);
        $this->assertCount(1, $sheets);
        $this->assertSame('Sales Summary', $sheets[0]['name']);

        $rows = $sheets[0]['rows'];
        $byKey = [];
        foreach ($rows as $row) {
            if (isset($row[0]) && $row[0] !== '') {
                $byKey[$row[0]] = $row[1] ?? null;
            }
        }
        $this->assertSame('All Stores', $byKey['Store']);
        $this->assertSame('106.00', $byKey['Gross Sales']);
        $this->assertSame('6.00', $byKey['Sales Tax']);
    }

    /**
     * @return array<int, array{name: string, rows: array<int, array<int, mixed>>}>
     */
    private function readSheets(string $path): array
    {
        $reader = new Reader();
        $reader->open($path);

        $sheets = [];
        foreach ($reader->getSheetIterator() as $sheet) {
            $rows = [];
            foreach ($sheet->getRowIterator() as $row) {
                $rows[] = $row->toArray();
            }
            $sheets[] = [
                'name' => $sheet->getName(),
                'rows' => $rows,
            ];
        }

        $reader->close();

        return $sheets;
    }

    /**
     * Reads the cols/width xml from the first sheet of the workbook by extracting
     * the worksheet xml from the xlsx zip and parsing the <col> elements. Returns a
     * map of column index (0-based) → width.
     *
     * @return array<int, float>
     */
    private function readColumnWidths(string $path): array
    {
        $zip = new \ZipArchive();
        $this->assertTrue($zip->open($path) === true, 'failed to open xlsx as zip');

        $xml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();
        $this->assertNotFalse($xml, 'sheet1.xml not found in xlsx');

        $widths = [];
        $previous = libxml_use_internal_errors(true);
        $doc = simplexml_load_string($xml);
        libxml_use_internal_errors($previous);
        $this->assertNotFalse($doc);

        foreach ($doc->cols->col ?? [] as $col) {
            $min = (int) $col['min'];
            $max = (int) $col['max'];
            $width = (float) $col['width'];
            for ($i = $min; $i <= $max; $i++) {
                $widths[$i - 1] = $width;
            }
        }

        ksort($widths);

        return $widths;
    }

    /**
     * @return array<string, mixed>
     */
    private function sampleDetailedSalesData(): array
    {
        return [
            'gross' => 500.00,
            'net' => 350.00,
            'productCost' => 150.00,
            'productSales' => 400.00,
            'liquidSales' => 100.00,
            'totalMl' => 200,
            'liquids' => [
                10 => 4,
                30 => 2,
            ],
            'subtotal' => 471.70,
            'cash' => 200.00,
            'credit' => 300.00,
            'totalOrders' => 5,
            'averageOrder' => 100.00,
            'discounts' => 25.00,
            'employee' => [
                'Alice' => 300.00,
                'Bob' => 200.00,
            ],
            'hourly' => [
                '09:XXam' => 0,
                '10:XXam' => 100.00,
                '11:XXam' => 0,
                '12:XXpm' => 200.00,
                '01:XXpm' => 200.00,
                '02:XXpm' => 0,
                '03:XXpm' => 0,
                '04:XXpm' => 0,
                '05:XXpm' => 0,
                '06:XXpm' => 0,
                '07:XXpm' => 0,
                '08:XXpm' => 0,
            ],
            'customers' => [
                '09:XXam' => 0,
                '10:XXam' => 1,
                '11:XXam' => 0,
                '12:XXpm' => 2,
                '01:XXpm' => 2,
                '02:XXpm' => 0,
                '03:XXpm' => 0,
                '04:XXpm' => 0,
                '05:XXpm' => 0,
                '06:XXpm' => 0,
                '07:XXpm' => 0,
                '08:XXpm' => 0,
            ],
        ];
    }
}
