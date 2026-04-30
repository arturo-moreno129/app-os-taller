<?php
class Database
{
    private static ?mysqli $connection = null;

    public static function connect(): ?mysqli
    {
        if (self::$connection !== null) {
            return self::$connection;
        }

        $config = require __DIR__ . '/../config/config.php';
        mysqli_report(MYSQLI_REPORT_OFF);

        $connection = mysqli_connect(
            $config['db_host'],
            $config['db_user'],
            $config['db_pass'],
            $config['db_name']
        );

        if (!$connection) {
            define('NO_DB_ACCESS', true);
            self::$connection = null;
            return null;
        }

        self::$connection = $connection;
        return self::$connection;
    }
}
