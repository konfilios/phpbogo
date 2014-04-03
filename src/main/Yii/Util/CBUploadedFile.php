<?php
/**
 * Extensions to uploaded file.
 *
 * @since 1.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class CBUploadedFile extends CUploadedFile
{
	/**
	 * Returns an array of instances for the specified array name.
	 *
	 * If multiple files were uploaded and saved as 'Files[0]', 'Files[1]',
	 * 'Files[n]'..., you can have them all by passing 'Files' as array name.
	 * @param string $name the name of the array of files
	 * @return array the array of CUploadedFile objects. Empty array is returned
	 * if no adequate upload was found. Please note that this array will contain
	 * all files from all subarrays regardless how deeply nested they are.
	 */
	public static function getInstancesHashByName($name)
	{
		$files = array();
		foreach ($_FILES[$name]['name'] as $key=>$file) {
			$files[$key] = self::getInstanceByName($name.'['.$key.']');
		}
		return $files;
	}
}