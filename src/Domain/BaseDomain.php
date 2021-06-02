<?php
declare (strict_types = 1);

namespace App\Domain;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\Model;
use JsonSerializable;

class BaseDomain extends Model implements JsonSerializable
{
    public $timestamps = false;

    public function getSequenceName()
    {
        return $this->getTable() . '_id_seq';
    }

    public function fetchNextId()
    {
        $strSql = 'SELECT nextval(\'' . $this->getSequenceName() . '\'::text)  AS id';

        $arrstrResult = DB::select($strSql);

        return (isset($arrstrResult[0]->id)) ? $arrstrResult[0]->id : null;
    }

    public function fill(array $attributes)
    {
        return parent::fill(array_filter($attributes));
    }

    public static function beginTransaction()
    {
        self::getConnectionResolver()->connection()->beginTransaction();
    }

    public static function commit()
    {
        self::getConnectionResolver()->connection()->commit();
    }

    public static function rollBack()
    {
        self::getConnectionResolver()->connection()->rollBack();
    }

    public function update(array $attributes = [], array $options = [])
    {
        $intUserId = 1;
        $strsql    = "SET application_name = " . $intUserId . "";
        DB::select($strsql);
        return parent::update();
    }

    public function insert(array $options = [])
    {
        $intUserId = 1;
        $strsql    = "SET application_name = " . $intUserId . "";

        if (in_array("created_by", $this->fillable)) {
            $this->setAttribute('created_by', $intUserId);
            $this->setAttribute('created_on', 'NOW()');
        }
        DB::select($strsql);
        return parent::save();
    }

    public function delete(array $options = [])
    {
        if (in_array("deleted_by", $this->fillable)) {
            $intUserId = 1;
            $strsql    = "SET application_name = " . $intUserId . "";
            $this->setAttribute('deleted_by', $intUserId);
            $this->setAttribute('deleted_on', 'NOW()');
            DB::select($strsql);
            return parent::update();
        } else {
            return parent::delete();
        }
    }
}