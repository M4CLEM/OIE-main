<?php 
include("db_connect.php"); 
include("view_logs.php");

$studentNumber = $_SESSION['student_num'];

$data = [];
$post = new viewStudentLogs();
$data = $post->loadStudentLogs($conn, $studentNumber);

require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue("A1", "PAMANTASAN NG LUNGSOD NG MUNTINLUPA\nNBP, Reservation, Poblacion, City of Muntinlupa\nCOLLEGE OF INFORMATION TECHNOLOGY AND COMPUTER STUDIES");
$sheet->getStyle('A1')->getAlignment()->setWrapText(true)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$sheet->getStyle('A1')->getFont()->setBold(true);

$sheet->getColumnDimension('A')->setWidth(21);
$sheet->getColumnDimension('B')->setWidth(21);
$sheet->getColumnDimension('C')->setWidth(21);
$sheet->getColumnDimension('D')->setWidth(21);
$sheet->getColumnDimension('E')->setWidth(21);

$sheet->mergeCells('A1:E4');

$sheet->getStyle('A1:A4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$headers = ['Student Number', 'Date', 'Time In', 'Time Out', 'Total'];
foreach ($headers as $column => $text) {
  $letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($column + 1);
  $cell = $letter . '5';
  $sheet->setCellValue($cell, $text);
  
}
$sheet->getStyle('A5:E5')->getFont()->setBold(true);


$spreadsheet->getActiveSheet()->getStyle('A5:E5')->getFill()
   ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
   ->getStartColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_YELLOW);

$sheet->fromArray($data, null, 'A6');
$length = count($data);
$endRow = 6 + $length - 1;

$spreadsheet->getActiveSheet()->getStyle('A1:E' . $endRow)->applyFromArray([
    'borders' => [
        'allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            'color' => ['rgb' => '000000']
        ]
    ]
 ]);

$spreadsheet->getActiveSheet()->getStyle('A6:E' . $endRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
 

$writer = new Xlsx($spreadsheet);
ob_end_clean();

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="records.xlsx"');
header('Cache-Control: max-age=0');

$writer->save('php://output');

exit;

?>