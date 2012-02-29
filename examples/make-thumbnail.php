<?php

error_reporting(E_ALL);

require "../ImageMani.class.php";

/* PLEASE DEFINE THIS RIGHT AWAY */
// Defines our webroot - i.e. http://localhost/ImagaMani
define("WEBROOT", "/ImageMani");

$local_path = pathinfo(__FILE__);

$src_dir = "src_imgs";
$dst_dir = "dst_imgs";

$src_dir_full = realpath("../$src_dir");
$dst_dir_full = realpath("../$dst_dir");


$d1 = dir($src_dir_full);

while ( FALSE !== ( $entry = $d1->read())) {

	if ( $entry === "." || $entry === "..") continue;

	$paths = array(
			"INPUT_PATH" => $src_dir_full,
			"OUTPUT_PATH" => $dst_dir_full,
			"FILE" => $entry,
		);

	ImageMani::create($paths)
		->thumbnail("square", FALSE, 150, 150, 0.5, 100);


			
}

$d2 = dir($dst_dir_full);
while ( FALSE !== ( $entry = $d2->read())) {

	if ( $entry === "." || $entry === "..") continue;

	$img = WEBROOT . DIRECTORY_SEPARATOR . $dst_dir . DIRECTORY_SEPARATOR . $entry;
	echo $img . "<br />";
	echo "<img src='$img' /> <br />";

}


