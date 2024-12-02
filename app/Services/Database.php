<?php

namespace App\Services;

use PDO;
use PDOException;

class Database
{
    /**
     * Database schema
     *
     * @var array[]
     */
    protected array $tables = [
        'tasks' => [
            '_id' => 'INTEGER PRIMARY KEY NOT NULL,',
            'name' => 'varchar(64),',
            'description' => 'text,',
            'is_done' => 'bool',
        ],
    ];

    /**
     * Database name
     *
     * @var string
     */
    private string $databaseNamePath = 'sqlite:database/hiba-challenge-database.db';

    /**
     * @var string
     */
    public string $databaseName = 'hiba-challenge-database.db';

    /**
     * Database credentials
     *
     * @var array|string[]
     */
    private array $credientials = [
        'user' => 'root',
        'password' => 'root'
    ];

    /**
     * Check if SQLite is supported
     *
     * @return bool
     */
    public function isSqliteInit(): bool
    {
        /*
         * Create a database named hiba-challenge
         * Check if SQLite3 is supported
         */
        if(!class_exists('SQLite3')) {
            echo "\033[31m SQLite 3 NOT supported. \033[0m \n"; // light green
            return false;
        } else {
            echo "\033[32m SQLite 3 supported. \033[0m \n";
            return true;
        }
    }

    /**
     * Create database
     * Check if database exists
     * And create the needed tables
     *
     * @return bool|PDO|null
     */
    public function initDatabase(): PDO|bool|null
    {
        $database = $this->createDatabaseConnexion();

        if (file_exists(__DIR__ . '/../database/' . $this->databaseName)) {
            $this->createTables($database, $this->tables);
            echo "\033[92m Database is ready to go. \033[0m \n";
            return true;
        } else {
            if (file_exists(__DIR__ . '/../database/' . $this->databaseName)) {
                unlink(__DIR__ . '/../database/' . $this->databaseName);
            }
            echo "\033[31m Error while creating database connexion. \033[0m \n";
            return false;
        }
    }

    /**
     * Create a database connection
     *
     * @return PDO|null PDO if database has been created if not stop program
     */

    /**
     *  Create a database connection
     *
     * @param bool $log
     * @return PDO|null
     */
    private function createDatabaseConnexion(bool $log = true) : PDO|null
    {
        /* Connect to an ODBC database using driver invocation */
        $databasePath = $this->databaseNamePath;
        $user = $this->credientials['user'];
        $password = $this->credientials['password'];

        try {
            $database = new PDO($databasePath, $user, $password);
            if ($log) {
                echo "\033[32m database connexion successfull \033[0m \n";
            }
            return $database;
        } catch (PDOException $e) {
            echo "\033[31m Database connexion failed: " . $e->getMessage() . "\033[0m \n";
            die();
        }
    }

    /**
     * Check if a table exists in the current database.
     *
     * @param PDO $database PDO instance connected to a database.
     * @param string $table Table to search for.
     * @return bool TRUE if table exists, FALSE if no table found.
     */
    private function tableExists(PDO $database, string $table): bool
    {
        // Try a select statement against the table
        // Run it in try-catch in case PDO is in ERRMODE_EXCEPTION.
        try {
            $result = $database->query("SELECT 1 FROM {$table} LIMIT 1");
            echo "\033[93m Table already exist: $table \033[0m \n";
        } catch (\Exception $e) {
            // We got an exception (table not found)
            echo "\033[93m Table not found: " . $e->getMessage() . "\033[0m \n";
            return false;
        }

        // Result is either boolean FALSE (no table found) or PDOStatement Object (table found)
        return $result !== false;
    }

    /**
     * Function use to create all needed tables if not exist
     * When database first init
     *
     * @param PDO $database
     * @param array $tables
     * @return void
     */
    function createTables(PDO $database, array $tables) : void
    {
        foreach ($tables as $tableName => $columns) {
            if (!$this->tableExists($database, $tableName)) {
                $query = "CREATE TABLE IF NOT EXISTS $tableName(";
                foreach ($columns as $key => $column) {
                    $query .= "$key $column";
                }
                $query .= ");";

                try {
                    $database->exec($query);
                    echo "\033[32m Table: $tableName created.\033[0m \n";
                } catch (\Exception $e) {
                    echo "\033[31m error while creating table $tableName: " . $e->getMessage() ."\033[0m \n";
                }
            }
        }
    }

    /**
     *
     *
     * @param string $table
     * @return false|int
     */
    public function getAllRows(string $table)
    {
        $database = $this->createDatabaseConnexion(false);
        $query = $database->prepare("SELECT * FROM $table");
        $query->execute();

        return $query->fetchAll();
    }

    /**
     * @param string $table
     * @return int
     */
    public function countAllRows(string $table)
    {
        $database = $this->createDatabaseConnexion(false);
        $query = $database->prepare("SELECT _id FROM $table");
        $query->execute();
        $allTasks = $query->fetchAll();

        return count($allTasks);
    }

    /**
     * Return a database connexion
     * @return PDO|null
     */
    public function getDatabase(): ?PDO
    {
        return $this->createDatabaseConnexion(false);
    }

}
