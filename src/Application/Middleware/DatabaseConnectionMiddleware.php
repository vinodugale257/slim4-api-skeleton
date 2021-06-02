<?php

namespace App\Application\Middleware;

class DatabaseConnectionMiddleware extends AbstractMiddleware
{
    protected function beforeRouteExecution()
    {
        if ( !in_array( $this->m_strRouteName, ['welcome'] ) ) {
            $this->m_objContainer->get('db');
        }
    }
}
