<?php
declare (strict_types = 1);

namespace App\Domain\Action;

use App\Domain\BaseDomain;

class Action extends BaseDomain
{

    protected $table = 'public.actions';

    protected $fillable = [
        'id',
        'name',
        'is_active',
        'is_public',
    ];

    public function jsonSerialize()
    {
        return [
            'id'        => $this->getAttribute('id'),
            'name'      => $this->getAttribute('name'),
            'is_active' => $this->getAttribute('is_active'),
            'is_public' => $this->getAttribute('is_public'),
        ];
    }
}