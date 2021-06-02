<?php
declare (strict_types = 1);

namespace App\Application\Actions;

use App\Application\Actions\ActionError;
use App\Domain\DomainException\DomainRecordNotFoundException;
use Firebase\JWT\JWT;
use Library\Validator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Respect\Validation\Validator as V;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;

abstract class Action
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var array
     */
    protected $args;

    /**
     * @var Validator
     */
    protected $m_objValidator;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        V::with('App\Application\Validations\Rules\\');

        $this->m_objValidator = new Validator();
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return Response
     * @throws HttpNotFoundException
     * @throws HttpBadRequestException
     */
    public function __invoke(Request $request, Response $response, $args): Response
    {
        $this->request  = $request;
        $this->response = $response;
        $this->args     = $args;

        try {
            return $this->action();
        } catch (DomainRecordNotFoundException $e) {
            throw new HttpNotFoundException($this->request, $e->getMessage());
        }
    }

    /**
     * @return Response
     * @throws DomainRecordNotFoundException
     * @throws HttpBadRequestException
     */
    abstract protected function action(): Response;

    /**
     * @return array|object
     * @throws HttpBadRequestException
     */
    protected function getFormData()
    {
        $fileContent = file_get_contents('php://input');

        if (!valStr($fileContent)) {
            return null;
        }

        $input = json_decode($fileContent);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new HttpBadRequestException($this->request, 'Malformed JSON input.');
        }

        return $input;
    }

    /**
     * @param  string $name
     * @return mixed
     * @throws HttpBadRequestException
     */
    protected function resolveArg(string $name)
    {
        if (!isset($this->args[$name])) {
            throw new HttpBadRequestException($this->request, "Could not resolve argument `{$name}`.");
        }

        return $this->args[$name];
    }

    /**
     * @param  array|object|null $data
     * @return Response
     */
    protected function respondWithErrorMessages($arrstrErrorMessages = []): Response
    {
        $error = new ActionError(
            ActionError::VALIDATION_ERROR,
            'Failed to handle your request.',
            $arrstrErrorMessages
        );

        $payload        = new ActionPayload(422, null, $error);
        $encodedPayload = json_encode($payload, JSON_PRETTY_PRINT);

        $this->response->getBody()->write($encodedPayload);
        return $this->response->withHeader('Content-Type', 'application/json');
    }

    /**
     * @param  array|object|null $data
     * @return Response
     */
    protected function respondWithData($data = null): Response
    {
        $payload = new ActionPayload(200, $data);
        return $this->respond($payload);
    }

    /**
     * @param ActionPayload $payload
     * @return Response
     */
    protected function respond(ActionPayload $payload): Response
    {
        $json = json_encode($payload, JSON_PRETTY_PRINT);
        $json = json_encode(array_merge(json_decode($json, true), $this->getAccessToken()), JSON_PRETTY_PRINT);

        $this->response->getBody()->write($json);
        return $this->response->withHeader('Content-Type', 'application/json');
    }

    protected function getAccessToken(): array
    {
        $objDate = new \DateTime('+15 minutes');

        $stdCurrentUser = $this->request->getAttribute('current_user');

        if (!isset($stdCurrentUser->user_type_id) || !isset($stdCurrentUser->reference_id)) {
            return [];
        }

        $strUserInfo = [
            'user_type_id' => $stdCurrentUser->user_type_id,
            'reference_id' => $stdCurrentUser->reference_id,
            'expires'      => $objDate->getTimeStamp(),
        ];

        $strAccessToken = JWT::encode($strUserInfo, getenv('JWT_SECRET'), 'HS256');

        return [
            'authInfo' => [
                'tokenType'   => 'bearer',
                'accessToken' => $strAccessToken,
            ],
        ];
    }
}