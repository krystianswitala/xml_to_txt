<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', 'Zażółć gęślą jaźń...');
$sheet->setCellValue('B1', 128);
$sheet->setCellValue('C1', 256);

$writer = new Xls($spreadsheet);
$writer->save('hello world.xls');
