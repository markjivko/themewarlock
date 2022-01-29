<?php

/**
 * Theme Warlock - Folder
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */
class Folder {

    /**
     * Driver letter cache 
     * 
     * @var array
     */
    protected static $_driverLetterCache = array();

    /**
     * Remove empty subfolders
     * 
     * @param string $path Empty folder to check
     * @return boolean
     */
    public static function removeEmptySubFolders($path) {
        $empty = true;
        foreach (glob($path . DIRECTORY_SEPARATOR . "*") as $file) {
            $empty &= is_dir($file) && self::removeEmptySubFolders($file);
        }
        return $empty && rmdir($path);
    }
    
    /**
     * Copy contents from source to destination
     * 
     * @param string $source      Source
     * @param string $destination Destination
     */
    public static function copyContents($source, $destination) {
        // Sanitize the source and destination
        $source = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $source);
        $destination = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $destination);

        // Invalid source
        if (!is_dir($source)) {
            Log::check(Log::LEVEL_WARNING) && Log::warning('Source folder "' . $source . '" not found');
            return;
        }
        
        // Perform only if the destination directory does not exist
        if (!is_dir($destination)) {
            Folder::create($destination, 0777, true);
        }
        
        foreach ($iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST) as $item) {
            if ($item->isDir()) {
                if (!is_dir($destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName())) {
                    Folder::create($destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName(), 0777, true);
                }
            } else {
                // Get the basename once
                $itemBasename = basename($item);
                
                // Never copy Thumbs.db or unintentionally copied files
                if ('Thumbs.db' !== $itemBasename && false === strpos($itemBasename, ' - Copy')) {
                    copy($item, $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
                } else {
                    @unlink($item);
                }
            }
        }
        Log::check(Log::LEVEL_DEBUG) && Log::debug('Recursively copied from "' . $source . '" to "' . $destination . '"');
    }

    /**
     * Copy contents from source to destination
     * 
     * @param string $source        Source
     * @param string $destination   Destination
     * @param array  $excludedFiles Exclude some files (destination)
     */
    public static function copyContentsExcluding($source, $destination, $excludedFiles = array()) {
        // Sanitize the source and destination
        $source = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $source);
        $destination = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $destination);

        // Clean the excluded files (and folders) list
        foreach ($excludedFiles as $key => $value) {
            $excludedFiles[$key] = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $value);
        }
        
        // Perform only if the destination directory does not exist
        if (!is_dir($destination)) {
            Folder::create($destination, 0777, true);
        }

        foreach ($iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST) as $item) {
            if ($item->isDir()) {
                if (!is_dir($destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName())) {
                    Folder::create($destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName(), 0777, true);
                }
            } else {
                // Item excluded
                if (in_array($destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName(), $excludedFiles)) {
                    continue;
                }
            
                // Get the basename once
                $itemBasename = basename($item);
                
                // Never copy Thumbs.db or unintentionally copied files
                if ('Thumbs.db' !== $itemBasename && false === strpos($itemBasename, ' - Copy')) {
                    @copy($item, $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
                } else {
                    @unlink($item);
                }
            }
        }
        Log::check(Log::LEVEL_DEBUG) && Log::debug('Recursively copied from "' . $source . '" to "' . $destination . '"');
    }
    
    
    /**
     * Copy a file, creating the destination directory recursively, if necessary
     * 
     * @param string $from From
     * @param string $to   To
     * @throws Exception
     * @return null
     */
    public static function copy($from, $to) {
        if (!file_exists($from)) {
            throw new Exception('File "' . $from . '" not found');
        }
        
        // Get the final directory
        $dir = dirname($to);
        
        // Create it if necessary
        if (!is_dir($dir)) {
            Folder::create($dir, 0777, true);
        }
        
        // Log the information
        Log::check(Log::LEVEL_DEBUG) && Log::debug('Copied from ' . $from . ' to ' . $to);

        // Copy the file
        copy($from, $to);
    }
    
    /**
     * Get available system drive letters
     * 
     * @return array Drive letters
     */
    public static function getDriveLetters() {
        // Prepare the result
        $result = array();

        $fso = new COM('Scripting.FileSystemObject');
        foreach ($fso->Drives as $drive) {
            $result[] = strtoupper($drive->DriveLetter);
        }

        // Return the result
        return $result;
    }

    /**
     * Locate a file or folder
     * 
     * @param string|array $items         Path RegEx(es)
     * @param string        $driveLetter Drive letter
     * @return array|boolean Array of items on succes, false o failure
     */
    public static function locate($items, $driveLetter = null) {
        // The items are always an array
        $items = (array) $items;

        // Get the drive letters
        $driveLettersOnMachine = self::getDriveLetters();

        // Prepare the targeted drive letters array
        $driveLettersTargeted = array();

        // Use a custom drive
        if (null !== $driveLetter) {
            if (!is_array($driveLetter)) {
                $driveLetter = strtoupper(substr(trim($driveLetter), 0, 1));
                if (!in_array($driveLetter, $driveLettersOnMachine)) {
                    throw new Exception('Drive letter ' . $driveLetter . ' not found on this computer.');
                }

                // Searc on the provided driver only
                $driveLettersTargeted = array($driveLetter);
            } else {
                // Prepare the temporary drive letters holder
                $driveLettersTargeted = array();

                // Validate that user-provided drive letters are allowed
                $driveLetter = array_map('strtoupper', $driveLetter);
                foreach ($driveLetter as $dl) {
                    if (in_array($dl, $driveLettersOnMachine)) {
                        $driveLettersTargeted[] = $dl;
                    }
                }

                // Must have some results
                if (!count($driveLettersTargeted)) {
                    throw new Exception('Drive letter' . (count($driveLetter) != 1 ? 's' : '') . ' ' . implode(', ', $driveLetter) . ' not found on this computer.');
                }
            }
        } else {
            $driveLettersTargeted = $driveLettersOnMachine;
        }

        // Unique values
        $driveLettersTargeted = array_unique($driveLettersTargeted);

        // Log the drives to search through
        Log::check(Log::LEVEL_DEBUG) && Log::debug('Locating "' . implode(', ', $items) . '" in drives: ' . implode(', ', $driveLettersTargeted));

        // Prepare the result
        $result = array_combine($items, array_fill(0, count($items), array()));

        // Go through each drive
        foreach ($driveLettersTargeted as $driveLetter) {
            // Implement local caching for faster later access during the execution of one script
            if (!isset(self::$_driverLetterCache[$driveLetter])) {
                // Get the Output
                @exec('dir /s /b ' . $driveLetter . ':\\', $outputFiles, $error);

                // Save the Output
                self::$_driverLetterCache[$driveLetter] = $outputFiles;
            } else {
                $error = 0;
                $outputFiles = self::$_driverLetterCache[$driveLetter];
            }

            if (!$error) {
                foreach ($outputFiles as $file) {
                    foreach ($items as $item) {
                        $nameRegex = '%^' . str_replace('/', '\\\\', $item) . '/?$%';
                        if (preg_match($nameRegex, $file)) {
                            // Append the found file
                            $result[$item][] = $file;
                        }
                    }
                }
            } else {
                Log::check(Log::LEVEL_WARNING) && Log::warning('Could not fetch data for drive ' . $driveLetter);
            }
        }

        // All done
        return $result;
    }

    /**
     * Download a large file from URL to a local path
     * 
     * @param string $url  File URL
     * @param string $path Local path to store the file
     */
    public static function download($url, $path) {
        // Create the file pointer
        $filePointer = fopen($path, 'w');

        // Initialize the CURL handler
        $curlHandler = curl_init($url);
        curl_setopt($curlHandler, CURLOPT_FILE, $filePointer);

        // Execute
        curl_exec($curlHandler);

        // Close the handlers
        curl_close($curlHandler);
        fclose($filePointer);
    }

    /**
     * Copy contents following the provided structure
     * 
     * @param string $source    Source folder
     * @param string $dest      Destination folder
     * @param array  $structure Structure
     */
    public static function copyContentsWithStructure($source, $dest, $structure) {
        // Sanitize the source and destination
        $source = rtrim(str_replace(array('/', '\\'), '/', $source), '/');
        $dest = rtrim(str_replace(array('/', '\\'), '/', $dest), '/');

        // Log this action
        Log::check(Log::LEVEL_DEBUG) && Log::debug('Copying contents with structure from "' . $source . '" to "' . $dest . '"...');
        Log::check(Log::LEVEL_DEBUG) && Log::debug($structure);
        
        // Perform only if the destination directory does not exist
        if (!is_dir($dest)) {
            Folder::create($dest, 0777, true);
        }

        foreach ($iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST) as $item) {
            // Prepare the new location
            $newLocation = $dest . '/' . str_replace(array('/', '\\'), '/', $iterator->getSubPathName());

            // Part of a "com" directory?
            if (preg_match('%/com/%i', $newLocation)) {
                // Get the part after the "com" folder
                $afterCom = preg_replace('%^.*?/com/%i', '', $newLocation);
                $afterCom = rtrim(str_replace(array('\\', '/'), '/', $afterCom), '/');
                $afterComNew = implode('/', array_slice($structure, 1, substr_count($afterCom, '/') + 1));

                // Get the new directory structure
                $newLocation = preg_replace('%^(.*?/com/).*?$%i', '$1' . $afterComNew, $newLocation);
                if ($item->isFile()) {
                    $newLocation .= '/' . basename($iterator->getSubPathName());
                }
            }

            // Directory
            if ($item->isDir()) {
                if (!is_dir($newLocation)) {
                    Folder::create($newLocation, 0777, true);
                }
            } else {
                // File (overwrite), Never copy Thumbs.db
                if ('Thumbs.db' !== basename($item)) {
                    copy($item, $newLocation);
                }
            }
        }
        Log::check(Log::LEVEL_DEBUG) && Log::debug('Recursively copied from "' . $source . '" to "' . $dest . '"');
    }

    /**
     * Clean a directory
     * 
     * @param string  $dirPath      Directory
     * @param boolean $removeDirToo (optional) Remove the directory itself; default <b>false</b>
     * @return null
     */
    public static function clean($dirPath, $removeDirToo = false) {
        // Clean-up the directory path
        $dirPath = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $dirPath);
                
        // Log this action
        Log::check(Log::LEVEL_DEBUG) && Log::debug('Cleaning "' . $dirPath . '"...');

        // Sanitize the source and destination
        if (!is_dir($dirPath)) {
            return;
        }
        
        // Perform only if the destination directory does not exist
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dirPath, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $path) {
            if ($path->isFile()) {
                @unlink($path->getPathname());
            } else {
                // Force PHP to let go of the directory
                closedir(opendir($path->getPathname()));

                // Remove the directory
                @rmdir($path->getPathname());
            }
        }
        if ($removeDirToo) {
            // Force PHP to let go of the directory
            closedir(opendir($dirPath));

            // Remove the directory
            rmdir($dirPath);
        }
    }
    
    /**
     * Clean a directory, excluding some files
     * 
     * @param string  $dirPath       Directory
     * @param array   $excludedFiles Excluded files (destination)
     * @return null
     */
    public static function cleanExcluding($dirPath, $excludedFiles = array()) {
        // Clean-up the directory path
        $dirPath = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $dirPath);
                
        // Log this action
        Log::check(Log::LEVEL_DEBUG) && Log::debug('Cleaning "' . $dirPath . '" with files excluded...');
        Log::check(Log::LEVEL_DEBUG) && Log::debug($excludedFiles);

        // Clean the excluded files (and folders) list
        foreach ($excludedFiles as $key => $value) {
            $excludedFiles[$key] = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $value);
        }
        
        // Sanitize the source and destination
        $dirPath = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $dirPath);
        if (!is_dir($dirPath)) {
            Log::check(Log::LEVEL_INFO) && Log::info('Directory "' . $dirPath . '" not found.');
            return;
        }
        
        // Perform only if the destination directory does not exist
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dirPath, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $path) {
            if ($path->isFile()) {
                // Item excluded
                if (in_array($path->getPathname(), $excludedFiles)) {
                    continue;
                }
                
                @unlink($path->getPathname());
            } else {
                foreach($excludedFiles as $excludedFile) {
                    if (preg_match('%^' . preg_quote(str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path->getPathname())) . '%', $excludedFile)) {
                        continue 2;
                    }
                }

                // Force PHP to let go of the directory
                closedir(opendir($path->getPathname()));

                // Remove the directory
                @rmdir($path->getPathname());
            }
        }
    }

    /**
     * Find and replace in all files in a directory
     * 
     * @param string   $find        Find RegEx
     * @param string   $replace     Replacement
     * @param string   $directory   Directory
     * @param string   $fileRegex   File RegEx
     * @param callable $callback    If provided, use this callback function instead of the $replace parameter
     * @return array Replacements
     * @throws Exception
     */
    public static function pregReplace($find, $replace, $directory, $fileRegex = '', $callback = null) {
        // Sanitize the source
        $directory = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $directory);

        // Count the number of replacements
        $replacements = 0;

        // Perform only if the destination directory does not exist
        if (is_dir($directory)) {
            foreach ($iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST) as $item) {
                if (!$item->isDir()) {
                    if ('' !== $fileRegex && !preg_match($fileRegex, $item->getFilename())) {
                        continue;
                    }

                    // Get the file contents
                    if (null !== $callback) {
                        $content = @preg_replace_callback($find, $callback, file_get_contents((string) $item), -1, $count);
                    } else {
                        $content = @preg_replace($find, $replace, file_get_contents((string) $item), -1, $count);
                    }
                    
                    // Increment the replacements
                    $replacements += $count;

                    // Valid replacement made
                    if (null !== $content) {
                        // Overwrite the file contents
                        if ($count) {
                            Log::check(Log::LEVEL_DEBUG) && Log::debug('Performed ' . $count . ' Regular Expression replacement' . (1 == $count ? '' : 's') . ' on file "' . (string) $item . '"');
                            file_put_contents((string) $item, $content);
                        }
                    } else {
                        throw new Exception('Invalid regular expression: ' . $find);
                    }
                }
            }
            Log::check(Log::LEVEL_DEBUG) && Log::debug('Performed ' . $replacements . ' Regular Expression replacement' . (1 == $replacements ? '' : 's') . ' on directory "' . $directory . '"');
            return $replacements;
        }

        throw new Exception('Directory "' . $directory . '" does not exist');
    }
    
    /**
     * Find in all files in a directory
     * 
     * @param string   $find      Find RegEx
     * @param string   $directory Directory
     * @param string   $fileRegex File RegEx
     * @return array Array of file -> matches found
     * @throws Exception
     */
    public static function pregMatchAll($find, $directory, $fileRegex = '') {
        // Sanitize the source
        $directory = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $directory);

        // Log this action
        Log::check(Log::LEVEL_DEBUG) && Log::debug('Searcing for ' . $fileRegex . ' in directory ' . $directory . ', file regex ' . ($fileRegex ? $fileRegex : 'not defined'));
        
        // Prepare the results
        $results = array();
        
        // Perform only if the destination directory does not exist
        if (is_dir($directory)) {
            foreach ($iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST) as $item) {
                if (!$item->isDir()) {
                    if ('' !== $fileRegex && !preg_match($fileRegex, $item->getFilename())) {
                        continue;
                    }

                    // Valid match made
                    if (false !== @preg_match_all($find, file_get_contents((string) $item), $matches, PREG_SET_ORDER)) {
                        // Match found
                        if (count($matches)) {
                            Log::check(Log::LEVEL_DEBUG) && Log::debug(count($matches) . ' match' . (1 == count($matches) ? '' : 'es') . ' found');
                            
                            // Append to the result
                            $results[$item->getFilename()] = $matches;
                        }
                    } else {
                        throw new Exception('Invalid regular expression: ' . $find);
                    }
                }
            }
            
            return $results;
        }

        throw new Exception('Directory "' . $directory . '" does not exist');
    }

    /**
     * Find folders with given name
     * 
     * @param string $nameRegex Folder name RegEx
     * @param string $directory Directory to search in
     * @return array
     */
    public static function findFolders($nameRegex, $directory) {
        // Sanitize the source and destination
        $directory = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $directory);

        // Get the results array
        $results = array();

        // Perform only if the destination directory does not exist
        if (is_dir($directory)) {
            foreach ($iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST) as $item) {
                if ($item->isDir()) {
                    if (@preg_match($nameRegex, $item->getFilename())) {
                        $results[] = $item->getPath() . DIRECTORY_SEPARATOR . $item->getFilename();
                    }
                }
            }
        }

        // Get the results
        return $results;
    }

    /**
     * Find files with given names
     * 
     * @param string $nameRegex Filename RegEx
     * @param string $directory Directory
     * @return array
     */
    public static function findFiles($nameRegex, $directory) {
        // Sanitize the source and destination
        $directory = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $directory);

        // Get the results array
        $results = array();

        // Perform only if the destination directory does not exist
        if (is_dir($directory)) {
            foreach ($iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST) as $item) {
                if (!$item->isDir()) {
                    if (@preg_match($nameRegex, $item->getFilename())) {
                        $results[] = $item->getPath() . DIRECTORY_SEPARATOR . $item->getFilename();
                    }
                }
            }
        }

        // Get the results
        return $results;
    }

    /**
     * Delete files with given names
     * 
     * @param string $nameRegex Filename RegEx
     * @param string $directory Directory
     * @return null
     */
    public static function deleteFiles($nameRegex, $directory) {
        // Sanitize the source and destination
        $directory = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $directory);

        // Perform only if the destination directory does not exist
        if (is_dir($directory)) {
            foreach ($iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST) as $item) {
                if (!$item->isDir()) {
                    if (@preg_match($nameRegex, $item->getFilename())) {
                        @unlink($item->getPath() . DIRECTORY_SEPARATOR . $item->getFilename());
                    }
                }
            }
        }
    }
    
    /**
     * Get the size of a directory
     * 
     * @param string $directory Directory path
     * @return int
     */
    public static function size($directory) {
        $size = 0;
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file) {
            if ($file->getFileName() != '..') {
                $size+=$file->getSize();
            }
        }
        return $size;
    }

    /**
     * Return dirname(ROOT) without the final slash
     * 
     * @return string
     */
    public static function dirnameRoot() {
        return rtrim(dirname(ROOT), '/\\');
    }
    
    /**
     * Create a directory recursively with no mask
     * 
     * @param string  $path      The directory path.
     * @param int     $mode      [optional] <p>
     * The mode is 0777 by default, which means the widest possible
     * access. For more information on modes, read the details
     * on the <b>chmod</b> page.
     * </p>
     * @param boolean $recursive [optional] <p>
     * Allows the creation of nested directories specified in the
     * <i>pathname</i>.
     * </p>
     */
    public static function create($path, $mode = 0777, $recursive = false) {
        // Get the mask
        $oldMask = umask(0);
        
        // Create the directory
        mkdir($path, $mode, $recursive);
        
        // Set the mask back
        umask($oldMask);
    }
}

/*EOF*/