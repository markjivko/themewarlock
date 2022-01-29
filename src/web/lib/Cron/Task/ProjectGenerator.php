<?php
/**
 * Theme Warlock - Cron_Task_ProjectGenerator
 * 
 * @title      Project Generator
 * @desc       Generate all projects at the end of the shift
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Cron_Task_ProjectGenerator extends Cron_Task {

    /**
     * Set the schedule
     * 
     * @return array
     */
    public function schedule() {
        return array(
            self::HOUR   => 1,
            self::MINUTE => 30,
        );
    }
    
    /**
     * Execute the task
     */
    public function execute() {
        // Get the users from the database
        $usersModel = new Model_Users();
        
        // Count the tasks
        $enqueuedTasks = 0;
        
        // Go through all the users
        foreach ($usersModel->getAll() as $userModel) {
            // Get the projects instance
            $projects = Model_Projects::getInstance($userModel->id);
            
            // Go through all individual projects
            foreach ($projects->getAll($userModel->id) as /* @var $project Model_Project */ $project) {
                // Get the task
                $task = TaskManager::getInstance()->get($project->getUserId(), $project->getProjectId());

                // Enqueue the task
                try {
                    if (TaskManager_Task::STATUS_PENDING !== $task->getStatus()) {
                        $task->enqueue();
                        $enqueuedTasks++;
                    }
                } catch (Exception $exc) {
                    Log::check(Log::LEVEL_WARNING) && Log::warning($exc->getMessage(), $exc->getFile(), $exc->getLine());
                    $task->stop();
                }
            }
        }
        
        // Inform the user
        if (count($enqueuedTasks)) {
            TaskbarNotifier::sendMessage('Project Generator', 'Enqueued ' . $enqueuedTasks . ' project' . ($enqueuedTasks == 1 ? '' : 's'));
        } else {
            TaskbarNotifier::sendMessage('Project Generator', 'No projects to generate', TaskbarNotifier::TYPE_WARNING);
        }
    }
}

/* EOF */