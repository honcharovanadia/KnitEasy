<?php
require_once( 'fpdf.php' );


class Pattern{
	var $type=2;		//тип одежды
	var $Ct;		//полуобхват талии
	var $Cb;		//полуобхват бедер
	var $Di;		//длинна изделия
	var $Cg=45;
	var $Dst=40;
	var $Dpt=39;
	var $Dps=37;
	var $Shs=35;
	var $Shp=15;
	var $Dr=50;
	var $Oz=24;
	var $y0=180;		//начало координат по Y
	var $x0=60;		//начало координат по Х
	var $k=38;		//коефициен преобразования сантиметров в пиксели
	
	public function Pattern($type=0,$ct=0,$cb=0,$di=0,$cg=0,$dst=0,$dpt=0,$dps=0,$shs=0,$shp=0,$dr=0,$oz=0){
		$this->type=$type;
		$this->Ct=$ct;
		$this->Cb=$cb;
		$this->Di=$di;
		$this->Cg=$cg;
		$this->Dst=$dst;
		$this->Dpt=$dpt;
		$this->Dps=$dps;
		$this->Shs=$shs;
		$this->Shp=$shp;
		$this->Dr=$dr;
		$this->Oz=$oz;
	}
	
	public function toStr(){
		return "type=".$this->type." Ct=".$this->Ct." Cb=".$this->Cb." Di=".$this->Di.
			" Cg=".$this->Cg." Dst=".$this->Dst."Dpt=".$this->Dpt." Shs=".$this->Shs
			." Shp=".$this->Shp." Dr=".$this->Dr." Oz=".$this->Oz."<br>";
	}
	
	public function build($pdf){
		switch($this->type){
			case 1:
				$this->buildSkirt2($pdf);
				break;
			case 2:
				$this->buildSwiter($pdf);
				break;
			default:
				throw new MyException("Unknown cloth type");
		}
	}
	
	public function buildSkirt(){
		$l1=$this->Ct/2;
		$l2=$this->Cb/2;
		$l3=$l2+3;
		$h1=$this->Di;
		$h2=13.5;
		$x0=($l3+5)*$this->k;
		$y0=$this->y0;
		$k=38;
		$im=$this->createImg(($l3*2+10)*$this->k, ($h1+10)*$this->k);
		$im=$this->line($im,$x0-$l3*$k,$y0+$h1*$k,$x0+$l3*$k,$y0+$h1*$k);
		$im=$this->line($im,$x0-$l2*$k,$y0+$h2*$k,$x0+$l2*$k,$y0+$h2*$k);
		$im=$this->line($im,$x0-$l1*$k,$y0,$x0+$l1*$k,$y0);
		$im=$this->line($im,$x0-$l3*$k,$y0+$h1*$k,$x0-$l2*$k,$y0+$h2*$k);
		$im=$this->line($im,$x0-$l2*$k,$y0+$h2*$k,$x0-$l1*$k,$y0);
		$im=$this->line($im,$x0+$l3*$k,$y0+$h1*$k,$x0+$l2*$k,$y0+$h2*$k);
		$im=$this->line($im,$x0+$l2*$k,$y0+$h2*$k,$x0+$l1*$k,$y0);
		$im=$this->linep($im,$x0,$y0,$x0,$y0+$h1*$k);
		$this->saveImg($im);
		$pdf = new FPDF('P','cm',array(($l3*2+10),($h1+10)));
		$pdf->Image("skirt.png");
		$pdf->Output("pattf.pdf",'F');	
	}
	
	public function buildSkirt2($pdf){
		$l1=$this->Ct/2;
		$l2=$this->Cb/2;
		$l3=$l2+3;
		$h1=$this->Di;
		$h2=14.5;
		$x0=$l3+5;
		$y0=7;
		//создаем страничку размера будущего чертежа
		$w=$l3*2+10;
		$h=$w>($h1+12)?$w:($h1+12);
		$pdf->addPage(array($w,$h),$x0,$y0);
		$pdf->setLineWidth(0.15);
		$pdf->titleText("���������� ����");
		//создаем чертеж
		$pdf->line( -$l3,$h1,$l3,$h1);
		$pdf->line(-$l2,$h2,$l2,$h2);
		$pdf->line(-$l1,0,$l1,0);
		$pdf->line(-$l3,$h1,-$l2,$h2);
		$pdf->line(-$l2,$h2,-$l1,0);
		$pdf->line($l3,$h1,$l2,$h2);
		$pdf->line($l2,$h2,$l1,0);
		$pdf->symetrLine(0,0,0,$h1);		//линия симетрии
		//расставляем размеры
		$pdf->sizeText($l1,0,$l3,$h1,$h1);
		$pdf->sizeText(-$l1,0,-$l2,$h2,$h2,-2-($l2-$l1));
		$pdf->sizeText(-$l1,0,$l1,0,$l1*2,1,"y");
		$pdf->sizeText(-$l3,$h1,$l3,$h1,$l3*2,-1,"y");
		$pdf->sizeText(-$l2,$h2,$l2,$h2,$l2*2,$h2+2,"y");
		return $pdf;
	}
	
	public function buildSwiter($pdf){
		$y0=7;
		$this->body($pdf,1.5,'������');
		$this->body($pdf,$this->Dpt-$this->Dps,'�����');
		$this->sleeve($pdf,$y0);
	}
	
	private function sleeve($pdf,$y0){
		$h1=$this->Dr;
		$h2=$this->Cg/3-1;
		$l1=$this->Cg/3+4;
		$x0=$l1+5;
		$s1=sqrt($l1*$l1+$h2*$h2)/4;
		$s2=2;
		$s3=1.5;
		$h3=$h2/4;
		$h4=$h2/2;
		$h5=$h2*3/4;
		$l2=$l1/4;
		$l3=$l1/2;
		$l4=$l1*3/4;
		$l5=$this->Oz/2;
		$r1=$this->radius($s1,$s3);
		$r2=$this->radius($s1,$s2);
		$cx1=$l3+$r1*sin(M_PI*3/2-atan(($l1-$l3)/($h2-$h4))-2*atan($s1/$s3));
		$cy1=$h2-$r1*cos(-M_PI/2-atan(($l1-$l3)/($h2-$h4))+2*atan($s1/$s3));
		$cx2=$l3-$r2*sin(-M_PI/2+atan($l3/$h4)+2*atan($s1/$s2));
		$cy2=$r2*sin(2*atan($s1/$s2)-atan($l3/$h4));
		$pdf->addPage(array(($l1*2+10),($h1+12)),$x0,$y0);
		$pdf->setLineWidth(0.15);
		$pdf->titleText("������ � ������� ������� (�����)");
		$pdf->symetrLine(0,0,0,$h1);
		$pdf->line($l1,$h2,$l5,$h1);
		$pdf->line(-$l5,$h1,$l5,$h1);
		$pdf->line(-$l1,$h2,-$l5,$h1);
		$pdf->arc($l3,$l1,$r1,$cx1,$cy1,$type='y');
		$pdf->arc(-$l1,-$l3,$r1,-$cx1,$cy1,$type='y');
		$pdf->arc(0,$h4,$r2,$cx2,$cy2,$type='x');
		$pdf->sizeText(0,0,$l1,$h2,$h2);
		$pdf->sizeText(0,0,0,$h1,$h1,$l1+2.7);
		$pdf->sizeText(-$l1,$h2,$l1,$h2,$l1*2,$h2+1,'y');
		$pdf->sizeText(-$l5,$h1,$l5,$h1,$l5*2,-1,'y');
	}
	
	
	
	private function body($pdf,$gg,$det=""){
		$h1=$this->Cg/3+4;
		$h2=$this->Dst;
		$h3=$this->Di;
		$l1=$this->Cb/2+0.5;
		$x0=$l1+6;
		$y0=9;
		$l2=$this->Shs/2;
		$h4=$h1-($l1-$l2)*2/3;
		$h5=3;
		$l3=$l2-sqrt(pow($this->Shp,2)-pow($y0+$h5,2));
		$l4=$l2+(sqrt(13)/3)*($l1-$l2)*sin(M_PI/6+atan(2/3));
		$h6=$h1-(sqrt(13)/3)*($l1-$l2)*sin(((2*M_PI)/3)-atan(2/3));
		$pdf->addPage(array(($l1*2+14),($h3+16)),$x0,$y0);
		$pdf->setLineWidth(0.15);
		$pdf->titleText("������ � ������� ������� (".$det.")");
		$pdf->symetrLine(0,0,0,$h3);
		$r=$this->radius($l3,$gg);
		$pdf->arc(-$l3,$l3,$r,0,-$r*cos(M_PI-2*atan($l3/$gg)),$type='y');
		$r=(sqrt(13)/3)*($l1-$l2);
		$pdf->arc($l2,$l1,$r,$l4,$h6,$type='y');
		$pdf->arc(-$l1,-$l2,$r,-$l4,$h6,$type='y');
		$pdf->line($l1,$h3,$l1,$h1);
		$pdf->line($l2,$h4,$l2,$h5);
		$pdf->line($l2,$h5,$l3,0);
		$pdf->line(0,$h3,$l1,$h3);
		$pdf->line(-$l1,$h3,-$l1,$h1);
		$pdf->line(-$l2,$h4,-$l2,$h5);
		$pdf->line(-$l2,$h5,-$l3,0);
		$pdf->line(0,$h3,-$l1,$h3);
		$pdf->sizeText(-$l1,$h3,$l1,$h3,$l1*2,-1,'y');
		$pdf->sizeText($l3,0,$l1,$h1,$h1,1,'x');
		$pdf->sizeText($l3,0,$l2,$h5,$h5,1,'x');
		$pdf->sizeText(-$l3,0,-$l1,$h3,$h3,-(($l1-$l3)+1.5));
		$pdf->sizeText(-$l3,0,0,$gg,$gg,-($l2+1.5));
		$pdf->sizeText(-$l2,$h4,$l2,$h4,$l2,$h4+1,'y');
	}
	
	private function radius($l3,$h7){
		return ($l3/sin(M_PI-2*atan($l3/$h7)));
	}
	
	private function line($im,$x1,$y1,$x2,$y2){
		$red = imagecolorallocate($im, 0xCC, 0x00, 0x00);
		imageline($im, $x1, $y1, $x2, $y2, $red);
		return $im;
	}
	
	private function linep($im,$x1,$y1,$x2,$y2){
		$white = imagecolorallocate($im, 0xFF, 0xFF, 0xFF);
		$red = imagecolorallocate($im, 0xCC, 0x00, 0x00);
		$style = array_merge(array_fill(0,40,$white),array_fill(41,80,$red));
		imagesetstyle($im, $style);

		imageline($im, $x1, $y1, $x2, $y2, IMG_COLOR_STYLED);
		return $im;
	}
	
	private function createImg($width,$height){
		$im = imagecreatetruecolor($width,$height);
		
		// Определяем белый цвет
		$white = imagecolorallocate($im, 0xFF, 0xFF, 0xFF);

		// Делаем фон белым 
		imagefill($im, 1, 1, $white);
		imagesetthickness($im, 5);
		return $im;
	}
	
	private function saveImg($im){
		imagepng($im);
		imagepng($im,"skirt.png");
	}

}
?>