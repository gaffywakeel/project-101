<?php

namespace App\Helpers;

use Illuminate\Database\ConfigurationUrlParser;
use Illuminate\Support\Arr;
use RuntimeException;
use Spatie\DbDumper\Databases\MySql;
use Spatie\DbDumper\Databases\PostgreSql;
use Spatie\DbDumper\Databases\Sqlite;
use Spatie\DbDumper\DbDumper;

class DumperFactory
{
    public static function createFromConnection(string $connectionName): DbDumper
    {
        $parser = new ConfigurationUrlParser();

        $config = $parser->parseConfiguration(config("database.connections.$connectionName"));

        if (isset($config['read'])) {
            $config = Arr::except(array_merge($config, $config['read']), ['read', 'write']);
        }

        $dumper = static::forDriver($config['driver'])
            ->setHost($config['host'])
            ->setUserName($config['username'])
            ->setPassword($config['password'])
            ->setDbName($config['database']);

        if ($dumper instanceof MySql) {
            $dumper->setDefaultCharacterSet($config['charset']);
        }

        if (isset($config['port'])) {
            $dumper = $dumper->setPort($config['port']);
        }

        if (isset($config['unix_socket'])) {
            $dumper = $dumper->setSocket($config['unix_socket']);
        }

        return $dumper;
    }

    protected static function forDriver(string $name): DbDumper
    {
        $driver = strtolower($name);

        return match ($driver) {
            'mysql', 'mariadb' => new MySql(),
            'pgsql' => new PostgreSql(),
            'sqlite' => new Sqlite(),
            default => throw new RuntimeException('Unknown database driver')
        };
    }
}
