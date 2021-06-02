<?php
declare(strict_types=1);

namespace App\Application\Middleware;

class SessionMiddleware extends AbstractMiddleware
{
    /**
     * {@inheritdoc}
     */
    public function beforeRouteExecution()
    {
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            session_start();
            $this->m_objRequest = $this->m_objRequest->withAttribute('session', $_SESSION);
        }

        return true;
    }
}
