<?php
class Schema{
	var $type=2;		//тип одежды
	var $Ct;		//полуобхват талии
	var $Cb;		//полуобхват бедер
	var $Di;		//длинна изделия
	var $Cg=55.5;
	var $Dst=40;
	var $Dpt=39;
	var $Dps=30;
	var $Shs=41;
	var $Shp=14;
	var $Dr=59;
	var $Oz=24;
	var $tx;		//к-ство петель на 1 см образца
	var $ty;		//к-ство рядов на 1 см образца
	
	public function Schema($type=0,$thicknessWidth,$thicknessHeight,$ct=0,$cb=0,$di=0,$cg=0,$dst=0,$dpt=0,$dps=0,$shs=0,$shp=0,$dr=0,$oz=0){
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
		$this->tx=$thicknessWidth;
		$this->ty=$thicknessHeight;
	}
	
	public function build($pdf){
		switch($this->type){
			case 1:
				$this->buildSkirt($pdf);
				break;
			case 2:
				$this->buildSwiter($pdf);
				break;
			default:
				throw new MyException("Unknown cloth type");
		}
	}
	
	//Составляем схему вязания прямой юбки
	public function buildSkirt($pdf){
		$k=round($this->Di*$this->ty);		//к-ство рядов в изделии
		$hs=1/$this->ty;				//высота петли
		$j=0;							//номер ряда
		$pdf->addPage("A4");
		$pdf->titleText("Двухшовная прямая юбка");
		$pdf->write2pdf("Вяжем перед:" );
		//Расчитаем начальное к-ство петель
		$pdf->write2pdf("Набрать на спицы ".$this->stitches($this->skrowlen($j*$hs))." + 2 кромочные.");
		while($j<$k){
			$x1=$this->skrowlen($j*$hs);
			$x2=$this->skrowlen(($j+1)*$hs);
			if($x1>$x2)
				$pdf->write2pdf("В ".($j+1)." ряду убавить ".$this->stitches($x1-$x2)." (".$this->stitches($x2+2,0).").");
			else if($x1<$x2)
					$pdf->write2pdf("В ".($j+1)." ряду прибавить ".($x2-$x1)." петель (".$this->stitches($x2+2,0)." петель).");
			$j++;
		}
		$pdf->write2pdf("Закончить вязать ".$k."-м рядом. Закрыть все петли.");
		$pdf->write2pdf("Заднюю часть вяжем так же, как перед.");
		$pdf->write2pdf("Сшиваем обе части, оставив место для вставки резинки. Вставляем резинку. Юбка готова.");
		return $pdf;
	}
	
	public function buildSwiter($pdf){
		$pdf->addPage("A4");
		$pdf->titleText("Свитер с вточным рукавом");
		$pdf->write2pdf('*При образовании горловины полотно разделится на две части (два плеча). Тогда "краем" полотна будем называть сторону плеча, к которой будет пришит рукав, а другую сторону будем называть "серединой" полотна.');
		$pdf->write2pdf('**Каждое плечо вяжем отдельно, причем в то время, как вы вяжете первое плечо, петли для второго следует надеть на вязальную булавку. После того, как первое плечо будет связано, петли второго нужно снова надеть на спицу и вязать симетрично первому плечу.');
		$pdf->write2pdf("***Если не указано иначе, убавляем, прибавляем и закрываем петли по краям полотна (смотри *).");
		$pdf->write2pdf("****Если ряд в инструкции не указан, то его нужно провязать без изменений.");
		$pdf->write2pdf("");
		$this->buildBody($pdf,"Спинка",1.5);
		$pdf->write2pdf("");
		$this->buildBody($pdf,"Перед",$this->Dpt-$this->Dps);
		$pdf->write2pdf("");
		$this->buildSleeve($pdf);
		$pdf->write2pdf("Сшиваем спинку и перед (боковые шви и плечики). Булавками прикалываем к пройме рукав так, чтобы боковой шов рукава и туловища сошлись. Вшить рукав. Сшить боковой шов рукава.");
		$pdf->write2pdf("Свитер готов.");
	}
	
	private function correct($a,$h){
		return round($a/$h)*$h;
	}
	
	private function buildBody($pdf,$det,$gg=1){
		$hs=round(1/$this->ty,4);				//ЧЩУПФБ РЕФМЙ
		$h3=$this->correct($this->Di,$hs);
		$gg=$this->correct($h3-$gg,$hs);
		$k=round($h3*$this->ty);		//Л-УФЧП ТСДПЧ Ч ЙЪДЕМЙЙ
		$l1=$this->Cb/2+0.5;
		$l2=$this->Shs/2;
		$h1=$this->correct($h3-($this->Cg/3+4),$hs);
		$j=0;							//ОПНЕТ ТСДБ
		$h4=$this->correct($h1+($l1-$l2)*2/3,$hs);
		$h5=$this->correct($h3-3,$hs);
		$l1=(55/2)-1;
		$l2=$this->Shs/2;
		$l3=$l2-sqrt(pow($this->Shp,2)-pow(($h3-$h5),2));
		$r=(sqrt(13)/3)*($l1-$l2);
		$cx1=$l2+$r*sin(M_PI/6+atan(2/3));
		$cy1=$h1+$r*sin(((2*M_PI)/3)-atan(2/3));
		$r2=(($h3-$gg)*($h3-$gg)+$l3*$l3)/(2*($h3-$gg));
		$cx2=0;
		$cy2=$gg+$r2;
		$h=$h1;
		$pdf->write2pdf($det.":");
		$pdf->write2pdf("Набираем на спицы ".$this->stitches(round($l1*$this->tx)*2)." + 2 кромочные.");
		$m=0;
		$bg=0;
		while($j<($k-1)){
		$x1=$x2;
			$str="В ".($j+1)." ряду ";
			if ($j*$hs>=$h1 && ($j+1)*$hs<=$h4){
				$x1=round(($cx1-($this->circle3($j*$hs-$cy1,$r)))*$this->tx);
				$x2=round(($cx1-($this->circle3(($j+1)*$hs-$cy1,$r)))*$this->tx);
			}	
			elseif($j*$hs>=$h5 && ($j+1)*$hs<=$h3){
				$x1=round($this->width($j*$hs,$l2,$h5,$l3,$h3)*$this->tx);
				$x2=round($this->width(($j+1)*$hs,$l2,$h5,$l3,$h3)*$this->tx);
			}
			if($j*$hs>=$gg && ($j+1)*$hs<=$h3){
				$x11=round($this->circle3($j*$hs-$cy2,$r2)*$this->tx);
				$x21=round($this->circle3(($j+1)*$hs-$cy2,$r2)*$this->tx);
				if (!$bg){
					$pdf->write2pdf("Вяжем плечо:");
					$m=$x21;
					$str.="закрыть для проймы по-середине полотна ".$this->stitches(($x21-$x11)*2)." (дальше количество петель будет указано для одного плеча)".($j*$hs<$h5?".":" и ");
				} 
				elseif ($x11<$x21){
					$m=$x21;
					$str.="закрыть для проймы (середина полотна) ".$this->stitches($x21-$x11)." (".$this->stitches($x1-$m+2).")".($j*$hs<$h5?".":" и ");
				}
				$bg=1;
			}
			if($x1>$x2)
				$str.=$this->closeStitches($x1-$x2)." по ".$this->stitches($x1-$x2).($bg?" с краю полотна (".$this->stitches(($x2-$m+2),0):" с каждого края полотна(".$this->stitches(($x2-$m)*2+2,0)).").";
			else 
				if($x1<$x2)
					$str.="прибавить по ".$this->stitches($x1-$x2)." с каждой стороны (".$this->stitches(($x2-$m)*2+2,0).").";
			if($str!="В ".($j+1)." ряду ")	$pdf->write2pdf($str);
			$j++;
		}
		$pdf->write2pdf("В ".$k." ряду закрыть все оставшиеся петли.");
		$pdf->write2pdf("Второе плечо вяжем симетрично первому.");
	}
	
	private function closeStitches($n){
		return $n>3?"закрыть":"убавить";
	}
	
	public function buildSleeve($pdf){	
		$hs=1/$this->ty;			//высота петли
		$h1=$this->correct($this->Dr,$hs);
		$k=round($h1*$this->ty);
		$h2=$this->correct($h1-$this->Cg/3-1,$hs);
		$h4=$h2+$this->correct(($h1-$h2)/2,$hs);
		$l1=$this->Cg/3+4;
		$l3=$l1/2;
		$l5=$this->Oz/2;
		$s1=sqrt($l1*$l1+($h1-$h2)*($h1-$h2))/4;
		$s2=2;
		$s3=1.5;
		$r1=$s1/sin(M_PI-2*atan($s1/$s3));
		$r2=$s1/sin(M_PI-2*atan($s1/$s2));
		$cx1=$l3+$r1*sin(M_PI*3/2-atan(($l1-$l3)/($h4-$h2))-2*atan($s1/$s3));
		$cy1=$h2+$r1*cos(-M_PI/2-atan(($l1-$l3)/($h4-$h2))+2*atan($s1/$s3));
		$cx2=$l3-$r2*sin(-M_PI/2+atan($l3/($h1-$h4))+2*atan($s1/$s2));
		$cy2=$h1-$r2*sin(2*atan($s1/$s2)-atan($l3/($h1-$h4)));
		$h=0;$j=0;
		$pdf->write2pdf("Рукав:");
		$pdf->write2pdf("Набираем на спицы ".$this->stitches(round($l5*$this->tx)*2)." + 2 кромочные.");
		while($j<($k-1)){
			$x1=$x2;
			if($j*$hs>=0 && ($j+1)*$hs<=$h2){
				$x1=round($this->width($j*$hs,$l5,0,$l1,$h2)*$this->tx);
				$x2=round($this->width(($j+1)*$hs,$l5,0,$l1,$h2)*$this->tx);
			}
			elseif (($j+1)*$hs<=$h4){
				$x1=round(($cx1-($this->circle3($j*$hs-$cy1,$r1)))*$this->tx);
				$x2=round(($cx1-($this->circle3(($j+1)*$hs-$cy1,$r1)))*$this->tx);
			}
			else{
				$x1=round(($cx2+($this->circle3($j*$hs-$cy2,$r2)))*$this->tx);
				$x2=round(($cx2+($this->circle3(($j+1)*$hs-$cy2,$r2)))*$this->tx);
			}
			if($x1>$x2)
				$pdf->write2pdf("В ".($j+1)." ряду ".$this->closeStitches($x1-$x2)." по ".$this->stitches($x1-$x2)." с каждой стороны (".$this->stitches($x2*2+2,0).").");
			else if($x1<$x2)
					$pdf->write2pdf("В ".($j+1)." ряду прибавить по ".$this->stitches($x2-$x1)." с каждой стороны(".$this->stitches($x2*2+2,0).").");
			
			$j++;
			$h+=$hs;
		}
		$pdf->write2pdf("В ".$k." ряду закрыть все оставшиеся петли.");
		$pdf->write2pdf("Второй рукав вяжем аналогично первому.");
	}
	
	//приведение к естественной речи
	private function stitches($st,$pd=1){
		if ($st!=11 && $st!=12 && $st!=13 && $st!=14){
			$un=$st-intval($st/10)*10;
			if ($un>0){
				if ($un==1){ 
					if ($pd==1)		//винительный падеж
						return $st." петлю";
					return $st." петля";
				}
				if ($un<5 ) return $st." петли";
			}
		}
		return $st." петель";
	}
	
	private function skrowlen($height){
		$h2=$this->Di-14.5;
		if( $height<=$h2 && $height>=0)
			return round($this->width($height,(($this->Cb/2)+3),0,$this->Cb/2,$h2)*$this->tx)*2;
		else	return round($this->width($height,$this->Cb/2,$h2,$this->Ct/2,$this->Di)*$this->tx)*2;
			
	}
	
	private function width($y,$x1,$y1,$x2,$y2){	
		return $x1+(($x1-$x2)/($y1-$y2))*($y-$y1);
	}
	
	private function circle($y,$r,$xc,$yc){
		return sqrt($r*$r-pow(($y-$yc),2))+$xc;
	}
	
	private function circle3($y,$r){
		return $r*cos(asin(abs($y)/$r));
	}
	
	private function circle2($y,$r,$xc,$yc,$dx1,$dx2){
		$y=abs($y-$yc);
		$dx1-=$xc;
		$dx2-=$xc;
		$x=$r*cos(asin($y/$r));
		if ($x>=$dx1 && $x<=$dx2)
			return $xc+$x;
		if (-$x>=$dx1 && -$x<=$dx2)
		return $xc-$x;
	}
	
}
?>