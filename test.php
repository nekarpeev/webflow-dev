<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

define('ROOT', $_SERVER['DOCUMENT_ROOT']);

$excelClass  = ROOT . '/assets/dist/lab/PHPExcel-1.8/Classes/PHPExcel.php';
$filepath    = ROOT . '/КАЛЬКУЛЯТОР/Калькулятор.xlsx';

function parse_excel_file( $filename, $excelClass ) {
	// подключаем библиотеку
	require_once $excelClass;
	
	$result     = [];
	$form_data  = [];
	$errors     = false;
	
	// получаем тип файла (xls, xlsx), чтобы правильно его обработать
	$file_type = PHPExcel_IOFactory::identify( $filename );
	// создаем объект для чтения
	$objReader = PHPExcel_IOFactory::createReader( $file_type );
	$objPHPExcel = $objReader->load( $filename ); // загружаем данные файла в объект
	//print_r($objPHPExcel);
	
	if( isset($_POST['submit']) ) {
	
	    if( isset($_POST['type_select']) && $_POST['type_select'] !== 'empty' ) {
    	    $form_data['type_select'] = $_POST['type_select'];
        }
        else {
            $errors['type_select'] = 'Выберите тип расчета';
        }  
        
        if( !empty($_POST['calc_balance']) ) {
        	$form_data['calc_balance'] = $_POST['calc_balance'];
        }
        else {
        	$errors['calc_balance'] = 'Укажите балансовую стоимость';
        }
        
        if( !empty($_POST['calc_length']) ) {
        	$form_data['calc_length'] = $_POST['calc_length'];
        }
        else {
        	$errors['calc_length'] = 'Укажите протяженность';
        }
        
        if( $_POST['rangeChange'] == 'change') {
        	$form_data['calc_power'] = $_POST['calc_power'];
        }
        else {
        	$errors['calc_power'] = 'Укажите Присоединенную мощность';
        }
	    
	}
	
	if($errors == false) {

	switch($_POST['type_select']) {
	   case 'ЭЭ':
	        $result['title'] = 'Электрические сети';
	        $set_cell_balance = 'D4';
	        $set_cell_length  = 'D5';
	        $set_cell_power   = 'D6';
	        break;
	   case 'ТЭ':
	        $result['title'] = 'Тепловые сети';
	        $set_cell_balance = 'E4';
	        $set_cell_length  = 'E5';
	        $set_cell_power   = 'E6';
	        break;
	   case 'ВС':
	        $result['title'] = 'Сети водоснабжения';
	        $set_cell_balance = 'F4';
	        $set_cell_length  = 'F5';
	        $set_cell_power   = 'F6';
	        break;
	   case 'ВО':
	        $result['title'] = 'Сети водоотведения';
	        $set_cell_balance = 'G4';
	        $set_cell_length  = 'G5';
	        $set_cell_power   = 'G6';
	        break;     
	}
	
	$objPHPExcel->getSheetByName('Исходные данные')->setCellValue($set_cell_balance, $form_data['calc_balance']);
	$objPHPExcel->getSheetByName('Исходные данные')->setCellValue($set_cell_length, $form_data['calc_length']);
	$objPHPExcel->getSheetByName('Исходные данные')->setCellValue($set_cell_power, $form_data['calc_power']);

	$result[] = round( $objPHPExcel->getSheetByName( $form_data['type_select'] )->getCell('F24')->getCalculatedValue() );
	$result[] = round( $objPHPExcel->getSheetByName( $form_data['type_select'] )->getCell('H24')->getCalculatedValue() );
	$result[] = round( $objPHPExcel->getSheetByName( $form_data['type_select'] )->getCell('J24')->getCalculatedValue() );
	$result[] = round( $objPHPExcel->getSheetByName( $form_data['type_select'] )->getCell('L24')->getCalculatedValue() );
	
	}
	
 	else {
 	   $errors['type_status'] = 'show';
	   $result = $errors;
 	}
	return $result;
}

$res = parse_excel_file($filepath, $excelClass );

echo json_encode($res, JSON_UNESCAPED_UNICODE);
//print_r($res);










