<?php

namespace App\Services;

use App\Models\Task;

class Prompts
{
    /**
     * @var array|string[]
     */
    protected array $commands = [
        "List all tasks? \n",
        "Create a new task? \n",
        "Update a task? \n",
        "Delete a tasks? \n",
        "Exit  \n"
    ];

    /**
     * @var array|string[]
     */
    protected array $taskUpdatecommands = [
        "Update task name? \n",
        "Update task description? \n",
        "Update task status (done/not done)? \n",
        "Exit?  \n"
    ];

    /**
     * List of available colors
     * const array
     */
    const COLORS = [
        'default' => "\033[39m",
        'black' => "\033[30m",
        'red' => "\033[31m",
        'green' => "\033[32m",
        'yellow' => "\033[33m",
        'blue' => "\033[34m",
        'magenta' => "\033[35m",
        'cyan' => "\033[36m",
        'light-grey' => "\033[37m",
        'dark-grey' => "\033[90m",
        'light-red' => "\033[91m",
        'light-green' => "\033[92m",
        'light-yellow' => "\033[93m",
        'light-blue' => "\033[94m",
        'light-magenta' => "\033[95m",
        'light-cyan' => "\033[96m",
        'white' => "\033[97m"
    ];

    /**
     * @return void
     */
    public function fire(): void
    {
        $this->init();
    }

    /**
     * Init the program
     *
     * @return void
     */
    public function init(): void
    {
        $this->promptWelcome();

        /*
         * If not tasks created
         * We ask to create the first task
         *
         * If at least one task already exists
         * We ask to choose: create a task or see the list
         */

        $tasks = (new Task())->getCountAllTasks();

        if ($tasks == 0) {
            echo "Seems that there is no existing task! \n";
            echo "Start by creating your first task \n";
            $this->promptCreateTask();
            $this->promptAllCommands();
        } else {
            $this->promptAllCommands();
            die();
        }
    }

    /**
     * @param string $message
     * @return void
     */
    private function defaultMessage(string $message): void
    {
        echo "\n" . self::COLORS['white'] . $message . self::COLORS['default'] . "\n";
    }

    /**
     * @param string $message
     * @return void
     */
    private function alert(string $message): void
    {
        echo "\n" . self::COLORS['red'] . $message . self::COLORS['default'] . "\n";
    }

    /**
     * @param string $message
     * @return void
     */
    private function notice(string $message): void
    {
        echo "\n" . self::COLORS['yellow'] . $message . self::COLORS['default'] . "\n";
    }

    /**
     * @param string $message
     * @return void
     */
    private function info(string $message): void
    {
        echo "\n" . self::COLORS['green'] . $message . self::COLORS['default'] . "\n";
    }

    /**
     * @param string $message
     * @return void
     */
    private function smallInfo(string $message): void
    {
        echo "\n" . self::COLORS['light-green'] . $message . self::COLORS['default'] . "\n";
    }

    /**
     * @param string $message
     * @return void
     */
    private function highlight(string $message): void
    {
        echo "\n" . self::COLORS['light-cyan'] . $message . self::COLORS['default'] . "\n";
    }

    /**
     * Format echo message
     *
     * @param string $type
     * @param string $message
     * @return void
     */
    public function formatMessage(string $type, string $message): void
    {
        switch ($type) {
            case 'info':
                $this->info($message);
                break;
            case 'smallInfo':
                $this->smallInfo($message);
                break;
            case 'notice':
                $this->notice($message);
                break;
            case 'alert':
                $this->alert($message);
                break;
            case 'highlight':
                $this->highlight($message);
                break;
            default:
                $this->defaultMessage($message);
        }
    }

    /**
     * Get the clean user input
     *
     * @param bool $retry
     * @return array|string|string[]|null
     */
    private function getUserInput(bool $retry = false): array|string|null
    {
        if ($retry) {
            echo "Something is wrong with your answer. please retry: \n";
        }

        $handle = fopen ("php://stdin","r");
        $userInput = fgets($handle);
        $cleanInput = preg_replace("/\n/", "", $userInput);

        if (empty($cleanInput)) {
            $cleanInput = $this->getUserInput(true);
        }
        return $cleanInput;
    }

    /**
     * Display welcome prompt
     *
     * @return void
     */
    private function promptWelcome(): void
    {
        echo "\n";
        echo "\n";
        echo "\n";
        echo "**********************************\n";
        echo "*                                *\n";
        echo "*             Welcome!           *\n";
        echo "*     To your tasks manager!     *\n";
        echo "*                                *\n";
        echo "**********************************\n";
        echo "\n \033[0m";
    }

    /**
     * Dispatch the prompt to the right action
     *
     * @param int $command
     * @return void
     */
    private function dispatchPrompt(int $command): void
    {
        // Clear console
        echo chr(27).chr(91).'H'.chr(27).chr(91).'J';

        switch ($command) {
            case 1:
                echo "\n";
                echo "\n";
                $this->promptAllTasks();
                break;
            case 2:
                echo "\n";
                echo "\n";
                $this->promptCreateTask();
                break;
            case 3:
                echo "\n";
                echo "\n";
                $this->promptUpdateTask();
            case 4:
                echo "\n";
                echo "\n";
                $this->promptDeleteTask();
                break;
            case 5:
                echo "\n";
                echo "\n";
                $this->promptExit();
                break;
        }

        echo "\n";
        echo "\n";
        echo "\n";
        $this->promptAllCommands(false);
    }

    /**
     * Prompt all available commands
     *
     * @param bool $log
     * @return void
     */
    private function promptAllCommands(bool $log = true) : void
    {
        if ($log) {
            echo "\n";
            echo "\n";
            echo "*********************************\n";
            echo "*                               *\n";
            echo "*  Here are all your commands:  *\n";
            echo "*        Choose a number!       *\n";
            echo "*                               *\n";
            echo "*********************************\n";
            echo "\n";
        } else {
            echo "\n";
            echo "********************************\n";
            echo "*                              *\n";
            echo "*  What else do you wanna do?  *\n";
            echo "*                              *\n";
            echo "********************************\n";
            echo "\n";
        }
        $this->displayListCommands($this->commands);
        $command = $this->getUserCommand($this->commands);
        $this->dispatchPrompt($command);
    }

    /**
     * Prompt all available tasks
     *
     * @return void
     */
    private function promptAllTasks() : void
    {
        echo "\n";
        echo "\n";
        echo "**************************************\n";
        echo "*                                    *\n";
        echo "*  Here are all your current tasks!  *\n";
        echo "*                                    *\n";
        echo "**************************************\n";
        echo "\n";

        $this->displayListTasks();
    }

    /**
     * Prompt to create a task
     *
     * @return void
     */
    private function promptCreateTask(): void
    {
        $task = [];

        // Name
        $this->formatMessage("default", "What is your task name?");
        $name = strip_tags($this->getUserInput());
        $task['name'] = $this->getValidUserInput($name, 'isInputValidString');

        // Description
        $this->formatMessage("default", "What is your task description?");
        $description =  $this->getUserInput();
        $task['description'] = $this->getValidUserInput($description , 'isInputValidString');

        // Status
        $task['isDone'] = 0;

        (new Task)->createTask($task);
    }

    /**
     * Prompt to update a task
     *
     * @return void
     */
    function promptUpdateTask() : void
    {
        $this->formatMessage("default", "What task would you like to update? \n Please select the Task-id you wanna update.");
        $this->displayListTasks();
        $this->formatMessage("default", "Enter the Task-id here: ");
        $taskId = $this->getValidUserInput($this->getUserInput(), 'isInputValidInteger');

        $this->formatMessage("default", "What would you like to update?");
        $this->displayListCommands($this->taskUpdatecommands);
        $value = $this->getUserCommand($this->taskUpdatecommands);

        if ($value != array_search($this->taskUpdatecommands[3], $this->taskUpdatecommands)+1) {
            $this->promptColumnToUpdate($value, $taskId);
            $this->promptAllCommands();
        } else {
            $this->promptExit();
        }
    }

    /**
     * Prompt to delete a task
     *
     * @param bool $retry
     * @return void
     */
    function promptDeleteTask(bool $retry = false): void
    {
        if ($retry) {
            $taskId = $this->getValidUserInput($this->getUserInput(true), 'isInputValidInteger');
        } else {
            $this->formatMessage("default", "What task would you like to delete? \n Please select the Task-id you wanna delete.");
            $this->displayListTasks();
            $taskId = $this->getValidUserInput($this->getUserInput(), 'isInputValidInteger');
        }

        $this->formatMessage("default", "Are you sure you want to delete the task $taskId? [yes/no]");
        $input = $this->getValidUserInput($this->getUserInput(), 'isInputValidString');

        if (is_string($input)) {
            switch ($input) {
                case 'yes':
                    (new Task())->deleteTask($taskId);
                case 'no':
                    echo "\n";
                    echo "\n";
                    $this->promptAllCommands(false);
                    break;
                default:
                    $this->promptExit(true);
            }
        }
    }

    /**
     * Prompt to exit the program
     *
     * @param bool $retry
     * @return void
     */
    private function promptExit(bool $retry = false) : void
    {
        if ($retry) {
            $input = strip_tags($this->getUserInput(true));
        } else {
            $this->formatMessage("default", "re you sure you want to Exit the program? [yes/no]");
            $input = strip_tags($this->getUserInput());
        }

        if (is_string($input)) {
            switch ($input) {
                case 'yes':
                    die('K- bye!');
                case 'no':
                    echo "\n";
                    echo "\n";
                    $this->promptAllCommands(false);
                    break;
                default:
                    $this->promptExit(true);
            }
        }

        echo "\n";
        echo "\n";
        echo "\n";
        $this->promptAllCommands(false);
    }

    /**
     * Get User input
     * And update task
     *
     * @param int $columnId
     * @param int $taskId
     * @return void
     */
    private function promptColumnToUpdate(int $columnId, int $taskId): void
    {
        switch ($columnId) {
            case 1:
                $columnName = 'name';
                break;
            case 2:
                $columnName = 'description';
                break;
            case 3:
                $columnName = 'status';
                break;
        }

        if ($columnName !== "status") {
            $this->formatMessage("default", "Please enter the new $columnName");
            $value = $this->getValidUserInput($this->getUserInput(), 'isInputValidString');
            (new Task())->setTask($taskId, $columnName, $value);

            $this->formatMessage("info", "Here is the updated tasks list:");
            $this->displayListTasks();
            echo self::COLORS['white'];
        } else {
            $this->formatMessage("default", "Please enter the new $columnName\n [1] - not done \n [2] - done ");
            $input = $this->getUserCommand(2);
            $value = (int) $input === 1 ? 0 : 1;

            (new Task())->setTask($taskId, 'is_done', $value);

            $this->formatMessage("info", "Here is the updated tasks list:");
            $this->displayListTasks();
            echo self::COLORS['white'];
        }
    }

    /**
     * Get user command
     *
     * @param array|int $valueLimit
     * @return int|string|null
     */
    private function getUserCommand(array|int $valueLimit): int|string|null
    {
        $limit = is_array($valueLimit) ? count($valueLimit) : $valueLimit;
        $input = $this->getValidUserInput($this->getUserInput(), 'isInputValidInteger');

        if ($input <= $limit) {
            return $input;
        }

        do {
            $input = $this->getValidUserInput($this->getUserInput(true), 'isInputValidInteger');
        } while ($input > $limit);

        return $input;
    }

    /**
     * Display list of available commands
     *
     * @param array $commands
     * @return void
     */
    private function displayListCommands(array $commands): void
    {
        foreach ($commands as  $key => $command) {
            echo "[" . $key+1 . "] " . $command;
        }
    }

    /**
     * Display list of available tasks
     *
     * @return void
     */
    private function displayListTasks(): void
    {
        $tasks = (new Task())->getAllTasks();

        foreach ($tasks as $task) {
            $isDone = $task['is_done'] ? 'X' : ' ';

            $this->formatMessage("info", self::COLORS['light-cyan'] . "Task-id: " . $task['_id'] . "\033[0m - Status: [$isDone] \n" . $task['name'] . ": " . $task['description'] . "\n");
        }
    }

    /**
     * Valid a string input
     *
     * @param string $input
     * @return bool
     */
    private function isInputValidString(string $input): bool
    {
        /*
         * A valid string must:
         * - Not be an integer
         * - Not be empty || Null || False
         * - Be a valid string
         * - Be at least 3 characters long
         */

        /*
         * Check if input is an integer
         * If "empty((int) $input" is an integer it will return !== 0
         * If "empty((int) $input" is an integer so "empty((int) $input" === false because then $input is not empty
         * If "empty((int) $input" is a string so "empty((int) $input" === true
         */
        return !is_numeric($input) && strlen($input) >= 3;
    }

    /**
     * Valid an integer input
     *
     * @param string $input
     * @return bool
     */
    private function isInputValidInteger(string $input): bool
    {
        /*
         * Check if input is an integer
         * If "empty((int) $input" is an integer it will return !== 0
         * If "empty((int) $input" is an integer so "empty((int) $input" === false because then $input is not empty
         */

        return is_numeric($input) && !empty($input);
    }

    /**
     * Get a valid input from user
     *
     * @param string $input
     * @param string $hook
     * @return array|string|null
     */
    public function getValidUserInput(string $input, string $hook): array|string|null
    {
        if ($this->$hook($input)) {
            return $input;
        }

        do {
            $input = $this->getUserInput(true);
        } while (!$this->$hook($input));

        return $input;
    }
}