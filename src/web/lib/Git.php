<?php
/**
 * Theme Warlock - Git
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Git {
    
    /**
     * Command cache
     *
     * @var array
     */
    protected static $_cache = array();
    
    /**
     * Get the result of a GIT command
     * 
     * @param string $command GIT Command
     * @return string|null Command result or null on failure
     */
    protected static function _get($command) {
        // Prepare the output buffer
        ob_start();
        
        // Execute the command
        passthru('git -C "' . ROOT . '" ' . $command . ' 2>&1', $returnVar);
        
        // Get the output
        $output = trim(ob_get_clean());

        // Store the result
        return (0 == $returnVar) ? $output : null;
    }
    
    /**
     * Get the revision number
     * 
     * @return string
     */
    public static function getRevisionLocal() {
        return self::_get('rev-parse HEAD');
    }
    
    /**
     * Get the Remote URL
     * 
     * @return string|null git@... format or null on failure
     */
    public static function getUrl() {
        // Convert the https to git (user@domain.com)
        if (preg_match('%^https?\:\/\/(.*?)@(.*?)\/(.*?)\/(.*?)\.git$%', $remoteOriginUrl = self::_get('config --get remote.origin.url'), $matches)) {
            // Get the transformed link
            return 'git@' . $matches[2] . ':' . $matches[1] . '/' . $matches[4] . '.git';
        }
        
        // Convert https to git (domain.com)
        if (preg_match('%^https?\:\/\/(.*?)\/(.*?)\/(.*?)\.git$%', $remoteOriginUrl = self::_get('config --get remote.origin.url'), $matches)) {
            // Get the transformed link
            return 'git@' . $matches[1] . ':' . $matches[2] . '/' . $matches[3] . '.git';
        }

        // Already a git format
        if (preg_match('%^git\@%', $remoteOriginUrl)) {
            return $remoteOriginUrl;
        }
        
        // Invalid URL
        return null;
    }
    
    /**
     * Get the Remote revision
     * 
     * @return string
     */
    public static function getRevisionRemote() {
        // Get the result
        $result = preg_replace('%^([a-z0-9]+)\s*HEAD.*$%ms', '$1', self::_get('ls-remote ' . self::getUrl()));

        // All done
        return $result;
    }
    
    /**
     * Git::pull
     * 
     * @return null
     */
    public static function pull() {
        // Start the buffer
        ob_start();
        
        // Fetch all
        passthru('git fetch ' . self::getUrl() . ' +refs/heads/master:refs/remotes/origin/master 2>&1');

        // Merge locally
        passthru('git merge origin/master 2>&1', $returnVar);
        
        // Get the contents
        $contents = ob_get_clean();

        // Get the contents
        if (1 == $returnVar) {
            if (preg_match('%error\:.*?by merge\:\s*(.*?)\s*Please,%ms', $contents, $matches)) {
                foreach (preg_split('%[\r\n]+%', $matches[1]) as $conflictingFile) {
                    // Inform the user
                    Console::p('Resetting "' . $conflictingFile . '"...', false);
                    
                    // Get the conflicting file relative path
                    $conflictingFile = trim($conflictingFile);

                    // Reset to head
                    passthru('git reset HEAD ' . str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, ROOT . '/' . $conflictingFile));
                    
                    // Check out the new version
                    passthru('git checkout -- ' . str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, ROOT . '/' . $conflictingFile));
                    
                    // Clean-up
                    passthru('git clean -f -d ' . str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, ROOT . '/' . $conflictingFile));
                }
            } else {
                // Inform the user
                Console::p('Performing a hard reset...');
                
                // Hard reset
                passthru('git reset --hard');
            }
            
            // Merge locally again
            passthru('git merge origin/master 2>&1', $returnVar);
        }
    }
    
    /**
     * Git::status
     * 
     * @return null
     */
    public static function status() {
        passthru('git status');
    }
    
    /**
     * Git::gc
     * 
     * @return null
     */
    public static function gc() {
        passthru('git gc');
    }
    
    /**
     * Git::reset
     * 
     * @return null
     */
    public static function reset() {
        passthru('git reset --hard');
    }
    
    /**
     * Command-line tool for Git Pull
     * 
     * @param boolean $async Run the script asynchronously
     * @return null
     */
    public static function run($async = false, $exit = true) {
        // Run on another thread
        if ($async) {
            // Start the process asynchronously
            Process::startTool(Cli_Run_Integration::TOOL_GIT_PULL, null, null, null, true, true);
            
            // Stop here
            if (!$exit) return;
            exit();
        }
        
        // Inform the user
        TaskbarNotifier::sendMessage(
            'Checking for updates...', 
            'Looking for the latest commits...'
        );
        
        // Get the revision IDs
        $from = self::getRevisionLocal();
        $to = self::getRevisionRemote();
        
        // Could not get the remote revision
        if (empty($to)) {
            Console::p($message = 'Git could not contact remote server', false);
            
            // Update needed
            TaskbarNotifier::sendMessage(
                'Git error', 
                $message,
                TaskbarNotifier::TYPE_ERROR
            );
            
            // Stop here
            if (!$exit) return;
            exit();
        }
        
        // Inform the user
        Console::p('Current version: ' . $from);
        Console::p('Latest version: ' . $to);

        // An update is needed
        if ($from !== $to) {
            // Inform the user
            Console::p($message = 'Update in progress...');
            
            // Update needed
            TaskbarNotifier::sendMessage(
                $message, 
                'Updating from (' . self::getDetails($from, 50) . ') to latest revision'
            );
            
            // Perform the pull
            self::pull();
            
            // Run the help tool
            Process::startTool(Cli_Run_Integration::TOOL_HELP, null, null, null, true, true);
            
            // Wait a while
            sleep(3);
        }
        
        // Update needed
        TaskbarNotifier::sendMessage(
            'Git up-to-date', 
            self::getDetails()
        );
        
        // All done
        if (!$exit) return;
        exit();
    }
    
    /**
     * Get the details of the last revision
     * 
     * @return string
     */
    public static function getDetails($revisionId = null, $subjectMaxLength = null, $showAuthor = true) {
        // Get the revision ID
        $revisionId = (null == $revisionId ? self::getRevisionLocal() : $revisionId);
        
        // Prepare the subject
        $subject = null;

        // Get the max length
        $maxLength = (null !== $subjectMaxLength) ? intval($subjectMaxLength) : 180;
        
        // Validate the length
        $maxLength = ($maxLength >= 0 && $maxLength <= 180) ? $maxLength : 180;
        
        // Show the subject
        if ($maxLength != 0) {
            // Get the subject
            $subject = self::_get('show --quiet --pretty=format:"%s" ' . $revisionId);
        
            // String too long
            if (strlen($subject) > $maxLength) {
                $subject = substr($subject, 0, $maxLength - 3) . '...';
            }
        }
        
        // Get the author info
        $author = self::_get('show --quiet --pretty=format:"' . ($showAuthor ? '%an, ' : '') .'%ar" ' . $revisionId);
        
        // All done
        return (null !== $subject ? ($subject . ' @') : '') . $author;
    }
}

/*EOF*/