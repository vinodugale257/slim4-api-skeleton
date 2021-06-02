<?php
declare (strict_types = 1);

namespace App\Application\Actions\User;

use App\Domain\User\UserSearchFilter;
use App\Infrastructure\Persistence\User\InMemoryUserRepository;
use Psr\Http\Message\ResponseInterface as Response;

class ListUsersAction extends UserAction
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

        $objPagination = new UserSearchFilter($arrstrRequestParameters);

        $objInMemoryUserRepository = new InMemoryUserRepository;
        $intTotalCount             = $objInMemoryUserRepository->fetchAllUsersCount($objPagination);

        $Users['users']['data'] = $this->userRepository->findAllUsersByPageNumberByLimit($objPagination);

        $Users['users']['totalCount'] = $intTotalCount;

        return $this->respondWithData($Users);
    }
}