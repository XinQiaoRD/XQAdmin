<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Asia/Shanghai');
error_reporting(0);


define("DbHost", "hdm15666170.my3w.com");
define("DbNm",   "hdm15666170_db");
define("DbRoot", "hdm15666170");
define("DbPsw",  "1981588k");//
define("DbPost", "3306");
define("DbPre",  "cy_");



include('cc.php');
require dirname(__FILE__) . '/Classes/PHPExcel.php';

$cc = new cc;
$g = $cc->get();

if(!$g->dates1 || !$g->dates2) die("date error!!");

//获取日期
$cc->where = "dates>='{$g->dates1}' and dates<='{$g->dates2}' and state=1";
$cc->order = "dates";
$cc->field = "dates";
$cc->group = "dates";
$dates_rsc = $cc->opsql('classmake',"rsc");
if(!$dates_rsc) die("no date");

//获取老师
$cc->order = "seat, id";
$cc->where = "state = 1";
$cc->field = "id, nm";
$teacher_rsc = $cc->opsql('teacher',"rsc");
$teacher = array();
$teacherID = array();
while($rs = $cc->rs($teacher_rsc)){
    $teacher[] = $rs["nm"];
    $teacherID[] = $rs["id"];
}

$arr = array();
while($dates = $cc->rs($dates_rsc)){

    $arr[$dates["dates"]] = array();

    $cc->where = "dates='".$dates["dates"]."'";
    $cc->order = "dates, times, tseat, tid";
    $cc->field = "tid,nm,dates,times";
    $rsc = $cc->opsql('classmake',"rsc");

    while($rs = $cc->rs($rsc)){

        if(!$arr[$dates["dates"]][$rs["times"]]) {
            $arr[$dates["dates"]][$rs["times"]] = array();
        }

        if(!$rs["nm"]) $rs["nm"] = "--";
        $arr[$dates["dates"]][$rs["times"]][$rs["tid"]] = $rs["nm"];
    }

}

$en = array(
    0=>"C", 1=>"D", 2=>"E", 3=>"F", 4=>"G", 5=>"H", 6=>"I", 7=>"J", 8=>"K", 9=>"L", 10=>"M", 11=>"N", 12=>"O", 13=>"P", 14=>"Q", 15=>"R",
    16=>"S", 17=>"T", 18=>"U", 19=>"V", 20=>"W", 21=>"X", 22=>"Y", 23=>"Z"
);

export_data($arr, $en, $teacherID, $teacher, '上课信息'.$g->dates1.'到'.$g->dates2.'.xls');

function tit($objPHPExcel, $ri, $en, $d, $tid, $tnm){
    $ds = explode("-", $d);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('A'.$ri, $ds[1]."月", PHPExcel_Cell_DataType::TYPE_STRING);
    color($objPHPExcel, 'A'.$ri, "800000", 1);

    $objPHPExcel->getActiveSheet()->setCellValueExplicit('B'.$ri, $ds[2]."日", PHPExcel_Cell_DataType::TYPE_STRING);
    color($objPHPExcel, 'B'.$ri, "800000", 1);


    foreach ($tid as $i => $id){
        $objPHPExcel->getActiveSheet()->setCellValueExplicit($en[$i].$ri, $tnm[$i], PHPExcel_Cell_DataType::TYPE_STRING);
        color($objPHPExcel, $en[$i].$ri, "00CCFF");
    }
}

function row($objPHPExcel, $ri, $en, $dates, $t, $tid, $arr){
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('A'.$ri, get_week($dates), PHPExcel_Cell_DataType::TYPE_STRING);
    color($objPHPExcel, 'A'.$ri, "003366", 1);

    $objPHPExcel->getActiveSheet()->setCellValueExplicit('B'.$ri, $t.":00", PHPExcel_Cell_DataType::TYPE_STRING);
    color($objPHPExcel, 'B'.$ri, "3366FF", 1);

    foreach ($tid as $i => $id){
        $objPHPExcel->getActiveSheet()->setCellValueExplicit($en[$i].$ri, $arr[$tid[$i]], PHPExcel_Cell_DataType::TYPE_STRING);
        color($objPHPExcel, $en[$i].$ri);
    }

}

function color($objPHPExcel, $ri, $color="", $mk=0){

    if($color){
        $objPHPExcel->getActiveSheet()->getStyle($ri)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle($ri)->getFill()->getStartColor()->setARGB("FF".$color);
    }

    if($mk) $objPHPExcel->getActiveSheet()->getStyle($ri)->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);

    $styleArray = array(
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,//细边框
                'color' => array('argb' => 'FF000000')
            ),
        ),
    );
    $objPHPExcel->getActiveSheet()->getStyle($ri)->applyFromArray($styleArray);
}

function mergeCells($objPHPExcel, $ri){

    $objPHPExcel->getActiveSheet()->mergeCells('A'.($ri-11).':A'.$ri);
}

function export_data($data = array(), $en, $tid, $tnm,  $filename=''){

    $objPHPExcel = new PHPExcel();

    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1', '')
        ->setCellValue('B1', '')
        ->setCellValue('C1', '')
        ->setCellValue('D1', '');

    //设置宽度
    $objPHPExcel->getActiveSheet("A")->getColumnDimension()->setWidth(10);
    $objPHPExcel->getActiveSheet("B")->getColumnDimension()->setWidth(10);
    foreach ($tid as $i => $id){
        $objPHPExcel->getActiveSheet("B")->getColumnDimension($en[$i])->setWidth(10);
    }
    $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(20);
    $objPHPExcel->setActiveSheetIndex()->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->setActiveSheetIndex()->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

    if($data){
        $i = 1;
        foreach ($data as $dates => $row) {
            $i++;
            tit($objPHPExcel, $i, $en, $dates, $tid, $tnm);

            for($t=9; $t<=20; $t++){
                $i++;
                row($objPHPExcel, $i, $en, $dates, $t, $tid, $row[$t]);
            }

            mergeCells($objPHPExcel, $i);

//            foreach ($row as $times => $rs){
//
//                foreach ($tid as $i => $id){
//
//
//
//                }
//
//            }

//            $objPHPExcel->getActiveSheet()->setCellValueExplicit('A'.$i, $rs['id'], PHPExcel_Cell_DataType::TYPE_STRING);
//            $objPHPExcel->getActiveSheet()->setCellValueExplicit('B'.$i, $rs['openid'], PHPExcel_Cell_DataType::TYPE_STRING);
//            $objPHPExcel->getActiveSheet()->setCellValueExplicit('C'.$i, $rs['tel'], PHPExcel_Cell_DataType::TYPE_STRING);
//            $objPHPExcel->getActiveSheet()->setCellValueExplicit('D'.$i, $rs['dates'], PHPExcel_Cell_DataType::TYPE_STRING);


        }
    }

    if(!$filename) $filename = "outexcel.xls";
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'.$filename.'"');
    header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');
// If you're serving to IE over SSL, then the following may be needed
    header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
    header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header ('Pragma: public'); // HTTP/1.0

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');


}


function get_week($date){
    //强制转换日期格式
    $date_str=date('Y-m-d',strtotime($date));

    //封装成数组
    $arr=explode("-", $date_str);

    //参数赋值
    //年
    $year=$arr[0];

    //月，输出2位整型，不够2位右对齐
    $month=sprintf('%02d',$arr[1]);

    //日，输出2位整型，不够2位右对齐
    $day=sprintf('%02d',$arr[2]);

    //时分秒默认赋值为0；
    $hour = $minute = $second = 0;

    //转换成时间戳
    $strap = mktime($hour,$minute,$second,$month,$day,$year);

    //获取数字型星期几
    $number_wk=date("w",$strap);

    //自定义星期数组
    $weekArr=array("星期日","星期一","星期二","星期三","星期四","星期五","星期六");

    //获取数字对应的星期
    return $weekArr[$number_wk];
}
?>