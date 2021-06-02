<?php
declare (strict_types = 1);

namespace App\Application\Actions\Department;

use App\Application\Actions\Action;
use App\Domain\Department\DepartmentRepository;
use Psr\Log\LoggerInterface;

abstract class DepartmentAction extends Action
{
    /**
     * @var DepartmentRepository
     */
    protected $departmentRepository;

    /**
     * @param LoggerInterface $logger
     * @param DepartmentRepository  $departmentRepository
     */
    public function __construct(LoggerInterface $logger, DepartmentRepository $departmentRepository)
    {
        parent::__construct($logger);
        $this->departmentRepository = $departmentRepository;
    }
}