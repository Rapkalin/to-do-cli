<?php

namespace App\Models;

use App\Services\Database;

class Task
{
    /**
     * @var string
     */
    protected string $name;

    /**
     * Create and insert a new task row
     * in table tasks
     *
     * @param array $task
     * @return void
     */
    public function createTask(array $task): void
    {
        $name = $task['name'];
        $description = $task['description'];
        $isDone = $task['isDone'];

        try {
            $database = (new Database())->getDatabase();
            $query = $database->prepare("INSERT INTO tasks(name,description,is_done) VALUES ('$name','$description','$isDone')");
            $query->execute();

            echo "\033[32m New task created! \033[0m \n";
        } catch (\Exception $e) {
            echo "\033[32m Creating new task failed: " . $e->getMessage() . "\033[0m \n";
        }

    }

    /**
     *  Update a task row
     *  in table tasks
     *
     * @param int $taskId
     * @param string $columnName
     * @param $value
     * @return void
     */
    private function updateTask(int $taskId, string $columnName, $value): void
    {
        try {
            $database = (new Database())->getDatabase();
            $query = "UPDATE tasks SET $columnName = '$value' WHERE _id = " . "$taskId";
            $database->exec($query);
            echo "\n\033[32m Task updated! \033[0m \n";
        } catch (\Exception $e) {
            echo "\n\033[31m Creating new task failed: " . $e->getMessage() . "\033[0m \n";
        }
    }

    /**
     * Delete a task row
     * in table tasks
     * @param int $taskId
     * @return void
     */
    private function deleteThisTask(int $taskId)
    {
        try {
            $database = (new Database())->getDatabase();
            $query = "DELETE FROM tasks WHERE _id = " . "$taskId";
            $database->exec($query);
            echo "\033[32m Task deleted! \033[0m \n";
        } catch (\Exception $e) {
            echo "\033[32m Deleting task failed: " . $e->getMessage() . "\033[0m \n";
        }
    }

    /**
     * Get all tasks from tasks table
     *
     * @return array|false
     */
    public function allTasks(): bool|array
    {
        $database = (new Database())->getDatabase();
        $query = $database->prepare("SELECT _id, name, description, is_done FROM tasks");
        $query->execute();

        return $query->fetchAll();
    }

    /**
     * Getter to get all tasks from tasks table
     *
     * @return array|bool
     */
    public function getAllTasks()
    {
        return  $this->allTasks();
    }

    /**
     * Deleter to delete a task from tasks table
     *
     * @param int $taskId
     * @return void
     */
    public function deleteTask(int $taskId)
    {
        $this->deleteThisTask($taskId);
    }

    /**
     * Return an array of all Tasks items
     *
     * @return array|false
     */
    public function getCountAllTasks()
    {
        return (new Database())->countAllRows('tasks');
    }

    /**
     * Setter to set a task in tasks table
     *
     * @param int $taskId
     * @param string $columnName
     * @param $value
     * @return null
     */
    public function setTask(int $taskId, string $columnName, $value)
    {
        return $this->updateTask($taskId, $columnName, $value);
    }
}