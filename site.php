<?php
	class site{
		private $base='';
		private $baseFolder='';
		private $tempFolder='';
		private $body='';
		private $charset='utf-8';
		private $cssSrc='';
		private $doctype;
		private $debug=false;
		private $endTime=0;
		private $html='5';
		private $meta='';
		private $metaContent='';
		private $metaType='';
		private $s='';
		private $scriptSrc='';
		private $scriptType='';
		private $startTime=0;
		private $title='';
		private $xml='';
		public $db;
		
		public function __construct(){
			$this->startTime=microtime(true);
		}
		public function getTime(){
			$endTime=microtime(true);
			return "time: ".($endTime - $this->startTime);
		}
		public function setDoctype($type){
			$this->html=$type;
			switch($type){
				case '5':
					$this->doctype="<!DOCTYPE HTML>\n<html>\n";
					$this->s='/';
					break;
				case '4frameset':
					$this->doctype='<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd"><html>';
					break;
				case '4strict':
					$this->doctype='<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd"><html>';				
					break;
				case 'xstrict':
					$this->doctype='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html xmlns="http://www.w3.org/1999/xhtml">';
					$this->s='/';					
					break;
				case 'xtransitional':
					$this->doctype='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml">';
					$this->s='/';
					break;
				case 'xframeset':
					$this->doctype='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd"><html xmlns="http://www.w3.org/1999/xhtml">';
					$this->s='/';
					break;
				default:
					$this->doctype='<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"><html>';
					break;			
			}		
		}
		public function debugOn(){
			$this->debug=true;
		}
		private function getDoctype(){
			return $this->doctype;
		}
		public function setCharset($char=''){
			$this->setMeta('Content-Type','text/html; charset='.$char);
			$this->charset=$char;
		}		
		public function setMeta($type='',$content=''){
			$this->metaType[]=$type;
			$this->metaContent[]=$content;
		}
		private function getMeta($nr=''){
			$out='';
			if(is_int($nr)){
				if($this->html!='5'){
					$out="<meta name='{$this->metaType[$nr]}' content='{$this->metaContent[$nr]}'{$this->s}>\n";
				}else{
					$out="<meta charset='{$this->charset}'>\n";	
				}
				$this->wys[$nr]=true;
			}else{
				for($i=0;$i<count($this->metaContent);$i++){
					if($this->wys[$i]!=true){
						$out.="<meta name='{$this->metaType[$i]}' content='{$this->metaContent[$i]}'{$this->s}>\n";
					}
				}
			}
			return $out;
		}
		public function setScript($src='',$type='text/javascript'){
			$this->scriptSrc[]=$src;
			$this->scriptType[]=$type;
		}
		private function getScript($nr=''){
			$out='';
			if($this->scriptSrc!=''){
				if(is_int($nr)){
					$out="<script src='{$this->scriptSrc[$nr]}' type='{$this->scriptType[$nr]}'></script>\n";
					$this->wyss[$nr]=true;
				}else{
					for($i=0;$i<count($this->scriptSrc);$i++){
						if($this->wyss[$i]!=true){
							$out.="<script src='{$this->scriptSrc[$i]}' type='{$this->scriptType[$i]}'></script>\n";
						}
					}
				}
			}
			return $out;
		}
		public function setCss($src=''){
			$this->cssSrc[]=$src;
		}
		private function getCss(){
			$out='';
			if($this->cssSrc!=''){
				for($i=0;$i<count($this->cssSrc);$i++){
					$out.="<link rel='stylesheet' type='text/css' href='{$this->cssSrc[$i]}'{$this->s}>\n";
				}
			}
			return $out;
		}
		public function setTitle($title=''){
			$this->title=$title;
		}
		private function getTitle(){
			return "<title>{$this->title}</title>\n";
		}
		public function setBody($bd){
			$this->body=$bd;
		}
		private function getBody(){
			return $this->body;
		}
		public function setBaseFolder($baseFolder){
			$this->baseFolder=$baseFolder;
		}
		public function setTempFolder($tempFolder){
			if($tempFolder{strlen($tempFolder)-1}=="/"&&strlen($tempFolder)>1)$tempFolder=substr($tempFolder,0,strlen($tempFolder)-1);
			$this->tempFolder=$baseFolder;
		}
		private function getTempFolder(){
			return $this->tempFolder;
		}
		public function setBaseAddr($base){
			$this->base=$base;
		}
		private function getBase(){
			return "<base href='".$this->base."'{$this->s}>\n";
		}
		private function toTemp($input,$fileName){
			$fp = fopen($this->getTempFolder()."/".$fileName.".html", 'w');
			fwrite($fp, $input);
			fclose($fp);
		}
		public function delTemp($file=""){
			$tmp=$this->getTempFolder();
			if($file==""){
				if(!$dh = @opendir($tmp)){
					return;
				}
				while (false !== ($obj = readdir($dh))){
					if($obj == '.' || $obj == '..'){
						continue;
					}
					@unlink($tmp ."/".$obj);
				}
			}else{
				@unlink($tmp."/".$file);
			}
		}
		public function generate($wh=''){
			$out='';
			$out.=$this->xml;
			$out.=$this->getDoctype();
			$out.="<head>\n";
			$out.=$this->getMeta(0);
			$out.=$this->getTitle();
			$out.=$this->getBase();
			$out.=$this->getMeta();
			$out.=$this->getScript();
			$out.=$this->getCss();
			//$out.="<link rel='stylesheet' href='css/mobile.css' type='text/css' media='handheld' />\n";
			//$out.="<link rel='icon' type='image/ico' href='favicon.ico'/>\n<link rel='apple-touch-icon' href='favicon.ico' />\n";
			//$out.="<link rel='alternate' type='application/rss+xml' title='RSS' href='http://z15.pl/rss.xml'/>\n";
			$out.="</head>\n";
			$out.="<body>\n";
			$out.=$this->getBody();
			if($this->debug==true){
				$this->_debug();
				$out.=$this->getTime();
			}
			$out.="</body>\n";
			$out.="</html>";
			return $out;
		}
		public function _debug(){
			echo "<pre style='bottom: 0;height: 200px;margin: 0;overflow: auto;padding: 0;position: fixed;width: 100%;border:1px dotted #999;'>";
			echo "metaType:";
			printf($this->metaType);
			echo "metaContent:";
			printf($this->metaContent);
			echo "scriptSrc:";
			printf($this->scriptSrc);
			echo "scriptType:";
			printf($this->scriptType);
			echo "get:";
			print_r($_GET);
			echo "post:";
			print_r($_POST);
			echo "server:";
			print_r($_SERVER);
			echo "</pre>";
		}
	}
?>
