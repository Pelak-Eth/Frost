<?php

namespace App\Services\Store;

use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Options;
use OpenSpout\Writer\XLSX\Writer;

class ReportExportService
{
    /**
     * Approximate character width used as the upper bound for auto-sized columns
     * to keep extreme values from blowing out the layout.
     */
    private const float MAX_COLUMN_WIDTH = 60.0;

    /**
     * Padding (in characters) added to the longest value seen in a column when
     * computing the final auto-sized column width.
     */
    private const float COLUMN_WIDTH_PADDING = 2.0;

    /**
     * Default minimum column width so empty/short columns still look balanced.
     */
    private const float MIN_COLUMN_WIDTH = 10.0;

    /**
     * Writes an inventory report to the given output path as an XLSX file.
     *
     * @param array<string, mixed> $data Output of ReportService::generateInventoryReport()
     */
    public function writeInventoryReport(array $data, string $outputPath): void
    {
        $categories = config('store.product_categories', []);

        $headers = [
            'Product',
            'Category',
            'Cost',
            'Price',
            'Stock',
            'Total Cost',
            'Total Value',
        ];

        $rows = [];
        foreach ($data['products'] as $product) {
            $category = $categories[$product['category']] ?? $product['category'];
            $rows[] = [
                $product['name'],
                $category,
                $this->formatMoney($product['cost']),
                $this->formatMoney($product['price']),
                $product['stock'],
                $this->formatMoney($product['cost'] * $product['stock']),
                $this->formatMoney($product['price'] * $product['stock']),
            ];
        }

        $rows[] = ['', '', '', '', '', '', ''];
        $rows[] = [
            'Totals',
            '',
            '',
            '',
            '',
            $this->formatMoney($data['totals']['cost']),
            $this->formatMoney($data['totals']['value']),
        ];

        $this->writeWorkbook($outputPath, [
            [
                'name' => 'Inventory',
                'headers' => $headers,
                'rows' => $rows,
            ],
        ]);
    }

    /**
     * Writes a detailed sales report to the given output path as an XLSX file.
     *
     * @param array<string, mixed> $data Output of ReportService::generateSalesReport() (detailed)
     * @param array<string, mixed> $filters Filters used to generate the report (start, end, store, type)
     */
    public function writeDetailedSalesReport(array $data, array $filters, string $outputPath): void
    {
        $taxRate = (float) config('store.tax', 0);
        $stores = config('store.stores', []);
        $storeName = $this->resolveStoreName($filters['store'] ?? null, $stores);
        $start = $filters['start'] ?? 'n/a';
        $end = $filters['end'] ?? 'n/a';

        $summaryRows = [
            ['Report Period', $start.' to '.$end],
            ['Store', $storeName],
            ['', ''],
            ['Gross Sales', $this->formatMoney($data['gross'])],
            ['Net Sales', $this->formatMoney($data['net'])],
            ['Cash Sales', $this->formatMoney($data['cash'])],
            ['Credit Sales', $this->formatMoney($data['credit'])],
            ['Product Sales', $this->formatMoney($data['productSales'])],
            ['Liquid Sales', $this->formatMoney($data['liquidSales'])],
            ['Total Orders', (string) $data['totalOrders']],
            ['Average Order', $this->formatMoney($data['averageOrder'])],
            ['Sales Tax', $this->formatMoney($data['subtotal'] * $taxRate)],
            ['Discounts Given', '-'.$this->formatMoney($data['discounts'])],
            ['Total ml Sold', (string) $data['totalMl']],
        ];

        $employeeRows = [];
        foreach ($data['employee'] as $name => $sales) {
            $employeeRows[] = [$name, $this->formatMoney($sales)];
        }

        $hourlyRows = [];
        foreach ($data['hourly'] as $hour => $sales) {
            $customers = $data['customers'][$hour] ?? 0;
            $hourlyRows[] = [
                $hour,
                $this->formatMoney($sales),
                (string) $customers,
            ];
        }

        $bottleRows = [];
        foreach ($data['liquids'] as $size => $count) {
            $bottleRows[] = [$size.'ml', (string) $count];
        }

        $sheets = [
            [
                'name' => 'Summary',
                'headers' => ['Metric', 'Value'],
                'rows' => $summaryRows,
            ],
            [
                'name' => 'Employee Sales',
                'headers' => ['Employee', 'Sales'],
                'rows' => $employeeRows,
            ],
            [
                'name' => 'Hourly Sales',
                'headers' => ['Hour', 'Sales', 'Customers'],
                'rows' => $hourlyRows,
            ],
            [
                'name' => 'Bottles Sold',
                'headers' => ['Size', 'Count'],
                'rows' => $bottleRows,
            ],
        ];

        $this->writeWorkbook($outputPath, $sheets);
    }

    /**
     * Writes a minimal sales report to the given output path as an XLSX file.
     *
     * @param array<string, mixed> $data Output of ReportService::generateSalesReport() (minimal)
     * @param array<string, mixed> $filters Filters used to generate the report
     */
    public function writeMinimalSalesReport(array $data, array $filters, string $outputPath): void
    {
        $taxRate = (float) config('store.tax', 0);
        $stores = config('store.stores', []);
        $storeName = $this->resolveStoreName($filters['store'] ?? null, $stores);
        $start = $filters['start'] ?? 'n/a';
        $end = $filters['end'] ?? 'n/a';

        $rows = [
            ['Report Period', $start.' to '.$end],
            ['Store', $storeName],
            ['', ''],
            ['Gross Sales', $this->formatMoney($data['gross'])],
            ['Sales Tax', $this->formatMoney($data['subtotal'] * $taxRate)],
        ];

        $this->writeWorkbook($outputPath, [
            [
                'name' => 'Sales Summary',
                'headers' => ['Metric', 'Value'],
                'rows' => $rows,
            ],
        ]);
    }

    /**
     * Writes the given sheets to an XLSX file with header styling and
     * auto-sized columns based on the longest cell value in each column.
     *
     * @param array<int, array{name: string, headers: array<int, string>, rows: array<int, array<int, string|int|float>>}> $sheets
     */
    private function writeWorkbook(string $outputPath, array $sheets): void
    {
        $writer = new Writer(new Options());
        $writer->openToFile($outputPath);

        $headerStyle = (new Style())
            ->withFontBold(true)
            ->withFontColor(Color::WHITE)
            ->withBackgroundColor(Color::DARK_BLUE)
            ->withCellAlignment(CellAlignment::CENTER);

        foreach ($sheets as $index => $sheet) {
            if ($index > 0) {
                $writer->addNewSheetAndMakeItCurrent();
            }

            $currentSheet = $writer->getCurrentSheet();
            $currentSheet->setName($this->sanitizeSheetName($sheet['name']));

            $writer->addRow(Row::fromValuesWithStyle($sheet['headers'], $headerStyle));
            foreach ($sheet['rows'] as $row) {
                $writer->addRow(Row::fromValues($row));
            }

            $widths = $this->calculateColumnWidths($sheet['headers'], $sheet['rows']);
            foreach ($widths as $columnIndex => $width) {
                $currentSheet->setColumnWidth($width, $columnIndex + 1);
            }
        }

        $writer->close();
    }

    /**
     * Calculates the appropriate width (in characters) for each column based on
     * the longest value seen in either the headers or the rows.
     *
     * @param array<int, string> $headers
     * @param array<int, array<int, string|int|float>> $rows
     * @return array<int, float>
     */
    private function calculateColumnWidths(array $headers, array $rows): array
    {
        $widths = [];
        foreach ($headers as $columnIndex => $header) {
            $widths[$columnIndex] = max(self::MIN_COLUMN_WIDTH, mb_strlen((string) $header) + self::COLUMN_WIDTH_PADDING);
        }

        foreach ($rows as $row) {
            foreach ($row as $columnIndex => $value) {
                $length = mb_strlen((string) $value) + self::COLUMN_WIDTH_PADDING;
                if (!isset($widths[$columnIndex]) || $length > $widths[$columnIndex]) {
                    $widths[$columnIndex] = $length;
                }
            }
        }

        foreach ($widths as $columnIndex => $width) {
            $widths[$columnIndex] = min(self::MAX_COLUMN_WIDTH, (float) $width);
        }

        return $widths;
    }

    /**
     * Formats a numeric value as a money string with two decimal places, no currency symbol
     * (Excel handles formatting). Returns '0.00' for null/non-numeric values.
     */
    private function formatMoney(mixed $value): string
    {
        if (!is_numeric($value)) {
            return '0.00';
        }

        return number_format((float) $value, 2, '.', '');
    }

    /**
     * Resolves a human readable store name from the configured stores list.
     */
    private function resolveStoreName(mixed $storeKey, array $stores): string
    {
        if ($storeKey === null || $storeKey === '' || (int) $storeKey === 0) {
            return 'All Stores';
        }

        return $stores[$storeKey] ?? ('Store '.$storeKey);
    }

    /**
     * Sanitizes a sheet name to comply with Excel's 31 character limit and
     * forbidden character set (: \ / ? * [ ]).
     */
    private function sanitizeSheetName(string $name): string
    {
        $clean = preg_replace('/[\\\\\\/\\?\\*\\[\\]:]/', ' ', $name) ?? $name;

        return mb_substr($clean, 0, 31);
    }
}
