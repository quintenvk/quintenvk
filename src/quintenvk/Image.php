<?php
namespace quintenvk;
class Image {
	/**
	 * basic functions that won't be used too often outside this class
	 */
	private $resource;
	private $type;

	public function __construct($file = null) {
		if($file) {
			$this->createResource($file);
		}
	}

	public function createResource($file) {
	    $this->type = File::getExtension($file);

	    switch($this->type) {
	    	case 'jpg':
	    	case 'jpeg':
	    		$this->resource = imagecreatefromjpeg($file);
	    		break;

	    	case 'gif':
	    		$this->resource = imagecreatefromgif($file);
	    		break;

	    	case 'png':
	    		$this->resource = imagecreatefrompng($file);
	    		break;

	    	case 'bmp':
	    		$this->resource = imagecreatefromwbmp($file);
	    		break;
	    }
	}

	public function saveFile($filename) {
	    switch($this->type) {
	    	case 'jpg':
	    	case 'jpeg':
	    		imagejpeg($this->resource, $filename);
	    		break;

	    	case 'gif':
	    		imagegif($this->resource, $filename);
	    		break;

	    	case 'png':
	    		imagepng($this->resource, $filename);
	    		break;

	    	case 'bmp':
	    		imagewbmp($this->resource, $filename);
	    		break;
	    }
	}

	/**
	 * This is where the useful & fun stuff starts
	 */

	public static function createThumb($file, $thumbWidth, $newFile = null) {
	    $newFile = $newFile ? $newFile : $file;
	    $img = new self($file);

		$width = imagesx($img->resource);
		$height = imagesy($img->resource);

		$new_width = $thumbWidth;
		$new_height = floor($height * ($thumbWidth / $width ));

		$tmp_img = imagecreatetruecolor($new_width, $new_height);
		imagecopyresized( $tmp_img, $img->resource, 0, 0, 0, 0, $new_width, $new_height, $width, $height );

		$img->resource = $tmp_img;
		$img->saveFile($newFile);
	}

	public static function crop($file, $newWidth, $newHeight, $origWidth, $origHeight, $startX, $startY, $newFile = null) {
	    $newFile = $newFile ? $newFile : $file;
	    $img = new self($file);


		$tmp_img = imagecreatetruecolor($newWidth, $newHeight);
		imagecopyresized( $tmp_img, $img->resource, 0, 0, $startX, $startY, $newWidth, $newHeight, $origWidth, $origHeight);

		$img->resource = $tmp_img;
		$img->saveFile($newFile);
	}
}