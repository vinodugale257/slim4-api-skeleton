<?php
declare (strict_types = 1);

namespace App\Domain\Department;

use App\Domain\BaseDomain;

class Department extends BaseDomain
{

    protected $table = 'public.departments';

    protected $fillable = [
        'id',
        'name',
        'is_active',
    ];

    public function jsonSerialize()
    {
        return [
            'id'        => $this->getAttribute('id'),
            'name'      => $this->getAttribute('name'),
            'is_active' => $this->getAttribute('is_active'),
        ];
    }
}