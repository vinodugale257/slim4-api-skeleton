<?php
declare (strict_types = 1);

namespace App\Domain\UserType;

use App\Domain\BaseDomain;

class UserType extends BaseDomain
{
    const ADMIN    = 1;
    const EMPLOYEE = 2;
    const PATIENT  = 3;

    protected $table = 'public.user_types';

    protected $fillable = [
        'id',
        'name',
        'description',
        'order_num',
    ];

    public function jsonSerialize()
    {
        return [
            'id'          => $this->getAttribute('id'),
            'name'        => $this->getAttribute('name'),
            'description' => $this->getAttribute('description'),
            'orderNum'    => $this->getAttribute('order_num'),
        ];
    }
}