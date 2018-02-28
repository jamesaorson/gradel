<?php

namespace AppBundle\Utils;

use \DateTime;
use \ZipArchive;

use Symfony\Component\Config\Definition\Exception\Exception;

class Zipper  {
	
	public function zipFiles($source, $destination){
		
		$pattern = '/.+/';
		
		$za = new ZipArchive();		
		
		if($za->open($destination, ZipArchive::OVERWRITE) !== TRUE){
			die("Unable to open zip file to save things to it.");
		}
		
		$options = array('add_path' => './', 'remove_all_path' => TRUE);
		
		$za->addPattern($pattern, $source, $options);
		
		/*
		for($i = 0; $i<$za->numFiles; $i++){
			
			echo $za->statIndex($i)['name']."<br>";
			
		}
		*/
		
		if($za->close() !== TRUE){
			die("Unable to close the zip file.");
		}
		
	}
	
	public function unzipFile($source, $destination){
	
		$za = new ZipArchive();
		
		if($za->open($source) !== TRUE){
			die("Unable to open the zip file for extraction");
		}
		
		/*
		for($i = 0; $i<$za->numFiles; $i++){
			
			echo $za->statIndex($i)['name']."<br>";
			
		}
		*/
		
		if($za->extractTo($destination) !== TRUE){
			die("Unable to extract the zip");
		}
		$za->close();
	}
	
}

?>