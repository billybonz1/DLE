<?
	/*
	+-----------------------------------+
	|                                   |
	|   Microsoft Excel v5 file writer  |
	|   By Ali Imran ali@gimptalk.com   |
	|                                   |
	|   Copyrights reserverd:           |
	|   www.swish-db.com                |
	|                                   |
	+-----------------------------------+
	*/

	#Microsoft Excel 5 Header and End Of File Binary Representation
	define("XLSFile_Header",	pack("s*",2057,8,0,0,0,0));
	define("XLSFile_End",		pack("s*",10,0));

	#Following function return code for an Excel Worksheet Cell
	function get_xlscell_code($x,$y,$cell_txt) {
		$ret = pack("s*",516,strlen($cell_txt)+8,$y,$x,0,strlen($cell_txt));
		$ret .= $cell_txt;
		return $ret;
	}
	class XLS {
		var $_final_bytes  = "";
		var $_file_name = 'text.xls';
		
		function XLS() { }
		
		function add_cell($COLUMN,$ROW,$cell_text) {
			$this->_final_bytes .= get_xlscell_code($COLUMN,$ROW,$cell_text);
		}
		function xls_bytes() {
			return XLSFile_Header.$this->_final_bytes.XLSFile_End;
		}
		function save_file($fname='',$_overwrite=true) {
			$fname = $fname==''  ? $this->_file_name : $fname;
			$f = @fopen($fname,"r");
			if($f and !$_overwrite) {
				@fclose($f);
				return false;
			}
			@fclose($f);
			$f = @fopen($fname,"w");
			if(!$f) return false;
			$bytes = $this->xls_bytes();
			if(!@fwrite($f,$bytes)) return false;
			@fclose($f);
			
			return true;
		}
	}

?>