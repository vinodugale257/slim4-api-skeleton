<?php

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteContext;

abstract class AbstractMiddleware implements Middleware
{
    protected $m_objContainer;
    protected $m_objRequest;
    protected $m_objResponse;

    protected $m_strRouteName;

    protected $m_boolSuccess;

    public function __construct($objContainer)
    {
        $this->m_objContainer   = $objContainer;
        $this->m_boolSuccess    = true;
    }

    public function process(Request $objRequest, RequestHandler $objHandler): Response
    {
        $this->m_objRequest     = $objRequest;
        $this->m_strRouteName   = $this->m_objRequest->getAttribute( 'route' )->getName();
        $this->beforeRouteExecution();
        $this->m_objResponse = $objHandler->handle($this->m_objRequest);
        $this->afterRouteExecution();

        return $this->m_objResponse;
    }

    protected function beforeRouteExecution()
    {
        return true;
    }

    protected function afterRouteExecution()
    {
        return true;
    }
}
