<?php

namespace App\Services;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;

class DefaultExport implements FromArray, WithTitle
{
    use Exportable;

    /**
     * The raw data to be exported.
     *
     * @var array
     */
    public $data = [];

    /**
     * The title of the workbook.
     *
     * @var string
     */
    public $title;

    public function __construct(string $title, array $data)
    {
        $this->data = $data;
        $this->title = $title;
    }

    /**
     * Generate the excel from this array.
     *
     * @return array
     */
    public function array(): array
    {
        return $this->data;
    }

    /**
     * Set the title of the work book.
     *
     * @return string
     */
    public function title(): string
    {
        return substr($this->title, 0, 31);
    }
}
