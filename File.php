<?php
namespace quintenvk;
class File {
	public static function handleUpload($file, $path, $nameWithoutExtension, $thumbWidth = null) {
		$old = umask(0);
		if(!is_dir($path)){
			mkdir($path, 0777, true);
			$exploded = explode('/', rtrim('/', $path));
			$length = count($exploded) -1;
			for($i = 0; $i < $length; $i++) {
				array_pop($exploded); //pop off the last one every time. First one was already set so works perfectly.
				$chmod_path = implode('/', $exploded);
				chmod($chmod_path, 0777);
			}
		}
		umask($old);

		$extension = self::getExtension($file['name']);
		$destination = $path.$nameWithoutExtension.'.'.$extension;
		move_uploaded_file($file['tmp_name'], $destination);

		if($thumbWidth){
			Image::createThumb($destination, 100);
		}

		return $destination;
	}

	public static function delete($filename) {
		// an array
		if(is_array($filename)) foreach($filename as $file) @unlink((string) $file);

		// string
		else return @unlink((string) $filename);
	}

	public static function getContent($filename) {
		return @file_get_contents((string) $filename);
	}

	public static function getExtension($filename, $lowercase = true) {
		$explode = explode('.', $filename);
		$extension = end($explode);
		return $lowercase ? strtolower($extension) : $extension;
	}

	public static function getInfo($filename) {
		// redefine
		$filename = (string) $filename;

		// init var
		$units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');

		// fetch pathinfo
		$pathInfo = pathinfo($filename);

		// clear cache
		@clearstatcache();

		// build details array
		$file = array();
		$file['basename'] = $pathInfo['basename'];
		$file['extension'] = self::getExtension($filename);
		$file['name'] = substr($file['basename'], 0, strlen($file['basename']) - strlen($file['extension']) -1);
		$file['size'] = @filesize($filename);
		$file['is_executable'] = @is_executable($filename);
		$file['is_readable'] = @is_readable($filename);
		$file['is_writable'] = @is_writable($filename);
		$file['modification_date'] = @filemtime($filename);
		$file['path'] = $pathInfo['dirname'];
		$file['permissions'] = @fileperms($filename);

		// calculate human readable size
		$size = $file['size'];
		$mod = 1024;
		for($i = 0; $size > $mod; $i++) $size /= $mod;
		$file['human_readable_size'] = round($size, 2) . ' ' . $units[$i];

		// clear cache
		@clearstatcache();

		// cough it up
		return $file;
	}
}

class FileException extends \Exception {}
