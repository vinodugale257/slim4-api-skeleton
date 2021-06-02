<?php

namespace Library;

class Database
{
    private $m_objConnection;

    public function connect($arrmixSettings)
    {
        $objCapsule = new \Illuminate\Database\Capsule\Manager;
        $objCapsule->addConnection($arrmixSettings);
        $objCapsule->setAsGlobal();
        $objCapsule->bootEloquent();
        $this->m_objConnection = $objCapsule;
    }

    public function getConnection()
    {
        return $this->m_objConnection;
    }
}
