<?php
declare (strict_types = 1);

namespace App\Domain\Action;

interface ActionRepository
{
    /**
     * @return Action[]
     */
    public function fetchAllPublicRoutes(): array;

}