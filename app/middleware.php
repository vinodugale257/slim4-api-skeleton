<?php

declare(strict_types=1);

use App\Application\Middleware\DatabaseConnectionMiddleware;
use App\Application\Middleware\UserAuthenticationMiddleware;
use Slim\App;

return function (App $objApp) {
    $objConteiner = $objApp->getContainer();
    $objApp->add(new UserAuthenticationMiddleware($objConteiner));
    $objApp->add(new DatabaseConnectionMiddleware($objConteiner));
};
