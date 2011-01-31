<?php
	
function HexToRGB($hex)
{
	$hex = ereg_replace("#", "", $hex);
	
	$colorsolor = array();
	
	if(strlen($hex) == 3)
	{
		$colorsolor['r'] = hexdec(substr($hex, 0, 1) . $r);
		$colorsolor['g'] = hexdec(substr($hex, 1, 1) . $g);
		$colorsolor['b'] = hexdec(substr($hex, 2, 1) . $b);
	}
	else if(strlen($hex) == 6)
	{
		$colorsolor['r'] = hexdec(substr($hex, 0, 2));
		$colorsolor['g'] = hexdec(substr($hex, 2, 2));
		$colorsolor['b'] = hexdec(substr($hex, 4, 2));
	}
	
	return $colorsolor;
}

function GenerateImage(){

	$params = explode("/", $_GET["p"]);
	
	//set the dimensions
	$dims = explode("x", $params[0]);
	
	if(empty($dims[1]))
	{
		$dims = explode(".", $dims[0]);
		$ext = $dims[1];
		$dims[1] = $dims[0];
	}
	else{
		$file_type = explode(".", $dims[1]);
		$dims[1] = $file_type[0];
		$ext = $file_type[1];
	}
	
	//set the colors for the background and text
	if(empty($params[1]))
	{
		$params[1] = "666666";
	}
	else
	{
		$colors = explode(".", $params[1]);
		$ext = $colors[1];
		$params[1] = $colors[0];
	}
	
	if(empty($params[2]))
	{
		$params[2] = "ffffff";
	}
	else
	{
		$colors = explode(".", $params[2]);
		$ext = $colors[1];
		$params[2] = $colors[0];
	}
	
	//convert hex to RGB
	$bkgd_rgb = HexToRGB($params[1]);
	$text_rgb = HexToRGB($params[2]);
	
	//build the image
	$img = imagecreate($dims[0], $dims[1]);
	$bkgd_color = imagecolorallocate($img, $bkgd_rgb['r'], $bkgd_rgb['g'], $bkgd_rgb['b']);	
	$text_color = imagecolorallocate( $img, $text_rgb['r'], $text_rgb['g'], $text_rgb['b']);
	
	//set the text
	if(empty($_GET['text']))
	{
		$text = $dims[0]." x ".$dims[1];
	}
	else
	{
		$text = $_GET['text'];
	}
	
	//set the font
	$font = 'DroidSansMono.ttf'; 
	
	//scale font size based on image dimensions
	$font_size = $dims[0]/16;
	$font_angle = 0;
	
	//determine the coordinates for centering the text
	$font_bounds = imagettfbbox($font_size, $font_angle, $font, $text);
	$font_width = ImageFontWidth($font);
	$text_height = $font_height = ImageFontHeight($font);
	$text_width = $font_size * strlen($text);
	$x = ceil(($dims[0] - $font_bounds[2]) / 2);
	$y = ceil(($dims[1]- $font_bounds[5]) / 2);
	
	//build the text
	imagettftext($img, $font_size, $angle, $x, $y, $text_color, $font, $text);
	
	//determine the file type
	switch ($ext) {
	    case 'png':
		header("Content-type: image/png");
			imagepng( $img );
		break;
	    case 'gif':
		header("Content-type: image/gif");
			imagegif( $img );
		break;
	    case ('jpg'||'jpeg'):
		header("Content-type: image/jpeg");
			imagejpeg( $img );
		break;
	    default:
	       header("Content-type: image/png");
		   imagepng( $img );
	}
	
	imagecolordeallocate( $text_color );
	imagecolordeallocate( $bkgd_color );
	imagedestroy( $img );

}

?>