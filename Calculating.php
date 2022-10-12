<?php
	require_once('Pattern.php');
	require_once('Schema.php');
	require_once('PDFmethods.php');
	require_once( 'MyException.php' );
	//define('FPDF_FONTPATH','font/'); 
	
	$pdf = new PDFmethods();
	$errors='';
	if(array_search(0,$_POST) && array_search(0,$_POST)!='goOn'){
			$errors.="1 Пожалуйста, не оставляйте нулевых значений.\"<br>";
	}
	$ct=$_POST['Ct']/2;
	$cb=$_POST['Cb']/2;
	$di=$_POST['Di'];
	$tx=$_POST['ksx']/$_POST['kcmx'];
	$ty=$_POST['ksy']/$_POST['kcmy'];
	$dpt=$_POST['Dpt'];
	$cb=$_POST['Cb']/2;
	$cg=$_POST['Cg']/2;
	$oz=$_POST['Oz'];
	$shs=$_POST['Shs'];
	$shp=$_POST['Shp'];
	$dps=$_POST['Dps'];
	$dst=$_POST['Dst'];
	$dr=$_POST['Dr'];
	$di=$_POST['Di'];
	$tx=$_POST['ksx']/$_POST['kcmx'];
	$ty=$_POST['ksy']/$_POST['kcmy'];
	if($ct>$cb) {
		$errors.="полуобхват бедер не должен быть больше, чем полуобхват талии. Скорре всего Вы не правильно сняли мерки.<br>";
	}
	if ($errors){
		echo "<script>alert(\"".$errors.".\");</script>";
		echo "<script>history.back()</script>";
		return;
	}
	if ($_POST['bPat']){
		$pt=new Pattern($_POST['type'],$ct,$cb,$di,$cg,$dst,$dpt,$dps,$shs,$shp,$dr,$oz);
		$pt->build($pdf);
	}
	$sc=new Schema($_POST['type'],$tx,$ty,$ct,$cb,$di,$cg,$dst,$dpt,$dps,$shs,$shp,$dr,$oz);
	$sc->build($pdf);
	$pdf->savePDF("vyazhem_prosto.pdf");
?>