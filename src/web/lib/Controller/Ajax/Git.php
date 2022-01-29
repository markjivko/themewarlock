<?php
/**
 * Theme Warlock - Controller_Ajax_Git
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Controller_Ajax_Git extends Controller_Ajax {

    /**
     * Set the flag to perform a GIT pull
     * 
     * @allowed admin
     */
    public function pull() {
        // Pull
        Git::run(false, false);
        
        // Working...
        echo 'Git pull successful!';
    }

}

/* EOF */
