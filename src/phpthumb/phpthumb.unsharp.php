<?php



class phpUnsharpMask {

	static function applyUnsharpMask(&$img, $amount, $radius, $threshold) {

		// $img is an image that is already created within php using
		// imgcreatetruecolor. No url! $img must be a truecolor image.

		// Attempt to calibrate the parameters to Photoshop:
		$amount = min($amount, 500) * 0.016;
		$radius = abs(round(min(50, $radius) * 2)); // Only integers make sense.
		$threshold = min(255, $threshold);
		if ($radius == 0) {
			return true;
		}
		$w = ImageSX($img);
		$h = ImageSY($img);
		$imgCanvas = ImageCreateTrueColor($w, $h);
		$imgBlur   = ImageCreateTrueColor($w, $h);

		// Gaussian blur matrix:
		//
		//    1    2    1
		//    2    4    2
		//    1    2    1
		//
		//////////////////////////////////////////////////

		if (function_exists('imageconvolution')) { // PHP >= 5.1
			$matrix = array(
				array(1, 2, 1),
				array(2, 4, 2),
				array(1, 2, 1)
			);
			ImageCopy($imgBlur, $img, 0, 0, 0, 0, $w, $h);
			ImageConvolution($imgBlur, $matrix, 16, 0);

		} else {

			// Move copies of the image around one pixel at the time and merge them with weight
			// according to the matrix. The same matrix is simply repeated for higher radii.
			for ($i = 0; $i < $radius; $i++)    {
				ImageCopy(     $imgBlur,   $img,       0, 0, 1, 0, $w - 1, $h);               // left
				ImageCopyMerge($imgBlur,   $img,       1, 0, 0, 0, $w    , $h,     50);       // right
				ImageCopyMerge($imgBlur,   $img,       0, 0, 0, 0, $w    , $h,     50);       // center
				ImageCopy(     $imgCanvas, $imgBlur,   0, 0, 0, 0, $w    , $h);
				ImageCopyMerge($imgBlur,   $imgCanvas, 0, 0, 0, 1, $w    , $h - 1, 33.33333); // up
				ImageCopyMerge($imgBlur,   $imgCanvas, 0, 1, 0, 0, $w    , $h,     25);       // down
			}
		}

		if ($threshold > 0){
			// Calculate the difference between the blurred pixels and the original
			// and set the pixels
			for ($x = 0; $x < $w-1; $x++)    { // each row
				for ($y = 0; $y < $h; $y++)    { // each pixel

					$rgbOrig = ImageColorAt($img, $x, $y);
					$rOrig = (($rgbOrig >> 16) & 0xFF);
					$gOrig = (($rgbOrig >>  8) & 0xFF);
					$bOrig =  ($rgbOrig        & 0xFF);

					$rgbBlur = ImageColorAt($imgBlur, $x, $y);

					$rBlur = (($rgbBlur >> 16) & 0xFF);
					$gBlur = (($rgbBlur >>  8) & 0xFF);
					$bBlur =  ($rgbBlur        & 0xFF);

					// When the masked pixels differ less from the original
					// than the threshold specifies, they are set to their original value.
					$rNew = ((abs($rOrig - $rBlur) >= $threshold) ? max(0, min(255, ($amount * ($rOrig - $rBlur)) + $rOrig)) : $rOrig);
					$gNew = ((abs($gOrig - $gBlur) >= $threshold) ? max(0, min(255, ($amount * ($gOrig - $gBlur)) + $gOrig)) : $gOrig);
					$bNew = ((abs($bOrig - $bBlur) >= $threshold) ? max(0, min(255, ($amount * ($bOrig - $bBlur)) + $bOrig)) : $bOrig);

					if (($rOrig != $rNew) || ($gOrig != $gNew) || ($bOrig != $bNew)) {
						$pixCol = ImageColorAllocate($img, $rNew, $gNew, $bNew);
						ImageSetPixel($img, $x, $y, $pixCol);
					}
				}
			}
		} else {
			for ($x = 0; $x < $w; $x++)    { // each row
				for ($y = 0; $y < $h; $y++)    { // each pixel
					$rgbOrig = ImageColorAt($img, $x, $y);
					$rOrig = (($rgbOrig >> 16) & 0xFF);
					$gOrig = (($rgbOrig >>  8) & 0xFF);
					$bOrig =  ($rgbOrig        & 0xFF);

					$rgbBlur = ImageColorAt($imgBlur, $x, $y);

					$rBlur = (($rgbBlur >> 16) & 0xFF);
					$gBlur = (($rgbBlur >>  8) & 0xFF);
					$bBlur =  ($rgbBlur        & 0xFF);

					$rNew = min(255, max(0, ($amount * ($rOrig - $rBlur)) + $rOrig));
					$gNew = min(255, max(0, ($amount * ($gOrig - $gBlur)) + $gOrig));
					$bNew = min(255, max(0, ($amount * ($bOrig - $bBlur)) + $bOrig));
					$rgbNew = ($rNew << 16) + ($gNew <<8) + $bNew;
					ImageSetPixel($img, $x, $y, $rgbNew);
				}
			}
		}
		ImageDestroy($imgCanvas);
		ImageDestroy($imgBlur);
		return true;
	}
}
?>