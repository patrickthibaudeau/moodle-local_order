<?php

namespace local_order;

require_once($CFG->libdir . '/phpspreadsheet/vendor/autoload.php');

use core\notification;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class import
{
    private $worksheet;

    /**
     * @param $file string  Path to file
     */
    public function __construct($file)
    {

        // Make sure we have an .xlsx file
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file_info = finfo_file($finfo, $file);

        if ($file_info == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $spread_sheet = $reader->load($file);
            $this->worksheet = $spread_sheet->getActiveSheet();;
        } else {
            notification::error('You must upload an xlsx file');
            $this->worksheet = false;
        }

    }

    /**
     * Returns an array of all columns in the first row of the work sheet
     * @return array
     */
    public function get_first_row()
    {
        $worksheet = $this->worksheet;
        $worksheet_array = $worksheet->toArray();
//        print_object($worksheet_array);
        $columns = [];
        for ($i = 0; $i < count($worksheet_array[0]); $i++) {
            if ($worksheet_array[0][$i]) {
                $columns[$i] = $worksheet_array[0][$i];
            }
        }
        return $columns;
    }

    /**
     * Returns all rows as an array
     * @return array
     */
    public function get_rows()
    {
        raise_memory_limit(MEMORY_UNLIMITED);
        $worksheet = $this->worksheet;
        $worksheet_array = $worksheet->toArray();
        $number_of_rows = count($worksheet_array);
        $columns = $this->get_first_row();
        $data = [];
        $rows = [];
        // Start at 1 because 0 is the first row
        for ($i = 1; $i <= $number_of_rows; $i++) {

            foreach ($columns as $key => $column) {
                if (isset($worksheet_array[$i][$key])) {
                    $rows[$i][$key] = $worksheet_array[$i][$key];
                } else {
                    $rows[$i][$key] = '';
                }
            }

        }
        raise_memory_limit(MEMORY_STANDARD);
        return $rows;
    }

    /**
     * Returns array for colum names
     * @return array
     */
    public function clean_column_names() {
        $columns = $this->get_first_row();
        $column_names = [];
        foreach ($columns as $key => $column) {
            $column_names[$key] = new \stdClass();
            $column_names[$key]->fullname = $column;
            // Clean the column name
            $clean_column = preg_replace('/[^\w\s]+/', '', $column);;
            $clean_column = str_replace(" ", '_', $clean_column);
            $clean_column = strtolower($clean_column);
            $column_names[$key]->shortname = $clean_column;

        }

        return $column_names;
    }
}