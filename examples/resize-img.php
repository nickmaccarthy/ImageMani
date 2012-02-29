<?php

error_reporting(E_ALL);

require "../ImageMani.class.php";

/* PLEASE DEFINE THIS FOR YOUR ENVRIONMENT RIGHT AWAY */
// Defines our webroot - i.e. http://localhost/ImagaMani
define("WEBROOT", "/ImageMani");


$src_dir = "src_imgs";
$dst_dir = "dst_imgs";


$src_dir_full = realpath("../$src_dir").DIRECTORY_SEPARATOR;
$dst_dir_full = realpath("../$dst_dir").DIRECTORY_SEPARATOR;


$d1 = dir($src_dir_full);

while ( FALSE !== ( $entry = $d1->read())) {

	if ( $entry === "." || $entry === "..") continue;


	ImageMani::resize($src_dir_full.$entry, $dst_dir_full, 800, 800, 100);

			
}

$d2 = dir($dst_dir_full);
while ( FALSE !== ( $entry = $d2->read())) {

	if ( $entry === "." || $entry === "..") continue;

	$img = WEBROOT . DIRECTORY_SEPARATOR . $dst_dir . DIRECTORY_SEPARATOR . $entry;
	echo $img . "<br />";
	echo "<img src='$img' /> <br />";

}


