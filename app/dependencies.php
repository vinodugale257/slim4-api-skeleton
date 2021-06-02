<?php
declare (strict_types = 1);

use DI\ContainerBuilder;
use Library\Database;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $arrstrLoggerSettings = $c->get('settings')['logger'];

            $objlogger = new Logger($arrstrLoggerSettings['name']);

            $processor = new UidProcessor();
            $objlogger->pushProcessor($processor);

            $objStreamHandler = new StreamHandler($arrstrLoggerSettings['path'], $arrstrLoggerSettings['level']);
            $objlogger->pushHandler($objStreamHandler);

            return $objlogger;
        },
        'db'                   => function (ContainerInterface $c) {
            $arrstrDbSettings = $c->get('settings')['db'];

            $arrstrDbConfig = [
                'driver'    => $arrstrDbSettings['driver'],
                'host'      => $arrstrDbSettings['host'],
                'port'      => $arrstrDbSettings['port'],
                'database'  => $arrstrDbSettings['database'],
                'username'  => $arrstrDbSettings['username'],
                'password'  => $arrstrDbSettings['password'],
                'charset'   => $arrstrDbSettings['charset'],
                'collation' => $arrstrDbSettings['collation'],
            ];

            $objDatabase = new Database();
            $objDatabase->connect($arrstrDbConfig);
            return $objDatabase->getConnection();
        },
        'objObjectStore'       => function (ContainerInterface $c) {
            $arrstrObStorageSettings = $c->get('settings')['ob_storage'];

            return new \Aws\S3\S3Client([
                'version'     => 'latest',
                'region'      => $arrstrObStorageSettings['region'],
                'endpoint'    => $arrstrObStorageSettings['endpoint'],
                'credentials' => [
                    'key'    => $arrstrObStorageSettings['key'],
                    'secret' => $arrstrObStorageSettings['secret'],
                ],
            ]);
        },
    ]);
};
