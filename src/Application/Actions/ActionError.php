<?php
declare (strict_types = 1);

namespace App\Application\Actions;

use JsonSerializable;

class ActionError implements JsonSerializable
{
    public const BAD_REQUEST             = 'BAD_REQUEST';
    public const INSUFFICIENT_PRIVILEGES = 'INSUFFICIENT_PRIVILEGES';
    public const NOT_ALLOWED             = 'NOT_ALLOWED';
    public const NOT_IMPLEMENTED         = 'NOT_IMPLEMENTED';
    public const RESOURCE_NOT_FOUND      = 'RESOURCE_NOT_FOUND';
    public const SERVER_ERROR            = 'SERVER_ERROR';
    public const UNAUTHENTICATED         = 'UNAUTHENTICATED';
    public const VALIDATION_ERROR        = 'VALIDATION_ERROR';
    public const VERIFICATION_ERROR      = 'VERIFICATION_ERROR';
    public const ACCESS_TOKEN_EXPIRED    = 'ACCESS_TOKEN_EXPIRED';
    public const INVALID_CREDENTIALS     = 'INVALID_CREDENTIALS';
    public const INVALID_PERMISSIONS     = 'INVALID_PERMISSIONS';

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $description;

    /**
     * @var array
     */
    private $errorMessages;

    /**
     * @param string        $type
     * @param string|null   $description
     */
    public function __construct(string $type, ?string $description, ?array $errorMessages = [])
    {
        $this->type          = $type;
        $this->description   = $description;
        $this->errorMessages = $errorMessages;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return self
     */
    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return array
     */
    public function getErrorMessages(): array
    {
        return $this->errorMessages;
    }

    /**
     * @param string|null $description
     * @return self
     */
    public function setDescription(?string $description = null): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @param array|null $description
     * @return self
     */
    public function setErrorMessages(?array $errorMessages = null): self
    {
        $this->errorMessages = $errorMessages;
        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'type'          => $this->type,
            'description'   => $this->description,
            'errorMessages' => $this->errorMessages,
        ];
    }
}