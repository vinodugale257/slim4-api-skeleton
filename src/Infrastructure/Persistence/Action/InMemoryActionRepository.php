<?php
declare (strict_types = 1);

namespace App\Infrastructure\Persistence\Action;

use App\Domain\Action\Action;
use App\Domain\Action\ActionRepository;
use Illuminate\Database\Capsule\Manager as DB;

class InMemoryActionRepository implements ActionRepository
{
    /**
     * @var Action[]
     */
    private $m_arrobjActions;

    /**
     * InMemoryActionRepository constructor.
     *
     * @param array|null $m_arrobjActions
     */
    public function __construct()
    {
        $this->m_arrobjActions = [];
    }

    /**
     * {@inheritdoc}
     */
    public function fetchAllPublicRoutes(): array
    {
        $strSql = 'SELECT
                    *
                FROM routes
                WHERE is_public IS TRUE
                ORDER BY id';

        $arrstdActions = DB::select($strSql);

        foreach ($arrstdActions as $stdAction) {
            $arrstrAction            = json_decode(json_encode($stdAction), true);
            $this->m_arrobjActions[] = new Action($arrstrAction);
        }

        return array_values($this->m_arrobjActions);
    }

}