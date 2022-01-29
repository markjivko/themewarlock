<?php
/**
 * Theme Warlock - WordPress_Snapshots
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class WordPress_Snapshots {

    // Snapshots folder name
    const FOLDER_NAME = 'snapshots';
    
    /**
     * Singleton instances
     * 
     * @var WordPress_Snapshots[]
     */
    protected static $_instances = array();
    
    /**
     * Project model
     * 
     * @var Model_Project
     */
    protected $_projectModel = null;
    
    /**
     * Snapshots holder
     * 
     * @param int $projectId (optional) Project ID; uses the current WP Session ID if omitted
     * @param int $userId    (optional) User ID; uses the current WP Session ID if omitted
     * @return WordPress_Snapshots
     */
    public static function getInstance($projectId = null, $userId = null) {
        // Get the default user ID
        if (null === $userId) {
            $userId = WordPress_Session::getInstance()->getUserId();
        }
        
        // Get the default project ID
        if (null === $projectId) {
            $projectId = WordPress_Session::getInstance()->getProjectId();
        }
        
        // Get the cache key
        $cacheKey = $projectId . '-' . $userId;
        
        // No instance yet
        if (!isset(self::$_instances[$cacheKey])) {
            self::$_instances[$cacheKey] = new self($projectId, $userId);
        }
        
        // Get all the snapshots
        $allSnapshots = self::$_instances[$cacheKey]->getAll();

        // Add the first empty snapshot
        if (!is_array($allSnapshots) || !count($allSnapshots)) {
            self::$_instances[$cacheKey]->add('Default', 'Default snapshot');
        }
        
        // Get the instance
        return self::$_instances[$cacheKey];
    }
    
    /**
     * Prepare the snapshot class
     * 
     * @param int $projectId
     * @param int $userId
     * @throws Exception
     */
    public function __construct($projectId, $userId) {
        if (!is_numeric($projectId)) {
            throw new Exception('Snapshots: Invalid project ID');
        }
        if (!is_numeric($userId)) {
            throw new Exception('Snapshots: Invalid user ID');
        }
        
        // Force the user ID
        WordPress_Session::getInstance()->setUserId($userId);
        
        /*@var $projects Model_Project*/
        $projects = Model_Projects::getInstance($userId);
        
        /*@var $project Model_Project*/
        $this->_projectModel = $projects->get($projectId);
    }
    
    /**
     * Snapshot ID
     * 
     * @param int $snapshotId Snapshot ID
     * @return WordPress_Snapshots_Snapshot
     * @throws Exception
     */
    public function getById($snapshotId) {
        // All done
        return new WordPress_Snapshots_Snapshot($snapshotId, $this->_projectModel);
    }
    
    /**
     * Available snapshots
     * 
     * @return WordPress_Snapshots_Snapshot[]
     */
    public function getAll() {
        // Get all the available snapshots
        return array_values(array_filter(
            array_map(
                function($item) {
                    // Prepare the result
                    $result = null;

                    // Get the snapshot
                    try {
                        $result = new WordPress_Snapshots_Snapshot(basename($item), $this->_projectModel);
                    } catch (Exception $exc) {
                        // Invalid snapshot folder
                        Log::check(Log::LEVEL_WARNING) && Log::warning($exc->getMessage(), $exc->getFile(), $exc->getLine());
                    }

                    // All done
                    return $result;
                }, 
                glob(
                    $this->_projectModel->getConfig()->getProjectPath() . '/' . self::FOLDER_NAME . '/*',
                    GLOB_ONLYDIR
                )
            )
        ));
    }

    /**
     * Add a new snapshot
     * 
     * @param string $title       Snapshot title
     * @param string $description Snapshot description
     * @param int    $fromId      (optional) Copy existing snapshot
     * @return WordPress_Snapshots_Snapshot
     */
    public function add($title, $description, $fromId = null) {
        // Prepare the new snapshot ID
        $toId = 1;
        
        // Get all the snapshots
        $allSnapshots = $this->getAll();
        $availableIds = array();
        if (is_array($allSnapshots)) {
            // Get the available IDs
            $availableIds = array_map(
                function($snapshot){
                    return $snapshot->getId();
                }, $allSnapshots
            );
            
            // Increment
            if (count($availableIds)) {
                do {
                    // Found a gap
                    if (!in_array($toId, $availableIds)) {
                        break;
                    }
                    $toId++;
                } while (true);
            }
        }
        
        // Generate from existing snapshot
        if (null !== $fromId) {
            // Valid ID provided
            if (in_array($fromId, $availableIds)) {
                Folder::copyContents(
                    $this->_projectModel->getConfig()->getProjectPath() . '/' . self::FOLDER_NAME . '/' . $fromId, 
                    $this->_projectModel->getConfig()->getProjectPath() . '/' . self::FOLDER_NAME . '/' . $toId
                );
            }
        }
        
        // Get the snapshot
        $newSnapshot = new WordPress_Snapshots_Snapshot($toId, $this->_projectModel, true);

        // Set the details
        $newSnapshot->setTitle($title)->setDescription($description, true);
        
        // All done
        return $newSnapshot;
    }
}

/* EOF */