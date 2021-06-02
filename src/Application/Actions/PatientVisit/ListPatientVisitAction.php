<?php
declare (strict_types = 1);

namespace App\Application\Actions\PatientVisit;

use App\Domain\PatientVisit\PatientVisitSearchFilter;
use App\Infrastructure\Persistence\PatientVisit\InMemoryPatientVisitRepository;
use Psr\Http\Message\ResponseInterface as Response;

class ListPatientVisitAction extends PatientVisitAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {

        $arrstrRequestParameters = [];
        $objFormData             = $this->getFormData();

        if ($objFormData) {
            $arrstrRequestParameters = get_object_vars($objFormData);
        }

        $objPagination = new PatientVisitSearchFilter($arrstrRequestParameters);

        $objInMemoryPatientVisitRepository = new InMemoryPatientVisitRepository;
        $intTotalCount                     = $objInMemoryPatientVisitRepository->fetchAllPatientVisitsCount($objPagination);

        $PatientVisits['PatientVisits']['data'] = $this->PatientVisitRepository->findAllPatientVisitsByPageNumberByLimit($objPagination);

        $PatientVisits['PatientVisits']['totalCount'] = $intTotalCount;

        return $this->respondWithData($PatientVisits);
    }
}