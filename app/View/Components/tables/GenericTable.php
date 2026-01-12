<?php

namespace App\View\Components\Tables;

use Illuminate\View\Component;

class GenericTable extends Component
{
    public $data;
    public $columns;
    public $formats;
    public $itemsPerPage;

    public function __construct($data = [], $columns = [], $formats = [], $itemsPerPage = 5)
    {
        $this->data = $data;
        $this->columns = $columns;
        $this->formats = $formats;
        $this->itemsPerPage = $itemsPerPage;
    }

    public function render()
    {
        return view('components.tables.generic-table');
    }
}