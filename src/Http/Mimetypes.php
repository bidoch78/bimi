<?php

namespace Bidoch78\Bimi\Http;

class MimeTypes {
	
	// private $_mimeTypesFolder = null;
	// private $_mimeTypesName = "mimetypes.json";
	// private $_mimeTypesUrl = "http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types";
	// private $_mimeTypes = null;
	
	// function __construct(array $option = null) {
	// 	if (is_array($option)) {
	// 		if (isset($option["cache_folder"])) $this->_mimeTypesFolder = $option["cache_folder"];
	// 		if (isset($option["cache_filename"])) $this->_mimeTypesName = $option["cache_filename"];
	// 	}
	// 	$this->generateJsonFile();
	// 	$this->loadMimeTypes();
	// }
	
	// public function getMimeTypeOfFile($filename) {
	// 	$fileext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
	// 	return isset($this->_mimeTypes[$fileext]) ? $this->_mimeTypes[$fileext] : "application/octet-stream";
	// }
	
	// private function getJsonFile() {
	// 	return ($this->_mimeTypesFolder ? $this->_mimeTypesFolder : __DIR__) . DIRECTORY_SEPARATOR . $this->_mimeTypesName;
	// }
	
	// private function loadMimeTypes() {
		
	// 	$this->_mimeTypes = array();

	// 	$fileMimeTypes = $this->getJsonFile();
	// 	if (is_file($fileMimeTypes)) { 
	// 		$json = (array)@json_decode(file_get_contents($fileMimeTypes));
	// 		if ($json !== false) $this->_mimeTypes = $json;
	// 	}
		
	// }
	
	// private function generateJsonFile() {
		
	// 	$fileMimeTypes = $this->getJsonFile();
	// 	if (!is_file($fileMimeTypes)) {
			
	// 		$mimeTypes = array();
	// 		foreach(explode("\n", @file_get_contents($this->_mimeTypesUrl)) as $line) {
	// 			$line = explode("#", $line);
	// 			$line = trim($line[0]);
	// 			if (strlen($line)) {
	// 				$stringMimeType = null;
	// 				foreach(explode("\t", $line) as $info) {
	// 					if (!$stringMimeType) {
	// 						$stringMimeType = trim($info);
	// 					}
	// 					else {
	// 						if (strlen($info)) {
	// 							foreach(explode(" ", $info) as $ext) {
	// 								$ext = trim($ext);
	// 								if (strlen($ext)) $mimeTypes[$ext] = $stringMimeType;
	// 							}
	// 						}
	// 					}
	// 				}
	// 			}
	// 		}
	// 		if (count($mimeTypes)) {
	// 			file_put_contents($fileMimeTypes, json_encode($mimeTypes));
	// 		}
			
	// 	}
		
	// }
	
	// private static $_singleton = null;
	
	// private static function getInstance(array $options = null) {
	// 	if (self::$_singleton) return self::$_singleton;
	// 	self::$_singleton = new self($options);
	// 	return self::$_singleton;
	// }
	
	// public static function initialize(array $options = null) {
	// 	return self::getInstance($options);
	// }
	
	// public static function getMimeType($filename) {
	// 	return self::getInstance()->getMimeTypeOfFile($filename);
	// }
	
}
