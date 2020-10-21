<?php

namespace App\Services;

use Exception;

class ExcelExportService
{
    /**
     * Excel export
     *
     * @var string
     */
    protected const EXPORT_EXCEL = 'xlsx';

    /**
     * CSV export
     *
     * @var string
     */
    protected const EXPORT_CSV = 'csv';

    /**
     * PDF export
     *
     * @var string
     */
    protected const EXPORT_PDF = 'pdf';

    /**
     * The excel instance.
     *
     * @var \Excel
     */
    protected $excel;

    protected $title;

    /**
     * File name.
     *
     * @var string
     */
    public $file_name;

    /**
     * Data to be exported.
     *
     * @var array
     */
    public $data = [];

    /**
     * The formatted data to be exported.
     *
     * @var array
     */
    private $formatted_data = [];

    /**
     * A new instance of excel export service.
     *
     * @param string $file_name
     * @param array $data
     * @return void
     */
    public function __construct($file_name, $data, $title)
    {
        //static::validateData($data);

        $this->data = $data;
        $this->file_name = $file_name;
        $this->title = $title;
        $this->makeExportInstance();
    }

    /**
     * Make the instance of the exportable
     *
     * @return void
     */
    public function makeExportInstance()
    {
        $this->formatData();
        $this->excel = new DefaultExport($this->file_name, $this->formatted_data);
    }

    /**
     * Download data as CSV.
     *
     * @return \Illuminate\Http\Response
     */
    public function downloadCSV()
    {
        return $this->excel->download($this->file_name .'.'. static::EXPORT_CSV);
    }

    /**
     * Download data as Excel.
     *
     * @return \Illuminate\Http\Response
     */
    public function downloadExcel()
    {
        return $this->excel->download($this->file_name .'.'. static::EXPORT_EXCEL);
    }

    /**
     * Download data as PDF.
     *
     * @return \Illuminate\Http\Response
     */
    public function downloadPDF()
    {
        return $this->excel->download($this->file_name .'.'. static::EXPORT_PDF);
    }

    /**
     * Save generated spreadsheet in csv file
     *
     * @param string $path The destination to save csv
     */
    public function saveCSV($path)
    {
        $path .= '/'. $this->file_name .'.'. static::EXPORT_CSV;
        $this->excel->store($path, 'root');
    }

    /**
     * Save generated spreadsheet in excel file
     *
     * @param string $path The destination to save excel
     */
    public function saveExcel($path)
    {
        $this->excel->store('xls', $path);
    }

    /**
     * Prepare the data to be exported.
     *
     * @return void
     */
    protected function formatData()
    {
        $this->formatted_data = [];

        $sheetData = $this->data;

        $this->addEmptyRows(2);

        $this->appendRow([
            $this->title
        ]);

        // Append headers and style
        $this->appendRow($sheetData['headers']);

        // Append the actual data (rows)
        foreach ($sheetData['data'] as $row) {
            $this->appendRow($this->prepData($row));
        }

        // Give two spaces
        $this->addEmptyRows(2);
    }

    /**
     * Append a row to the formatted data.
     *
     * @param array $data
     * @return void
     */
    private function appendRow(array $data)
    {
        $this->formatted_data[] = $data;
    }

    /**
     * Add empty rows to the sheet.
     *
     * @param int $number
     * @return void
     */
    private function addEmptyRows($number)
    {
        for ($i = 0; $i < $number; $i++) {
            $this->appendRow([null]);
        }
    }

    /**
     * Prepare the data to flatten it into a single array.
     * This is what needs to be inserted into the sheet.
     *
     * @param array $data
     * @return array
     */
    protected function prepData($data): array
    {
        $response = [];

        foreach ($data as $entry) {
            $response[] = (string)$entry;
        }

        return $response;
    }
}
