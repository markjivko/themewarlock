<?php
/**
 * Theme Warlock - Config_Items_Descriptor
 * 
 * @title      Configuration items descriptor
 * @desc       Descriptor for Config_Items
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Config_Items_Descriptor extends Config_Items {

    /**
     * Singleton instance of Config_Items_Descriptor
     * 
     * @var Config_Items_Descriptor
     */
    protected static $_instance = null;
    
    /**
     * Reflection class
     * 
     * @var ReflectionClass 
     */
    protected $_refection;
    
    /**
     * Singleton
     */
    protected function __construct() {
        $this->_refection = new ReflectionClass($this);
    }
    
    /**
     * A singleton instance of Config_Items_Descriptor
     * 
     * @return Config_Items_Descriptor
     */
    public static function getInstance() {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * Describe the Config_Items class
     * 
     * @return Config_Items_Descriptor_Entry[]
     */
    public function describe() {
        $values = array_values(
            array_filter(
                array_map(
                    function(/*@var $item ReflectionProperty*/ $item) {
                        // Onli accept public, non-static properties; exclude "myDomain" to avoid breaking the website
                        if ($item->isPublic() && !$item->isStatic() && 'myDomain' !== $item->getName()) {
                            return new Config_Items_Descriptor_Entry($item);
                        }
                        
                        // Ignore this entry
                        return null;
                    },
                    $this->_refection->getProperties()
                )
            )
        );
                    
        // Prepare the keys
        $keys = array_map(function($item){
            return $item->getName();
        }, $values);
        
        // All done
        return array_combine($keys, $values);
    }
}

/*EOF*/