<?php
    if (extension_loaded("sqlite3")) {
        $connection = new SQLite3($GLOBALS['sqlite_db_dir'] . "mind-planner.sqlite");
        # Checking connection status
        if (!$connection) {
            $error = $connection->lastErrorMsg();
        }    
    }
    else {
        die("SQLite isn't loaded here!");
    }
?>