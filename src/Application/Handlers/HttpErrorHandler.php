<?php
declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Actions\ActionError;
use App\Application\Actions\ActionPayload;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpNotImplementedException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Handlers\ErrorHandler as SlimErrorHandler;
use Throwable;

class HttpErrorHandler extends SlimErrorHandler
{
    /**
     * @inheritdoc
     */
    protected function respond(): Response
    {
        $objException = $this->exception;
        $intStatusCode = 500;
        $objActionError = new ActionError(
            ActionError::SERVER_ERROR,
            'An internal error has occurred while processing your request.'
        );

        if ($objException instanceof HttpException) {
            
            $intStatusCode = $objException->getCode();
            $objActionError->setDescription($objException->getMessage());

            if ($objException instanceof HttpNotFoundException) {
                $objActionError->setType(ActionError::RESOURCE_NOT_FOUND);
            } elseif ($objException instanceof HttpMethodNotAllowedException) {
                $objActionError->setType(ActionError::NOT_ALLOWED);
            } elseif ($objException instanceof HttpUnauthorizedException) {
                $objActionError->setType(ActionError::UNAUTHENTICATED);
            } elseif ($objException instanceof HttpForbiddenException) {
                $objActionError->setType(ActionError::INSUFFICIENT_PRIVILEGES);
            } elseif ($objException instanceof HttpBadRequestException) {
                $objActionError->setType(ActionError::BAD_REQUEST);
            } elseif ($objException instanceof HttpNotImplementedException) {
                $objActionError->setType(ActionError::NOT_IMPLEMENTED);
            }
        }

        if (
            !($objException instanceof HttpException)
            && ($objException instanceof Exception || $objException instanceof Throwable)
            && $this->displayErrorDetails
        ) {
            $objActionError->setDescription($objException->getMessage());
            
            if(method_exists($objException, 'getStatusCode' ) && valStr($objException->getStatusCode())) {
                $intStatusCode = (int)$objException->getStatusCode();
            }

            if(method_exists($objException, 'getType' ) && valStr($objException->getType())) {
                $objActionError->setType($objException->getType());
            }
        }

        $objActionPayload = new ActionPayload($intStatusCode, null, $objActionError);
        $encodedPayload = json_encode($objActionPayload, JSON_PRETTY_PRINT);

        $objResponse = $this->responseFactory->createResponse($intStatusCode);
        $objResponse->getBody()->write($encodedPayload);

        return $objResponse->withHeader('Content-Type', 'application/json');
    }
}
