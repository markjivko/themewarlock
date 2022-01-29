<?php
/**
 * Theme Warlock - Controller_Admin
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 * @name       Admin
 */

class Controller_Admin extends Controller {

    /**
     * Theme Warlock overview
     * 
     * @name Projects Manager
     * @allowed admin,manager
     */
    public function index($userId = null) {
        // Get the view
        $view = new View_Admin();

        // Add the CSS and JS
        $view->addCss('admin/index');
        $view->addJs('admin/index');

        /*@var $userModel Model_User*/
        $userModel = Session::getInstance()->get(Session::PARAM_WEB_USER_MODEL);
        
        // Store this model
        $view->setPlaceholder(View_Admin::PH_CURRENT_USER, $userModel);
        
        try {
            // Get the projects list
            $projects = Model_Projects::getInstance($userModel->id);
            
            do {
                // Some custom user ID provided
                if ($userModel->role == Session::ROLE_ADMIN && null != $userId && is_numeric($userId) && $userId > 0) {
                    // Get the custom user model
                    $customUserModel = new Model_User($userId);
                    
                    // Valid user
                    if ($customUserModel->exists()) {
                        // Get all the projects belonging to this user
                        $projectsList = $projects->getAll($userId);
                        
                        // A custom query is triggered
                        $view->setPlaceholder(View_Admin::PH_QUERY_USER, $customUserModel);
                        break;
                    }
                }

                // Get all the projects
                $projectsList = $projects->getAll();
            } while (false);
            
            // Prepare the pagination
            $pagination = new Pagination($projectsList);
            
            // Store the projects
            $view->setPlaceholder(View_Admin::PH_PAGINATION, $pagination);
        } catch (Exception $ex) {
            $view->setPlaceholder(View_Admin::PH_ERROR, $ex->getMessage());
        }
        
        // Output the part
        echo $view->getPart(View_Admin::PART_ADMIN_INDEX);
        
        // Display it
        $view->display();
    }
    
    /**
     * Theme Warlock overview
     * 
     * @hidden
     * @name Theme editor
     * @allowed admin,manager
     */
    public function project($projectId, $userId = null) {
        // Get the view
        $view = new View_Admin();
        
        // Add the CSS and JS
        $view->addCss('admin/project');
        $view->addCss('codemirror');
        $view->addCss('spectrum');
        $view->addJs('admin/project');
        $view->addJs('codemirror');
        $view->addJs('spectrum');
        $view->addJs('codemirror/lang/javascript');
        $view->addJs('codemirror/lang/css');
        $view->addJs('codemirror/addon/closebrackets');
        $view->addJs('codemirror/addon/closetag');
        
        /*@var $userModel Model_User*/
        $userModel = Session::getInstance()->get(Session::PARAM_WEB_USER_MODEL);
            
        // Prepare the user ID
        if (null == $userId) {
            $userId = $userModel->id;
        }
        
        try {
            // Get the projects list
            $projects = Model_Projects::getInstance($userModel->id);

            /*@var $project Model_Project*/
            $project = $projects->get($projectId, $userId);
            
            // Store the projects
            $view->setPlaceholder(View_Admin::PH_PROJECT, $project);
            
            // Not marked
            if (!$project->getMarker()->isMarked()) {
                throw new Exception(Controller_Ajax_Project::ERROR_EDIT_TOKEN_EXPIRED);
            }
            
            // A task is pending or has started for this project, abort
            if (TaskManager::getInstance()->isLockedForExport($userId)) {
                throw new Exception(Controller_Ajax_Project::ERROR_PROJECT_LOCKED_FOR_EXPORT);
            }
            
            // Get the project data
            $projectData = $project->getConfig()->toArray();
            
            // Get the title
            $projectDataTitle = $projectData['config'][Model_Project_Config::CATEGORY_CORE][Cli_Run_Integration::OPT_PROJECT_NAME][Model_Project_Config_Item::JSON_VALUE];
            
            // Get the framework
            $projectDataFramework = $projectData['config'][Model_Project_Config::CATEGORY_CORE][Cli_Run_Integration::OPT_PROJECT_FRAMEWORK][Model_Project_Config_Item::JSON_VALUE];

            // Set the title
            $view->setTitle($projectDataTitle . ' (' . $projectDataFramework . ')');
            
            // Log the event
            TaskbarNotifier::sendMessage(
                'Working...', 
                $userModel->name . ' started working ' . ($userId != $userModel->id ? 'as user #' . $userId : '') . ' on project #' . $projectId . ' - "' . $projectDataTitle . '"'
            );
        } catch (Exception $ex) {
            // Log the event
            TaskbarNotifier::sendMessage(
                'Exception', 
                $userModel->name . ' got "' . $ex->getMessage() . '" ' . ($userId != $userModel->id ? 'as user #' . $userId : '') . ' on project #' . $projectId,
                TaskbarNotifier::TYPE_WARNING
            );
            
            // Re-throw the exception
            throw $ex;
        }

        // Output the part
        echo $view->getPart(View_Admin::PART_ADMIN_PROJECT);
        
        // Display it
        $view->display();
    }
    
    /**
     * Translations Management
     * 
     * @name Translations
     * @allowed admin,manager
     */
    public function translations($language = WordPress_Pot_Translations_Cache::LANG_RO) {
        // Get the view
        $view = new View_Admin();

        // Add the CSS and JS
        $view->addCss('admin/translations');
        $view->addJs('admin/translations');  
        
        // If the language exists
        if(!in_array($language, WordPress_Pot_Translations_Cache::getLanguages())) {
            throw new Exception('Language not found');
        }
        
        // Store the validation errors
        $view->setPlaceholder(View_Admin::PH_TR_ERRORS, WordPress_Pot_Translations_Cache::validateAll($language));

        // Store the entries
        $view->setPlaceholder(View_Admin::PH_TR_ENTRIES, WordPress_Pot_Translations_Cache::getInstance($language)->getEntries());

        // Store the current language
        $view->setPlaceholder(View_Admin::PH_TR_LANGUAGE, $language);

        // Store the available languages
        $view->setPlaceholder(View_Admin::PH_TR_LANGUAGES, WordPress_Pot_Translations_Cache::getLanguagesVerbose(true));

        // Store the translations list size
        $view->setPlaceholder(View_Admin::PH_TR_NUMBER, WordPress_Pot_Translations_Cache::getInstance($language)->getPluralsCount());
            
        // Output the part
        echo $view->getPart(View_Admin::PART_ADMIN_TRANSLATIONS);
        
        // Display it
        $view->display();
    }
}

/*EOF*/