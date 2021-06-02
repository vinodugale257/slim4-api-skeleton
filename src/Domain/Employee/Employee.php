<?php
declare (strict_types = 1);

namespace App\Domain\Employee;

use App\Domain\BaseDomain;

class Employee extends BaseDomain
{
    protected $table = 'public.employees';

    protected $fillable = [
        'id',
        'person_id',
        'designation_id',
        'hired_on',
        'resignation_date',
        'termination_date',
        'created_by',
        'created_on',

    ];

    public function jsonSerialize()
    {
        return [
            'id'               => $this->getAttribute('id'),
            'person_id'        => $this->getAttribute('person_id'),
            'designation_id'   => $this->getAttribute('designation_id'),
            'hired_on'         => $this->getAttribute('hired_on'),
            'resignation_date' => $this->getAttribute('resignation_date'),
            'termination_date' => $this->getAttribute('termination_date'),
        ];
    }
}