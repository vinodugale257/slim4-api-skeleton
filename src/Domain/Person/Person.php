<?php
declare (strict_types = 1);

namespace App\Domain\Person;

use App\Domain\BaseDomain;

class Person extends BaseDomain
{
    protected $table = 'public.persons';

    protected $fillable = [
        'id',
        'first_name',
        'middle_name',
        'last_name',
        'phone_number',
        'mobile_number',
        'email_address',
        'birth_date',
        'pan_number',
        'adhaar_number',
        'profile_photo',
        'address',
        'city_name',
        'taluka_id',
        'district_id',
        'state_id',
        'pin_code',
        'created_by',
        'created_on',

    ];

    public function getFullName()
    {
        return $this->getAttribute('first_name') . ' ' . $this->getAttribute('last_name');
    }

    public function getDistributorFullName()
    {
        return $this->getAttribute('distributor_first_name') . ' ' . $this->getAttribute('distributor_last_name');
    }

    public function getFullAddress()
    {
        return $this->getAttribute('address') . ', ' . $this->getAttribute('taluka_name') . ', ' . $this->getAttribute('district_name') . ', ' . $this->getAttribute('state_name') . ', ' . $this->getAttribute('pin_code');
    }

    public function jsonSerialize()
    {
        return [
            'id'            => $this->getAttribute('id'),
            'fullName'      => $this->getFullName(),
            'first_name'    => $this->getAttribute('first_name'),
            'middle_name'   => $this->getAttribute('middle_name'),
            'last_name'     => $this->getAttribute('last_name'),
            'email_address' => $this->getAttribute('email_address'),
            'mobile_number' => $this->getAttribute('mobile_number'),
            'adhaar_number' => $this->getAttribute('adhaar_number'),
            'pan_number'    => $this->getAttribute('pan_number'),
            'address'       => $this->getAttribute('address'),
            'state_id'      => $this->getAttribute('state_id'),
            'district_id'   => $this->getAttribute('district_id'),
            'taluka_id'     => $this->getAttribute('taluka_id'),
            'pin_code'      => $this->getAttribute('pin_code'),
            'full_address'  => $this->getFullAddress(),
            'profile_photo' => $this->getAttribute('profile_photo'),
        ];
    }
}
