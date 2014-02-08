<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>PHPince Website - Downloader</title>
<style type="text/css">
body { font-family:Tahoma, Geneva, sans-serif; text-align:center; padding:14% 0 0 0; }
h3 { color:#666666;}
</style>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script type="text/javascript">
$(function() {
	$('#downloader').load(function() {
		var iBody = $("#downloader").contents().find("span").html();
		if(iBody){
			alert(iBody);
		}
		$("#downloader").remove();
		window.location = "install/"
	});
});
</script>
</head>
<body>
<h1>PHPince Website - Downloader</h1>
<h3>The system is downloading, please wait a moment ...</h3>
<img src="http://api.phpince.org/cdn/loading01.gif" alt="Loading..." />
<iframe id="downloader" src="index.php?download=ok" style="display:none;"></iframe>
<?php
/*---------------------------------------------------------------------+
| PHPince Website
| Copyright (c) 2011 - 2013 Dominik Hulla
| Web: http://phpince.org
| Author: Dominik Hulla / dh@bladrion.com
| Developer: Bladrion Technologies (http://bladrion.com)
+----------------------------------------------------------------------+
| This program is free software: you can redistribute it and/or modify
| it under the terms of the GNU General Public License as published by
| the Free Software Foundation, either version 3 of the License, or
| (at your option) any later version.
| 
| This program is distributed in the hope that it will be useful,
| but WITHOUT ANY WARRANTY; without even the implied warranty of
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
| GNU General Public License for more details.
| Removal of this copyright header is strictly prohibited 
| without written permission from the original author(s).
| 
| You should have received a copy of the GNU General Public License
| along with this program.  If not, see <http://www.gnu.org/licenses/>.
+----------------------------------------------------------------------*/
if(!empty($_GET["download"])){
	function bl_download($url, $path) {
	$newfname = $path;
	$file = fopen ($url, "rb");
	if ($file) {
		$newf = fopen ($newfname, "wb");
		if ($newf)
			while(!feof($file)) {
				fwrite($newf, fread($file, 1024 * 8 ), 1024 * 8 );
			}
		}
		if ($file) {
			fclose($file);
		}
		if ($newf) {
			fclose($newf);
		}
	}
	function bl_rmdir($dir) {
		if (!file_exists($dir)) return true;
		if (!is_dir($dir) || is_link($dir)) return unlink($dir);
			foreach (scandir($dir) as $item) {
				if ($item == '.' || $item == '..') continue;
				if (!bl_rmdir($dir . "/" . $item)) {
					chmod($dir . "/" . $item, 0777);
					if (!bl_rmdir($dir . "/" . $item)) return false;
				};
			}
			return rmdir($dir);
	}
	function recurse_copy($src,$dst) { 
			$dir = opendir($src); 
			@mkdir($dst); 
			while(false !== ( $file = readdir($dir)) ) { 
				if (( $file != '.' ) && ( $file != '..' )) { 
					if ( is_dir($src . '/' . $file) ) { 
						recurse_copy($src . '/' . $file,$dst . '/' . $file); 
					} else { 
						copy($src . '/' . $file,$dst . '/' . $file); 
					} 
				} 
			} 
			closedir($dir); 
		} 
	if(!is_writable("index.php")){
		echo "<span>[ERROR]: File is no writeable</span>";
		exit;
	}
	if(!ini_get('allow_url_fopen')) {
		echo "<span>[ERROR]: Download is no available, \"allow_url_fopen\" is disabled in php.ini</span>";
		exit;
	} 
	bl_download("https://github.com/bladrioncom/PHPince/archive/master.zip", "bladrionupdate_file.zip");
	if (file_exists("bladrionupdate_file.zip")) {
		$file = 'bladrionupdate_file.zip';
		$zipArchive = new ZipArchive();
		$result = $zipArchive->open($file);
		if ($result === TRUE) {
			$zipArchive ->extractTo(pathinfo(realpath($file), PATHINFO_DIRNAME));
			$zipArchive ->close();
			bl_rmdir("PHPince-master/update");
			recurse_copy("PHPince-master", pathinfo(realpath($file), PATHINFO_DIRNAME));
			bl_rmdir("PHPince-master");
			unlink($file);
		} else {
			echo "<span>[ERROR]: Zip open failed</span>";
		}
	} else {
		echo "<span>[ERROR]: Zip open failed</span>";
	}
}
?>
</body>
</html>