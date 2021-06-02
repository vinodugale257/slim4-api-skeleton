<?php
declare (strict_types = 1);

namespace App\Application\Actions\UserDepartment;

use App\Application\Actions\Action;
use App\Domain\UserDepartment\UserDepartmentRepository;
use Psr\Log\LoggerInterface;

abstract class UserDepartmentAction extends Action
{
    /**
     * @var UserDepartmentRepository
     */
    protected $userDepartmentRepository;

    /**
     * @param LoggerInterface $logger
     * @param UserDepartmentRepository  $userDepartmentRepository
     */
    public function __construct(LoggerInterface $logger, UserDepartmentRepository $userDepartmentRepository)
    {
        parent::__construct($logger);
        $this->userDepartmentRepository = $userDepartmentRepository;
    }
}