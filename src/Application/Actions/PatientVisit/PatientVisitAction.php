<?php
declare(strict_types=1);

namespace App\Application\Actions\PatientVisit;

use App\Application\Actions\Action;
use App\Domain\PatientVisit\PatientVisitRepository;
use Psr\Log\LoggerInterface;

abstract class PatientVisitAction extends Action
{
    /**
     * @var PatientVisitRepository
     */
    protected $PatientVisitRepository;

    /**
     * @param LoggerInterface $logger
     * @param PatientVisitRepository  $PatientVisitRepository
     */
    public function __construct(LoggerInterface $logger, PatientVisitRepository $PatientVisitRepository)
    {
        parent::__construct($logger);
        $this->PatientVisitRepository = $PatientVisitRepository;
    }
}
