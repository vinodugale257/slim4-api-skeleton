<?php
declare (strict_types = 1);

use App\Application\Handlers\HttpErrorHandler;
use App\Application\Handlers\ShutdownHandler;
use App\Application\ResponseEmitter\ResponseEmitter;
use DI\ContainerBuilder;
use Respect\Validation\Validator as V;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;

require __DIR__ . '/../vendor/autoload.php';

$objEnvironment = new \Dotenv\Dotenv(__DIR__ . '/..');
$objEnvironment->load();

// Instantiate PHP-DI ContainerBuilder
$objContainerBuilder = new ContainerBuilder();

// Should uncomment on production
// $objContainerBuilder->enableCompilation(__DIR__ . '/../var/cache');

// Set up settings
$objSettings = require __DIR__ . '/../app/settings.php';
$objSettings($objContainerBuilder);

// Set up dependencies
$objDependencies = require __DIR__ . '/../app/dependencies.php';
$objDependencies($objContainerBuilder);

// Set up repositories
$objRepositories = require __DIR__ . '/../app/repositories.php';
$objRepositories($objContainerBuilder);

// Build PHP-DI Container instance
$objContainer = $objContainerBuilder->build();

// Instantiate the app
AppFactory::setContainer($objContainer);
$objApp              = AppFactory::create();
$objCallableResolver = $objApp->getCallableResolver();

// Register middleware
$objMiddleware = require __DIR__ . '/../app/middleware.php';
$objMiddleware($objApp);

// Register routes
$objRoutes = require __DIR__ . '/../app/routes.php';
$objRoutes($objApp);

/** @var bool $displayErrorDetails */
if ('test' === getenv('BUGSNAG_ENV') || 'production' === getenv('BUGSNAG_ENV')) {
    $bugsnag = Bugsnag\Client::make($objContainer->get('settings')['bugsnag_api_key']);
    \Bugsnag\Handler::register($bugsnag);
}

$boolDisplayErrorDetails = $objContainer->get('settings')['displayErrorDetails'];

// Create Request object from globals
$objServerRequestCreator = ServerRequestCreatorFactory::create();
$objRequest              = $objServerRequestCreator->createServerRequestFromGlobals();

// Create Error Handler
$objResponseFactory = $objApp->getResponseFactory();
$objErrorHandler    = new HttpErrorHandler($objCallableResolver, $objResponseFactory);

// Create Shutdown Handler
$objShutdownHandler = new ShutdownHandler($objRequest, $objErrorHandler, $boolDisplayErrorDetails);
register_shutdown_function($objShutdownHandler);

// Add Routing Middleware
$objApp->addRoutingMiddleware();

// Add Error Middleware
$objErrorMiddleware = $objApp->addErrorMiddleware($boolDisplayErrorDetails, false, false);
$objErrorMiddleware->setDefaultErrorHandler($objErrorHandler);

V::with('App\\Validations\\Rules\\');

// Run App & Emit Response
$objResponse        = $objApp->handle($objRequest);
$objResponseEmitter = new ResponseEmitter();
$objResponseEmitter->emit($objResponse);