<?php
declare (strict_types = 1);

namespace App\Application\Actions\PatientVisit;

// use App\Domain\UserType\UserType;
use App\Domain\PatientVisit\PatientVisit;
use App\Domain\Patient\Patient;
use App\Domain\Person\Person;
use Exception;
use Illuminate\Database\Capsule\Manager as DB;
use Psr\Http\Message\ResponseInterface as Response;
use stdClass;

class AddPatientVisitAction extends PatientVisitAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $arrstrRequestParameters = get_object_vars($this->getFormData());

        $arrstrRequestPersonParameters['first_name']    = $arrstrRequestParameters['first_name'];
        $arrstrRequestPersonParameters['last_name']     = $arrstrRequestParameters['last_name'];
        $arrstrRequestPersonParameters['email_address'] = $arrstrRequestParameters['email_address'];
        $arrstrRequestPersonParameters['mobile_number'] = $arrstrRequestParameters['mobile_number'];
        // $arrstrRequestPatientVisitParameters = $arrstrRequestParameters['patientvisitInfo'];

        $objPerson = new Person();
        $objPerson->fill($arrstrRequestPersonParameters);

        DB::beginTransaction();
        try {
            $objPerson->insert();
        } catch (\Illuminate\Database\QueryException $objException) {
            DB::rollback();
            throw new Exception('Failed to add person information.');
        }

        $arrstrRequestPatientParameters['person_id']         = $objPerson->getAttribute('id');
        $arrstrRequestPatientParameters['patient_status_id'] = 1;

        $objPatient = new Patient();
        $objPatient->fill($arrstrRequestPatientParameters);

        try {
            $objPatient->insert();
        } catch (\Illuminate\Database\QueryException $objException) {
            DB::rollback();
            throw new Exception('Failed to add patient information.');
        }

        $arrstrRequestPatientVisitParameters['patient_id']         = $objPatient->getAttribute('id');
        $arrstrRequestPatientVisitParameters['scheduled_datetime'] = $arrstrRequestParameters['apointment_datetime'];

        $objPatientVisit = new PatientVisit();
        $objPatientVisit->fill($arrstrRequestPatientVisitParameters);

        try {
            $objPatientVisit->insert();
        } catch (\Illuminate\Database\QueryException $objException) {
            DB::rollback();
            throw new Exception('Failed to add patientvisit information.');
        }
        DB::commit();

        $stdPatientVisitOrder          = new stdClass();
        $stdPatientVisitOrder->message = 'PatientVisit Order information added successfully.';

        return $this->respondWithData($stdPatientVisitOrder);
    }
}