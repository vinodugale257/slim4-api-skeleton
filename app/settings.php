<?php
declare (strict_types = 1);

use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $objContainerBuilder) {

    $arrstrSettings['displayErrorDetails'] = (bool) getenv('DEBUG');

    $arrstrSettings['logger'] = [
        'name'  => 'slim-app',
        'path'  => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
        'level' => Logger::DEBUG,
    ];

    // Database settings
    $arrstrSettings['db'] = [
        'driver'    => getenv('DB_DRIVER'),
        'host'      => getenv('DB_HOST'),
        'port'      => getenv('DB_PORT'),
        'database'  => getenv('DB_NAME'),
        'username'  => getenv('DB_USER'),
        'password'  => getenv('DB_PASSWORD'),
        'charset'   => getenv('DB_CHARSET'),
        'collation' => getenv('DB_COLLATION'),
    ];

    $arrstrSettings['ob_storage'] = [
        'region'                         => getenv('OB_STORAGE_REGION'),
        'endpoint'                       => getenv('OB_STORAGE_PROTOCOL') . '://' . getenv('OB_STORAGE_BUCKET') . '.' . getenv('OB_STORAGE_REGION') . '.' . getenv('OB_STORAGE_ENDPOINT'),
        'key'                            => getenv('OB_STORAGE_KEY'),
        'secret'                         => getenv('OB_STORAGE_SECRET'),
        'bucket'                         => ('production' == getenv('APP_ENV') ? 'p' : 'd'),
        'client_path'                    => getenv('OB_STORAGE_PROTOCOL') . '://' . getenv('OB_STORAGE_BUCKET') . '.' . getenv('OB_STORAGE_REGION') . '.cdn.' . getenv('OB_STORAGE_ENDPOINT') . '/' . ('dev' == getenv('APP_ENV') ? 'd' : 'p') . '/',
        'mahoganivishwaagro_base_folder' => getenv('OB_STORAGE_MAHOGANIVISHWAAGRO_BASE_FOLDER'),
    ];

    $arrstrSettings['jwt_secret']      = getenv('JWT_SECRET');
    $arrstrSettings['bugsnag_api_key'] = getenv('BUGSNAG_API_KEY');

    $objContainerBuilder->addDefinitions(['settings' => $arrstrSettings]);
};
