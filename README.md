Description:

ImagMani is a very simple PHP class that is used to manipulate images using the GD Image library.  

Some notable features include the creation of thumbnails from bigger images in a 'square' or 'default (just resized smaller)' format.

It will also resize a bigger image to a smaller one (or vise versa) keeping original proprtions intact.

Supports only JPEG at the moment.

Example Usage:

This will create a square thumbnail, checking to see if our output img already exists in our output path, with a max height and width of 150px, a crop factor of 0.5 and with a 100% output image quality

      // Create an array defining our input and output paths, as well as our image file with no path information
      $paths = array( "INPUT_PATH" => "/path/to/my/src/img", "OUTPUT_PATH" => "/path/to/my/dst/img", "FILE" => "foo.jpg");

      // Now create the thumbnail 
      ImageMani::create($paths)
      		->thumbnail("square", TRUE, 150, 150, 0.5, 100);

If you want to resize an image:

	ImageMani::resize($our_src_img, $output_directory, $max_width, $max_height, $output_quality);



Next update:
      Add ability to output using a specified name, i.e. src = 'foo.jpg', output = 'foo_thumbnail.jpg'
      Include support for other formats (mainly gif and png)
      Add a 'sharpening' option
      Add a watermarking ability
      Add a 'text to image' option

License:

The ImageMani source code is released under the GPL v3 and MIT licenses.  The images included in ImageMani are copyright Nick MacCarthy 2011 (http://www.nickmaccarthy.com) and are for example only.  You are more then welcome to use ImageMani in your own projects, the only thing I ask is that if you make it better, to fork and add your updated code to this project.

Requirements:

*PHP 5.2 or higher
*GD Image Library

