<?php
declare (strict_types = 1);

namespace App\Application\Actions\UserType;

use App\Application\Actions\Action;
use App\Domain\UserType\UserTypeRepository;
use Psr\Log\LoggerInterface;

abstract class UserTypeAction extends Action
{
    /**
     * @var UserTypeRepository
     */
    protected $userTypeRepository;

    /**
     * @param LoggerInterface $logger
     * @param UserTypeRepository  $userTypeRepository
     */
    public function __construct(LoggerInterface $logger, UserTypeRepository $userTypeRepository)
    {
        parent::__construct($logger);
        $this->userTypeRepository = $userTypeRepository;
    }
}