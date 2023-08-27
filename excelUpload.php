<?php
require 'vendor/autoload.php';
include(__DIR__ .'/vendor/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if(isset($_POST['Submit'])){


    $mimes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
    if(in_array($_FILES["file"]["type"],$mimes)){
        $uploadFilePath = 'uploads/'.date("Ymdhis").basename($_FILES['file']['name']);
        move_uploaded_file($_FILES['file']['tmp_name'], $uploadFilePath);

        try {
            // load uploaded file
            $objPHPExcel = PHPExcel_IOFactory::load($uploadFilePath);
        } catch (Exception $e) {
             die('Error loading file "' . pathinfo($file, PATHINFO_BASENAME). '": ' . $e->getMessage());
        }

        $csvData = [];
        // Specify the excel sheet index
        $sheet = $objPHPExcel->getSheet(0);
        $total_rows = $sheet->getHighestRow();
        $highestColumn      = $sheet->getHighestColumn();   
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);     
        //  loop over the rows
        for ($row = 1; $row <= $total_rows; ++ $row) {
            for ($col = 0; $col < $highestColumnIndex; ++ $col) {
                $cell = $sheet->getCellByColumnAndRow($col, $row);
                $val = $cell->getValue();
                $csvData[$row][$col] = $val;
            }
        }

        $json  = json_encode($csvData);
        file_put_contents("outputs/".date("Ymdhis")."output.json", $json); //generate json file
        echo "Extort record into csv files successfully<a href='http://localhost/chetsApp'>Back</a>";exit;
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }
}else { 
    die("<br/>Sorry, File type is not allowed. Only Excel file."); 
}


