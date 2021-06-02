<?php
declare (strict_types = 1);

namespace App\Domain\UserDepartment;

use App\Domain\BaseDomain;

class UserDepartment extends BaseDomain
{

    protected $table = 'public.user_departments';

    protected $fillable = [
        'id',
        'user_id',
        'department_id',
    ];

    public function jsonSerialize()
    {
        return [
            'id'            => $this->getAttribute('id'),
            'user_id'       => $this->getAttribute('user_id'),
            'department_id' => $this->getAttribute('department_id'),
        ];
    }
}