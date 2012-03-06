<?php

/**
* ImageMani
*
* @author	Nick MacCarthy <nickmaccarthy@gmail.com>
* 
* @version 	1
* 
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

class ImageMani {

    // Our input img we will be manipulating
	public static $input_img;

	// Our input path where the source image will reside
	public static $input_path;

	// The path where will put our modified image
	public static $output_path;

	// The filename of our input image with no path information
	public static $filename;

	/**
	* Intializes class, and sets input and output directories for the image
	*
	* @param	array	$paths	Our input img and our output directory
	* @return   object
	*/
	public static function create( array $paths)
	{

		self::$input_path = $paths['INPUT_PATH'];
		self::$output_path = $paths['OUTPUT_PATH'];
		self::$input_img = $paths['INPUT_PATH'] . DIRECTORY_SEPARATOR . $paths['FILE'];
		self::$filename = $paths['FILE'];

		return new self;
	}

	/**
	* Creates thumbnail for an image.
	* Will create a thumbnail in cropped square, or a resized version of the orginal depening on $thumb_type
	* Currently outputs thumbnail into $output_directory only
	* If used with $check_existing, you can check to see if your thumbnail exists in your output directory before you create a new thumb. Very useful when you want this to 'monitor' a directory for new files to thumbnail
	*
	* @param	string	$img		source image you wnat to thumbnail
	* @param	string	$output_dir	the directory where you want your thumbnail to be placed
	* @param 	boolean	$check_existing check to see if the thumnail already exists in your output dir, if it does, it will skip making a thumbnail
	* @param 	string	$thumb_type	specifies the type of thumbnail you want to create.  currently supports a cropped square version, or a small resized versino of the source $img
	* @param 	int 	$dst_width	the width you want for your new thumbnail
	* @param 	int 	$dst_height 	the height you want for your new thumbnail
	* @param 	double	$crop_factor	the crop factor used from the original picture.  Higher = less crop from the orig, Lower = more crop.  0.5 seems to be the best
	* @param 	int 	$quality 	the quality you want for you thumbnail.
	*
	* @return boolean   TRUE if new image creation was successful, FALSE if not
	*/
	public static function thumbnail($thumb_type = "square", $check_existing = FALSE, $dst_width = 150, $dst_height = 150, $crop_factor = "2", $quality = "100") 
	{

		$img = self::$input_img;
		$output_dir = self::$output_path;
		
		// only jpeg support at this time
		if ( exif_imagetype($img) !== IMAGETYPE_JPEG) return;

		$cropped_img = $output_dir . DIRECTORY_SEPARATOR .  basename($img);

		// if set, will check to see if our 'cropped image' already exists, if it does, then we dont need to go any further 
		if( $check_existing)
		{
			if( is_file($cropped_img)) return;
		}


		if ( $thumb_type === "square")
		{
			$pic_info = ImageMani::getinfo($img);

			if( ! is_array($pic_info)) return;

			$width = $pic_info[basename($img)]['IMAGESIZE'][0];
			$height = $pic_info[basename($img)]['IMAGESIZE'][1];
	

			/**
			* with the 'square' thumbnail type, its important for us to determine if our image is  'landscape', or 'portrait'.
			* this is so we accurately crop from the middle of our input image.
			*
			* 'biggest' side determines if the img is landscape or portrait, and then we act accordingly
			*/
			if ( $width > $height)
			{

				$biggest_side = $width;

				// figure out where we will crop from
				$crop_width = $biggest_side * $crop_factor;
				$crop_height = $biggest_side * $crop_factor;

				// define our crop array, ensuring we are pulling from the 'middle' of our input img
				$c1 = array(
					'x' => ($width - $crop_width) / 2,
					'y' => ($height - $crop_height) / 2
					);


				// stage our new image
				$src_img = imagecreatefromjpeg($img);
				$dst_img = imagecreatetruecolor($dst_width, $dst_height);

				// create our new image
				imagecopyresampled($dst_img, $src_img, 0, 0, $c1['x'], $c1['y'], $dst_width, $dst_height, $crop_width, $crop_height);

				// output our new image
				$created = imagejpeg($dst_img, $cropped_img, $quality);


				// free up memory
				imagedestroy($src_img);
				imagedestroy($dst_img);
				

			} 
			else if ( $height > $width)
			{
				$biggest_side = $height;
		
				$crop_width = $biggest_side * $crop_factor;
				$crop_height = $biggest_side * $crop_factor;

				$c1 = array(
					'x' => ( ($width - $crop_width) / 2),
					'y' => ( ($height - $crop_height) / 2)
					);
			
					
				$src_img = imagecreatefromjpeg($img);
				$dst_img = imagecreatetruecolor($dst_width, $dst_height);

				imagecopyresampled($dst_img, $src_img, 0, 0, $c1['x'], $c1['y'], $dst_width, $dst_height, $crop_width, $crop_height);

				// Make our new img
				$created = imagejpeg($dst_img, $cropped_img, $quality);

				// free up memory
				imagedestroy($src_img);
				imagedestroy($dst_img);
			}
			else if ($width === $height) 
			{
				$src_img = imagecreatefromjpeg($cropped_img);
				$dst_img = imagecreatetruecolor($dst_width, $dst_height);

				$c1 = array(
					'x' => $width,
					'y' => $height
					);

				imagecopyresized($dst_img, $src_img, 0, 0, 0, 0, $dst_width, $dst_height, $width, $height);

				$created = imagejpeg($dst_img, $cropped_img, $quality);

				// free up memory
				imagedestroy($src_img);
				imagedestroy($dst_img);

			}
			else 
			{
				// An error has occured -- this means we wernt able to accurately find our biggest side -- this should never happen

				print_r($pic_info);

				return FALSE;
			}

			if ( $created) return TRUE;
		}
		else if ( $thumb_type === "default")
		{
			ImageMani::resize($img, $output_dir, $dst_width, $dst_height, $quality);

		}

	} 

	/**
	* resizes image while keeping original proportions from our input img intact
	*
	* @param	string	$src_img	source image you wish you to resize
	* @param	string	$output_dir	directory where you wish to output your resized image to
	* @param	int	    $dst_width	what width you wish to resize to
	* @param	int	    $dst_height	what height you wish to resize to
	* @param	int	    $quality	output quality you wish to receive after img has been resized 
	*
	* @return boolean   TRUE if image creation was successful, FALSE if not
	*/
	public static function resize($src_img, $output_dir, $max_width = 400, $max_height = 400, $quality = 100)	
	{

		$cropped_img = $output_dir . DIRECTORY_SEPARATOR .  basename($src_img);

		$pic_info = self::getinfo($src_img);

		if( ! is_array($pic_info)) return false;

		$width = $pic_info[basename($src_img)]['IMAGESIZE'][0];
		$height = $pic_info[basename($src_img)]['IMAGESIZE'][1];	
			
		$ratio_orig = $width / $height;
		$resize_ratio = $max_width / $max_height;

		if($resize_ratio > $ratio_orig)
		{
			$dst_width = $max_height * $ratio_orig;
			$dst_height = $max_height;
		} 
		else 
		{
			$dst_height = $max_width / $ratio_orig;
			$dst_width = $max_width;
		}
		
		$src = imagecreatefromjpeg($src_img);
		$dst = imagecreatetruecolor($dst_width, $dst_height);

		imagecopyresampled($dst, $src, 0, 0, 0, 0, $dst_width, $dst_height, $width, $height);

		$created = imagejpeg($dst, $cropped_img, $quality);

		// free up memory
		imagedestroy($src);
		imagedestroy($dst);

		if ( ! $created) return false;

		return TRUE;
	}

	/**
	*  gets exif, and image size information for the pic given
	*
	* @param	$img	full path to image you want information for
	* 
	* @return 	array   EXIF and Image dimensions in associative array -- Array( "EXIF" => <img_exif_data>, "IMAGESIZE" => <img_dimensions> )
	*/
	public static function getinfo($img)
	{ 

		if ( is_file($img))
		{

			$exif_array = read_exif_data($img);
			$info[basename($img)] = array( 
						"IMAGESIZE" => getimagesize($img), 
						"EXIF" => $exif_array
						);

			return $info;
		}
		else return FALSE;
	}

} // End ImageMani
