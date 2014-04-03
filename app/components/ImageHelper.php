<?php

class ImageHelper {
	const JPEG_Q = 90; // jpeg quality
	const PNG_C = 9; // png compression

	private $_path;
	private $_max_dimension;
	private $_parts;
	private $_type;
	private $_image;
	private $_horizontal;
	private $_width;
	private $_height;
	private static $_alpha;
	private static $_alpha_width;
	private static $_alpha_height;

	public function __construct($max_dimension, $parts) {
		if (!function_exists('gd_info')) throw new Exception('gd required');
		$info = gd_info();
		if (strpos($info['GD Version'], '2.') === false) throw new Exception('gd version != 2.x ');

		$this->_image = false;
		$this->_max_dimension = $max_dimension;
		$this->_parts = $parts;
	}

	public function getType() {	
		return $this->_type;
	}

	public function load($path) {
		$size = getimagesize($path);

		if (($size === false) ||
			($size[0] > $this->_max_dimension) ||
			($size[1] > $this->_max_dimension) ||
			($size[0] < $this->_parts) ||
			($size[1] < $this->_parts)) return false;

		$this->_path = $path;
		$this->_width = $size[0];
		$this->_height = $size[1];

		switch ($size['mime']) {
			case 'image/jpeg':
				$this->_image = @imagecreatefromjpeg($this->_path);
				break;
			case 'image/png':
				$this->_image = @imagecreatefrompng($this->_path);
				break;
			case 'image/gif':
				$this->_image = @imagecreatefromgif($this->_path);
				break;
			default:
				return false;
		}

		if ($this->_image === false) return false;

		$this->_type = substr($size['mime'], 6);
		$this->_horizontal = ($this->_width >= $this->_height);
		return true;
	}

	private function _createTransparentImage($width, $height)
	{
		if (self::$_alpha === null)
		{
			self::$_alpha = imagecreatefromstring(base64_decode
			(
				'iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29'.
				'mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAADqSURBVHjaYvz//z/DYAYAAcTEMMgBQAANegcCBN'.
				'CgdyBAAA16BwIE0KB3IEAADXoHAgTQoHcgQAANegcCBNCgdyBAAA16BwIE0KB3IEAADXoHAgTQoHcgQ'.
				'AANegcCBNCgdyBAAA16BwIE0KB3IEAADXoHAgTQoHcgQAANegcCBNCgdyBAAA16BwIE0KB3IEAADXoH'.
				'AgTQoHcgQAANegcCBNCgdyBAAA16BwIE0KB3IEAADXoHAgTQoHcgQAANegcCBNCgdyBAAA16BwIE0KB'.
				'3IEAADXoHAgTQoHcgQAANegcCBNCgdyBAgAEAMpcDTTQWJVEAAAAASUVORK5CYII='
			));
			self::$_alpha_width = imagesx(self::$_alpha);
			self::$_alpha_height = imagesy(self::$_alpha);
		}

		$image = imagecreatetruecolor($width, $height);
		if ($image === false) return false;

		$ret = imagecopyresized($image, self::$_alpha, 0, 0, 0, 0, $width, $height, self::$_alpha_width, self::$_alpha_height);
		if ($ret === false) {
			imagedestroy($image);
			return false;
		}

		imagealphablending($image, false);
		imagesavealpha($image, true);

		return $image;
	}	

	public function resize($max) {
		if ($this->_image === false) return false;

		// fix max
		$mod = $max % $this->_parts;
		$max = $max - $mod;

		if ($this->_horizontal) {
			if ($max >= $this->_width) {
				$mod = $this->_width % $this->_parts;
				if ($mod == 0) return true; // no resize required 
				else {
					$new_width = $this->_width - $mod;
				}
			} else {
				$new_width = $max;
			}
			$new_height = round($new_width/$this->_width*$this->_height);
		} else {
			if ($max >= $this->_height) {
				$mod = $this->_height % $this->_parts;
				if ($mod == 0) return true; // no resize required 
				else {
					$new_height = $this->_height - $mod;
				}
			} else {
				$new_height = $max;
			}
			$new_width = round($new_height/$this->_height*$this->_width);
		}

		imagealphablending($this->_image, true);
		imagesavealpha($this->_image, true);

		$new_image = $this->_createTransparentImage($new_width, $new_height);
		if ($new_image === false) return false;

		$ret = imagecopyresized($new_image, $this->_image, 0, 0, 0, 0, $new_width, $new_height, $this->_width, $this->_height);
		if ($ret === false) {
			imagedestroy($new_image);
			return false;
		}

		$this->_width = $new_width;
		$this->_height = $new_height;

		imagedestroy($this->_image);
		$this->_image = $new_image;

		return true;
	}

	public function save($path) {
		if ($this->_image === false) return false;

		switch ($this->_type) {
			case 'jpeg':
				return imagejpeg($this->_image, $path, self::JPEG_Q);
			case 'png':
				return imagepng($this->_image, $path, self::PNG_C);
			case 'gif':
				return imagegif($this->_image, $path);
			default:
				return false;
		}			
	}

	public function cleanup() {
		if ($this->_image === false) return false;
		imagedestroy($this->_image);
		return true;
	}

	public function saveCrop($x, $y, $w, $h, $path) {
		if ($this->_image === false) return false;

		$new_image = $this->_createTransparentImage($w, $h);
		if ($new_image === false) return false;


		$ret = imagecopy($new_image, $this->_image, 0, 0, $x, $y, $w, $h);
		if ($ret === false) goto CLEANUP;

		switch ($this->_type) {
			case 'jpeg':
				$ret = imagejpeg($new_image, $path, self::JPEG_Q);
				break;
			case 'png':
				$ret = imagepng($new_image, $path, self::PNG_C);
				break;
			case 'gif':
				$ret = imagegif($new_image, $path);
				break;
			default:
				$ret = false;
		}

	CLEANUP:
		imagedestroy($new_image);
		return $ret;		
	}

	public function isHorizontal() {
		return $this->_horizontal;
	}

	public function getHeight() {
		return $this->_height;
	}

	public function getWidth() {
		return $this->_width;
	}	

	public function partSize() {
		if ($this->_horizontal) {
			return floor($this->_width / $this->_parts);
		} else {
			return floor($this->_height / $this->_parts);
		}
	}	
}
