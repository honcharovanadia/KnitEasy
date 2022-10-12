<?php
	
	require_once( 'fpdf.php' );
	define('FPDF_FONTPATH','font/');

	class PDFmethods{
		var $pdf;
		var $dx;
		var $dy;
	
		public function PDFmethods($size='A4'){
			$this->pdf = new FPDF('P','cm',$size);
			$this->pdf->SetTextColor( 0, 0, 0 );
			$this->pdf->SetDrawColor(0, 0, 0);
			$this->pdf->AddFont('Comic','','comic.php');
			$this->pdf-> SetFont('Comic','',12);
			$this->pdf->SetLineWidth(0.15);
			//$this->pdf->AddPage('cm',$size);
		}
				
		public function savePDF($name){
			$this->pdf->Output($name,'I');
		}
		
		public function write2pdf($str){
			$this->pdf->Write( 0.6,$str);
			$this->pdf->Ln(0.75);
		}
		
		public function addPage($size,$dx=0,$dy=0){
			$this->pdf->AddPage("P",$size);
			$this->dx=$dx;
			$this->dy=$dy;
		}
		
		public function line($x1,$y1,$x2,$y2){
			$this->pdf->Line($this->dx+$x1,$this->dy+$y1,$this->dx+$x2,$this->dy+$y2);
		}
		
		public function sizeText($x1,$y1,$x2,$y2,$str,$a=1,$type="x"){
			$this->pdf->SetLineWidth(0.01);
			$fs=$this->pdf->GetFontSize();
			$this->pdf->SetFontSize(20);
			$type=mb_strtolower($type);
			switch ($type){
				case "x":
					$tx=$x1<$x2?$x2+$a:$x1+$a;
					$sx=$a<0?0.3:-0.3;
					$this->line($x1,$y1,$tx,$y1);
					$this->line($x2,$y2,$tx,$y2);
					$this->line($tx+$sx,$y1,$tx+$sx,$y2);
					$this->line($tx+$sx,$y1,$tx+2*$sx,$y1+0.5);
					$this->line($tx+$sx,$y1,$tx,$y1+0.5);
					$this->line($tx+$sx,$y2,$tx+2*$sx,$y2-0.5);
					$this->line($tx+$sx,$y2,$tx,$y2-0.5);
					$this->pdf->SetXY($this->dx+$tx+$sx,$this->dy+($y1>$y2?$y2:$y1)+abs($y2-$y1)/2-0.4);
					$this->pdf->Write(0,$str);
					break;
				case "y":
					$ty=abs($y1)>abs($y2)?$y2-$a:$y1-$a;
					$sy=$a<0?-0.3:0.3;
					$this->line($x1,$y1,$x1,$ty);
					$this->line($x2,$y2,$x2,$ty);
					$this->line($x1,$ty+$sy,$x2,$ty+$sy);
					$this->line($x1,$ty+$sy,$x1+0.5,$ty+$sy*2);
					$this->line($x1,$ty+$sy,$x1+0.5,$ty);
					$this->line($x2,$ty+$sy,$x2-0.5,$ty+$sy*2);
					$this->line($x2,$ty+$sy,$x2-0.5,$ty);
					$this->pdf->SetXY($this->dx+($x1>$x2?$x2+($x1-$x2)/2:$x1+($x2-$x1)/2)-0.4,$this->dy+$ty-$sy*0.5);
					$this->pdf->Write(0,$str);
					break;
				default: 
					echo "gt5hg5yh";
			}
			$this->pdf->SetFontSize($fs);
		}
		
		public function symetrLine($x1,$y1,$x2,$y2){
			$w=$this->pdf->GetLineWidth();
			$this->pdf->SetLineWidth(0.05);
			$this->pdf->MyVertLineP($this->dx+$x1,$this->dy+$y1,$this->dx+$x2,$this->dy+$y2);
			$this->pdf->SetLineWidth($w);
		}
		
		public function setLineWidth($val){
			$this->pdf->SetLineWidth($val);
		}
		
		public function arc($g1,$g2,$r,$cx,$cy,$type="y"){
			$h=0.2;
			$type=mb_strtolower($type);
			switch ($type){
				case "y":
					$x=$g1;
					while($x+$h<=$g2){
						$this->line($x,$this->circle($x,$r,$cx,$cy,$type),
							($x+$h),$this->circle($x+$h,$r,$cx,$cy,$type));
						$x+=$h;
					}
					$this->line($x-$h,$this->circle($x-$h,$r,$cx,$cy,$type),
						$g2,$this->circle($g2,$r,$cx,$cy,$type));
					break;
				case "x":
					$y=$g1;
					while($y+$h<=$g2){
						$this->line($this->circle($y,$r,$cx,$cy,$type),$y,
							$this->circle($y+$h,$r,$cx,$cy,$type),($y+$h));
						$this->line(-$this->circle($y,$r,$cx,$cy,$type),$y,
							-$this->circle($y+$h,$r,$cx,$cy,$type),($y+$h));
						$y+=$h;
					}
					$this->line($this->circle($y-$h,$r,$cx,$cy,$type),$y-$h,
						$this->circle($g2,$r,$cx,$cy,$type),$g2);
					$this->line(-$this->circle(0,$r,$cx,$cy,$type),0,$this->circle(0,$r,$cx,$cy,$type),0);
					break;
				default: return"ERROR";
			}
		}
		
		private function circle($t,$r=0,$xc=0,$yc=0,$restype){
			$type=mb_strtolower($restype);
			switch ($restype){
				case "y":
					return sqrt($r*$r-($t-$xc)*($t-$xc))+$yc;
				case "x":
					return sqrt($r*$r-($t-$yc)*($t-$yc))+$xc;
				default:	
					echo "gt5hg5yh";
			}
		}
		
		public function titleText($title){
			$fs=$this->pdf->GetFontSize();
			$this->pdf->SetFontSize(36);
			$this->pdf->Cell( 0, 2.5, $title, 0, 0, 'C' );
			$this->pdf->Ln();
			$this->pdf->SetFontSize($fs);
		}
	}
?>