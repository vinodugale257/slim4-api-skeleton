<?php
declare (strict_types = 1);

namespace App\Application\Actions\UserDepartment;

use App\Domain\UserDepartment\UserDepartment;
use App\Infrastructure\Persistence\UserDepartment\InMemoryUserDepartmentRepository;
use Illuminate\Database\Capsule\Manager as DB;
use Psr\Http\Message\ResponseInterface as Response;
use stdClass;

class AddUserDepartmentAction extends UserDepartmentAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $arrstrRequestParameters = get_object_vars($this->getFormData());

        $arrobjDepartments                   = [];
        $arrobjUserDepartments               = [];
        $arrobjDeleteInMemoryUserDepartments = [];

        $objInMemoryUserDepartmentRepository = new InMemoryUserDepartmentRepository();
        $arrobjInMemoryUserDepartments       = $objInMemoryUserDepartmentRepository->findUserDepartmentsOfId($arrstrRequestParameters['id']);

        foreach ($arrobjInMemoryUserDepartments as $objInMemoryUserDepartment) {
            $arrobjUserDepartments[] = $objInMemoryUserDepartment->department_id;
        }

        $arrAddUserdepartments    = array_diff($arrstrRequestParameters['groupIds'], $arrobjUserDepartments);
        $arrDeleteUserDepartments = array_diff($arrobjUserDepartments, $arrstrRequestParameters['groupIds']);

        // Delete existing user groups
        foreach ($arrDeleteUserDepartments as $intDepartmentId) {
            $tempobjInMemoryUserDepartmentRepository = new InMemoryUserDepartmentRepository();

            $arrInMemoryUserDepartment = $tempobjInMemoryUserDepartmentRepository->findUserDepartmentByUserIdBydepartmentId($arrstrRequestParameters['id'], $intDepartmentId);

            $arrobjDeleteInMemoryUserDepartments[] = $arrInMemoryUserDepartment;
        }
        DB::beginTransaction();

        foreach ($arrobjDeleteInMemoryUserDepartments as $objDeleteInMemoryUserDepartment) {
            $objDeleteInMemoryUserDepartment->exists = true;
            try {
                $objDeleteInMemoryUserDepartment->delete();
            } catch (\Illuminate\Database\QueryException $objException) {
                DB::rollback();
                throw new Exception('Failed to delete user groups.');
            }
        }

        // Add existing user groups
        foreach ($arrAddUserdepartments as $intGroupId) {
            $objUserDepartment = new UserDepartment();
            $objUserDepartment->setAttribute('user_id', $arrstrRequestParameters['id']);
            $objUserDepartment->setAttribute('department_id', $intGroupId);

            $arrobjDepartments[] = $objUserDepartment;

        }
        foreach ($arrobjDepartments as $objDepartment) {
            try {
                $objDepartment->insert();
            } catch (\Illuminate\Database\QueryException $objException) {
                DB::rollback();
                throw new Exception('Failed to add user groups.');
            }
        }
        DB::commit();

        $stdClass          = new stdClass();
        $stdClass->message = 'User group assigned successfully.';

        return $this->respondWithData($stdClass);
    }

}