<?php

/*
 * This class is intended to change a wrong extension of the imagefile to the right one
 * 
 * @author Vladislav Holovko <vlad.holovko@gmail.com> 30 May 2013
 */

class ImgFileExtension {
    
    /*
     * The last occured error message
     */
    protected static $_error;

    /**
	 * Gets the right extension of the image file according to the image type
	 * @param string $filename source image filename
     * @param boolean $short if set to true changes extension to the short version (.jpeg => .jpg)
	 * @return string|boolean in case of an error returns false, otherwise returns the file extension
	 */
    public static function getReal($filename, $short = true) {
        if (false === $type = exif_imagetype($filename)) {
            self::$_error = "Unsupported image type or source file is empty.";
            return false;
        }
        $replacements = array(
            '.jpeg' => '.jpg',
            '.tiff' => '.tif',
        );
        $ext = image_type_to_extension($type);
        if ($short)
            $ext = strtr($ext, $replacements);
        return $ext;
    }
    
    /**
	 * Gets current extension of the image file
	 * @param string $filename the source image filename
     * @param boolean $strict if true then only files with extension are acceptable, default false
	 * @return string|boolean in case of an error returns false, otherwise returns the file extension
	 */
    public static function getCurrent($filename, $strict = false) {
        if(false === $pos = strrpos($filename, '.')) {
            if ($strict) {
                self::$_error = "Source file has no extension.";
                return false;
            }
            else
                return "";
        }
        return strtolower(substr($filename, $pos+1));
    }

    /**
	 * Renames the source file (if needed) to the version with a correct extension.
     * After renaming a content of the $filename variable replaced with a new value. 
	 * @param string $filename the source image filename
     * @param boolean $strict parameter used in getCurrent() method
	 * @return boolean in case of an error returns false, otherwise returns true
	 */
    public static function correct(&$filename, $strict = false) {
        if (false === $ext = self::getCurrent($filename, $strict)) {
            return false;
        }
        if (false === $EXT = self::getReal($filename)) {
            return false;
        }
        if ($EXT != $ext) {
            if ($ext) {
                $pos = strrpos($filename, $ext);
                $newname = substr($filename,0,$pos).'.'.$EXT;
            }
            else 
                $newname = $filename.'.'.$EXT;
            if(!rename($filename, $newname)) {
                self::$_error = "Can't rename \"$filename\" to \"$newname\"";
                return false;
            }
            $filename = $newname;
        }
        return true;
    }
    

    /*
     * Gets the last occured error message
	 * @return string the error message
     */
    public static function getError() {
        $error = self::$_error;
        self::$_error = "";
        return $error;
    }
    
}