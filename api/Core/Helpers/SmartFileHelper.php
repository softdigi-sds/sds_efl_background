<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Database
 *
 * @author kms
 */

namespace Core\Helpers;


class SmartFileHelper
{

    static public function createDirectoryRecursive($fullFilePath)
    {
        // Extract the directory path from the full file path
        $directoryPath = dirname($fullFilePath);

        // Split the directory path into an array of individual directories
        $directories = explode(DS, $directoryPath);

        // Initialize a variable to keep track of the current directory being processed
        $currentDirectory = '';

        // Iterate through the directories and create them recursively
        foreach ($directories as $directory) {
            $currentDirectory .= $directory . DS;
            if (!is_dir($currentDirectory)) {
                if (!mkdir($currentDirectory, 0755, true)) { // You can adjust the permissions (0755) as needed
                    return false; // Return false if directory creation fails
                }
            }
        }
        return true; // Return true if all directories were created successfully
    }
    /**
     * 
     */
    static public function  getDataPath()
    {
        if (isset($_ENV["DATA_PATH"])) {
            return $_ENV["DATA_PATH"];
        } else {
            \CustomErrorHandler::triggerInternalError("Invalid Data Path");
        }
    }
    /**
     * 
     */
    public static function moveSingleFile(string $index, string $store_path)
    {
        // make full path 
        $dest_path = self::getDataPath() . $store_path;
        // get stored extension
        $ext = SmartGeneral::getExt($store_path);
        if (strlen($ext) < 1) {
            // get the extenstion from upload file and attach to full path before creation of directories
            $name = isset($_FILES[$index]) ? $_FILES[$index]["name"] : "";
            $ext = SmartGeneral::getExt($name);
            $dest_path = $dest_path . "." . $ext;
        }
        // create a directory if not exits 
        self::createDirectoryRecursive($dest_path);
        // try to move the file 
        $temp_path = isset($_FILES[$index]) ? $_FILES[$index]["tmp_name"] : "";
        // check file availble in temporray directory
        if (file_exists($temp_path)) {
            move_uploaded_file($temp_path, $dest_path);
            return basename($dest_path);
        } else {
            \CustomErrorHandler::triggerInternalError("Error Uploading File");
        }
    }
    /**
     * 
     */
    public static function getLoggedInUserData(string $param)
    {
        // var_dump($GLOBALS["USER"]);
        return isset($GLOBALS["USER"]) && isset($GLOBALS["USER"]->USER) && isset($GLOBALS["USER"]->USER->{$param}) ? $GLOBALS["USER"]->USER->{$param} : null;
    }
    /**
     * 
     */
    public static function getLoggedInId()
    {
        $id = self::getLoggedInUserData("ID");
        // echo " logged in ID " . $id . "<br/>";
        return $id !== NULL ? $id : 0;
    }

    public static function getLoggedInUserId()
    {
        $id = self::getLoggedInUserData("euserid");
        return $id !== NULL ? $id : 0;
    }

    /**
     * 
     */
    public static function getRoles()
    {
        $roles = self::getLoggedInUserData("role");
        return $roles !== NULL ? $roles : [];
    }
    /**
     * 
     */
    public static function checkRole(array $roles)
    {
        $user_roles = self::getRoles();
        // var_dump($user_roles);
        $result = array_intersect($user_roles, $roles);
        return (!empty($result)) ? true : false;
    }

    public static function storeFile(string $content, string $store_path)
    {
        // make full path 
        $dest_path = self::getDataPath() . $store_path;
        $base64String = base64_decode($content);
        // create a directory if not exits 
        self::createDirectoryRecursive($dest_path);
        if (file_put_contents($dest_path, $base64String)) {
            return   $dest_path;
        } else {
            \CustomErrorHandler::triggerInternalError("Error Storing File");
        }
    }

    static public function extractZip($zipFilePath, $extractToPath = "")
    {
        $zip = new \ZipArchive;
        if ($extractToPath === "") {
            $extractToPath = dirname($zipFilePath);
        }
        //$zipFilePath = 'path/to/your/file.zip'; // Path to your ZIP file
        //$extractToPath = 'path/to/extract/destination/'; // Directory where to extract
        //echo " zip path " . $zipFilePath;
        if ($zip->open($zipFilePath) === TRUE) {
            // Extract the ZIP file to the specified directory
            $zip->extractTo($extractToPath);
            $zip->close();
            //echo 'Extraction successful!';
        } else {
            echo 'Failed to open the ZIP file.';
        }
    }

    static public function getFilesDirectory($directory, $extension)
    {
        // Get all files and directories in the specified directory
        $full_dir = self::getDataPath() . $directory;
        // echo "full directory " .  $full_dir;
        $files = scandir($full_dir);
        // var_dump($files);
        $xlsxFiles = [];

        foreach ($files as $file) {
            // Skip if it's not a file or if it doesn't have .xlsx extension
            if (is_file($full_dir  . DIRECTORY_SEPARATOR . $file) && pathinfo($file, PATHINFO_EXTENSION) === $extension) {
                // Store the file name and full path
                $xlsxFiles[] = [
                    'name' => $file,
                    'nameonly' => pathinfo($full_dir  . DIRECTORY_SEPARATOR . $file, PATHINFO_FILENAME),
                    'path' => realpath($full_dir  . DIRECTORY_SEPARATOR . $file)
                ];
            }
        }
        return  $xlsxFiles;
    }
    /**
     * Reads the content of a file and encodes it in Base64.
     *
     * @param string $filePath The path to the file.
     * @return string|false The Base64-encoded content or false if the file can't be read.
     */
    static public function encodeFileToBase64(string $file_path)
    {
        $filePath = self::getDataPath() . $file_path;
        // Check if the file exists and is readable
        if (!file_exists($filePath) || !is_readable($filePath)) {
            return false;
        }
        // Read the file content
        $fileContent = file_get_contents($filePath);
        if ($fileContent === false) {
            return false;
        }
        // Encode the content in Base64
        return base64_encode($fileContent);
    }

    // Array of file paths with custom names
// $filePaths = [
//     'folder1' => [
//         ['path' => '/path/to/original1.txt', 'name' => 'custom1.txt'],
//         ['path' => '/path/to/original2.jpg', 'name' => 'custom2.jpg'],
//     ],
//     'folder2/subfolder' => [
//         ['path' => '/path/to/original3.pdf', 'name' => 'custom3.pdf'],
//         ['path' => '/path/to/original4.png', 'name' => 'custom4.png'],
//     ],
//     'loosefile' => ['path' => '/path/to/original5.txt', 'name' => 'custom5.txt']
// ];
    static public function createZipWithSubfolders($filePaths, $zipFilePath)
    {
        $zip = new \ZipArchive();
        $base_path = self::getDataPath();
        // Open the ZIP file for creation
        if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            foreach ($filePaths as $folder => $files) {
                if (is_array($files)) {
                    // Create a folder in the ZIP
                    $zip->addEmptyDir($folder);
                    foreach ($files as $file) {
                        $full_path =   $base_path . $file['path'];
                        if (isset($file['path'], $file['name']) && file_exists( $full_path )) {
                            // Add file with a custom name inside the folder
                            $zip->addFile  ($full_path , $folder . '/' . $file['name']);
                        } else {
                           // echo "File not found or invalid entry: " . ($file['path'] ?? 'Unknown') . "\n";
                        }
                    }
                } else {
                    $full_path =   $base_path . $files['path'];
                    if (isset($files['path'], $files['name']) && file_exists( $full_path)) {
                        // Add file with a custom name to the root of the ZIP
                        $zip->addFile(   $full_path , $files['name']);
                    } else {
                        //echo "File not found or invalid entry: " . ($files['path'] ?? 'Unknown') . "\n";
                    }
                }
            }
            $zip->close();
            //echo "ZIP file created successfully at $zipFilePath\n";
        } else {
            //echo "Failed to create ZIP file.\n";
        }
    }
}
