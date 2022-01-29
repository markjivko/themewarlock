<?php

/**
 * Theme Warlock - Zip
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */
class Zip {
   
    /**
     * Create a ZIP archive from the provided list of files
     * 
     * @param array  $files   List of files as [localName => fullPath, ...]
     * @param string $zipPath ZIP archive path
     * @return boolean True on success, false on failure
     */
    public static function create(Array $files, $zipPath, $comment = '') {
        // Prepare the valid files holder
        $validFiles = array();
        
        // Associative list
        $associative = array_keys($files) !== range(0, count($files) - 1);
        
        // If files were passed in
        if (is_array($files) && count($files)) {
            // Cycle through each file
            foreach ($files as $localName => $fullPath) {
                // Make sure the file exists
                if (is_string($fullPath) && file_exists($fullPath)) {
                    $validFiles[$localName] = $fullPath;
                }
            }
        }

        // If we have good files...
        if (count($validFiles)) {
            // Create the archive
            $zip = new ZipArchive();
            
            // Could not open
            if (true !== $result = $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
                return false;
            }
            
            // Set the archive comment
            if (!empty($comment) && is_string($comment)) {
                $zip->setArchiveComment($comment);
            }
            
            // Add the files
            foreach ($validFiles as $localName => $fullPath) {
                $zip->addFile($fullPath, $associative ? $localName : null);
            }

            // Close the archive
            $zip->close();

            // Check to make sure the file exists
            return file_exists($zipPath);
        }
        
        // Something went wrong
        return false;
    }
    
    /**
     * Archive a folder faster using system-level commands
     * 
     * @param string  $folderPath    Folder path
     * @param string  $extensionName (optional) Extension name; default <b>"zip"</b>
     * @param boolean $remove        (optional) Remove the original folder; default <b>true</b>
     * @param boolean $compression   (optional) Use faster compression or just store?; default <b>null</b>, converts to <b>true</b> if not in staging mode
     */
    public static function packNative($folderPath, $extensionName = 'zip', $remove = true, $compression = null) {
        // Set the default compression
        if (!is_bool($compression)) {
            // Automatically enable compression in live mode
            $compression = !Tasks::isStaging();
        }
        
        // Prepare the command
        if ($remove) {
            $command = sprintf(
                // Go to, archive, remove folder
                'cd %s && zip %s -r %s %s && rm -rf %s',
                // Parent path
                escapeshellarg(dirname($folderPath)),
                // Compression level (best or store)
                $compression ? '-9' : '-0',
                // Archive name
                escapeshellarg(basename($folderPath) . '.' . $extensionName),
                // Folder path
                escapeshellarg(basename($folderPath)),
                // Folder name
                escapeshellarg(basename($folderPath))
            );
        } else {
            $command = sprintf(
                // Go to, archive, remove folder
                'cd %s && zip %s -r %s %s',
                // Parent path
                escapeshellarg(dirname($folderPath)),
                // Compression level (best or store)
                $compression ? '-9' : '-0',
                // Archive name
                escapeshellarg(basename($folderPath) . '.' . $extensionName),
                // Folder path
                escapeshellarg(basename($folderPath))
            );
        }

        // Run the command
        shell_exec($command);
    }

    /**
     * Create an archive from a folder
     *  
     * @param string  $folderPath    Folder Path
     * @param string  $zipPath       Zip Path
     * @param string  $comment       Zip comment
     * @param boolean $includeFolder The final ZIP should extract to a folder
     * @return boolean
     */
    public static function pack($folderPath, $zipPath, $comment = '', $includeFolder = true) {
        // Create the archive
        $zip = new ZipArchive();

        // Trim
        $folderPath = rtrim($folderPath, '\\/');
        
        // Could not open
        if (true !== $zip->open($zipPath, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE)) {
            return false;
        }
        
        // Set the archive comment
        if (!empty($comment) && is_string($comment)) {
            $zip->setArchiveComment($comment);
        }

        // Go throught the files
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folderPath, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST) as $file) {
            // Skip directories (they would be added automatically)
            if (!$file->isDir()) {
                // Get real and relative path for current file
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($includeFolder ? dirname($folderPath) : $folderPath) + 1);
                
                // Add current file to archive
                $zip->addFile($filePath, $relativePath);
            }
        }
        
        // Zip archive will be created only after closing object
        $zip->close();
        
        // Check to make sure the file exists
        return file_exists($zipPath);
    }
    
    /**
     * Unzip an archive and return its comment
     * 
     * @param string $zipPath   Archive path
     * @param string $finalPath Unzip location
     * @return boolean|string False on failure or archive comment
     */
    public static function unpack($zipPath, $finalPath = null) {
        // File not found
        if (!file_exists($zipPath)) {
            return false;
        }
        
        // Prepare the destination
        if (null == $finalPath) {
            $finalPath = preg_replace('%\.\w+$%', '', $zipPath);
        }

        // Create the archive
        $zip = new ZipArchive();

        // Could not open
        if (true !== $result = $zip->open($zipPath)) {
            return false;
        }
        
        // Extract the file
        if ($zip->extractTo($finalPath)) {
            if (false !== $comment = $zip->getArchiveComment(ZipArchive::FL_UNCHANGED)) {
                return $comment;
            }
            
            // No comment defined for this archive
            return '';
        }
        
        // Something went wrong
        return false;
    }
    
}

/* EOF */
