<?php
class palmDOCHeader {
	public $Compression = 0;
	public $TextLength = 0;
	public $Records = 0;
	public $RecordSize = 0;
}

class palmHeader {
	public $Records = array();
}

class palmRecord {
	public $Offset = 0;
	public $Attributes = 0;
	public $Id = 0;
}

class mobiHeader {
	public $Length = 0;
	public $Type = 0;
	public $Encoding = 0;
	public $Id = 0;
	public $FileVersion = 0;

}

class exthHeader {
	public $Length = 0;
	public $Records = array();  
}

class exthRecord {
	public $Type = 0;
	public $Length = 0;
	public $Data = "";
}

class mobi {
	protected $mobiHeader;
	protected $exthHeader;

	public function __construct($file){
		$handle = fopen($file, "r");
		if ($handle){
			fseek($handle, 60, SEEK_SET);
			$content = fread($handle, 8);
			if ($content != "BOOKMOBI"){
				//echo "Invalid file format";
				fclose($handle);
				return;
			}

			// Palm Database
			//echo "\nPalm database:\n";
			$palmHeader = new palmHeader();

			fseek($handle, 0, SEEK_SET);
			$name = fread($handle, 32);
			//echo "Name: ".$name."\n";

			fseek($handle, 76, SEEK_SET);
			$content = fread($handle, 2);
			$records = hexdec(bin2hex($content));
			//echo "Records: ".$records."\n";

			fseek($handle, 78, SEEK_SET);
			for ($i=0; $i<$records; $i++){
				$record = new palmRecord();

				$content = fread($handle, 4);
				$record->Offset = hexdec(bin2hex($content));

				$content = fread($handle, 1);
				$record->Attributes = hexdec(bin2hex($content));

				$content = fread($handle, 3);
				$record->Id = hexdec(bin2hex($content));

				array_push($palmHeader->Records, $record);
				//echo "Record ".$i." offset: ".$record->Offset." attributes: ".$record->Attributes."  id : ".$record->Id."\n";
			}

			// PalmDOC Header
			$palmDOCHeader = new palmDOCHeader();
			fseek($handle, $palmHeader->Records[0]->Offset, SEEK_SET);
			$content = fread($handle, 2);
			$palmDOCHeader->Compression = hexdec(bin2hex($content));
			$content = fread($handle, 2);
			$content = fread($handle, 4);
			$palmDOCHeader->TextLength = hexdec(bin2hex($content));
			$content = fread($handle, 2);
			$palmDOCHeader->Records = hexdec(bin2hex($content));
			$content = fread($handle, 2);
			$palmDOCHeader->RecordSize = hexdec(bin2hex($content));
			$content = fread($handle, 4);

			//echo "\nPalmDOC Header:\n";
			//echo "Compression:".$palmDOCHeader->Compression."\n";
			//echo "TextLength:".$palmDOCHeader->TextLength."\n";
			//echo "Records:".$palmDOCHeader->Records."\n";
			//echo "RecordSize:".$palmDOCHeader->RecordSize."\n";

			// MOBI Header
			$mobiStart = ftell($handle);
			$content = fread($handle, 4);
			if ($content == "MOBI"){
				$this->mobiHeader = new mobiHeader();
				//echo "\nMOBI header:\n";
				$content = fread($handle, 4);
				$this->mobiHeader->Length = hexdec(bin2hex($content));

				$content = fread($handle, 4);
				$this->mobiHeader->Type = hexdec(bin2hex($content));

				$content = fread($handle, 4);
				$this->mobiHeader->Encoding = hexdec(bin2hex($content));

				$content = fread($handle, 4);
				$this->mobiHeader->Id = hexdec(bin2hex($content));

				//echo "Header length: ".$this->mobiHeader->Length."\n";
				//echo "Type: ".$this->mobiHeader->Type."\n";
				//echo "Encoding: ".$this->mobiHeader->Encoding."\n";
				//echo "Id: ".$this->mobiHeader->Id."\n";

				fseek($handle, $mobiStart+$this->mobiHeader->Length, SEEK_SET);
				$content = fread($handle, 4);
				if ($content == "EXTH"){
					$this->exthHeader = new exthHeader();
					//echo "\nEXTH header:\n";

					$content = fread($handle, 4);
					$this->exthHeader->Length = hexdec(bin2hex($content));

					$content = fread($handle, 4);
					$records = hexdec(bin2hex($content));
					//echo "Records: ".$records."\n";

					for ($i=0; $i<$records; $i++){
						$record = new exthRecord();

						$content = fread($handle, 4);
						$record->Type = hexdec(bin2hex($content));

						$content = fread($handle, 4);
						$record->Length = hexdec(bin2hex($content));

						$record->Data = fread($handle, $record->Length - 8);

						array_push($this->exthHeader->Records, $record);
						//echo "Record ".$i." type: ".$record->Type." length: ".$record->Length."\n";
						//echo "  data: ".$record->Data."\n";
					}
				}
			}

			fclose($handle);
		}
	}

	protected function GetRecord($type) {
		foreach ($this->exthHeader->Records as $record){
			if ($record->Type == $type) {
				return $record;
			}
		}
		return NULL;
	}

	protected function GetRecordData($type) {
		$record = $this->GetRecord($type);
		if ($record) {
			return $record->Data;
		}
		return "";
	}

	public function Title() {
		return $this->GetRecordData(503);
	}

	public function Author() {
		return $this->GetRecordData(100);
	}

	public function Isbn() {
		return $this->GetRecordData(104);
	}

	public function Subject() {
		return $this->GetRecordData(105);
	}

	public function Publisher() {
		return $this->GetRecordData(101);
	}
}
?>