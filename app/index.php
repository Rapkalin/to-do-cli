<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Services\Database;
use App\Services\Prompts;

$authorizedFileArguments = ['delete'];


if ($argv && count($argv) > 1 && $argv[0] === 'index.php') {
    unset($argv[0]);

    foreach ($argv as $argument) {
        if (!in_array($argument, $authorizedFileArguments)) {
            echo 'invalid argument: ' . $argument  . "\n";
            $invalidArgument[] = $argument;
        }

        if ($argument === 'delete') {
            unlink(__DIR__ . '/database/hiba-challenge-database.db');
            echo 'File deleted: ' . __DIR__ . "/database/hiba-challenge-database.db \n";
            die('Program stopped');
        }
    }

    die();
}

/**
 * Load database
 */
$database = new Database();

if (
    $database->isSqliteInit()
) {

    if (!file_exists(__DIR__ . '/database/' . $database->databaseName)) {
        $database->initDatabase();
    } else {
        echo "\033[92m Database exist, we are ready to go! \n";
    }

} else {
    die("\033[31m App stopped");
}

/**
 * Start To-do app
 */
$todo = new Prompts();
$todo->fire();