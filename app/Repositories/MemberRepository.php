<?php

namespace App\Repositories;

use App\Models\Member;
use App\Repositories\BaseRepository;

/**
 * Class MemberRepository
 * @package App\Repositories
 * @version December 31, 2020, 3:42 am UTC
*/

class MemberRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'firstName',
        'lastName',
        'email',
        'memberType',
        'specialization',
        'department',
        'educationQualification',
        'state',
        'country',
        'city',
        'status',
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Member::class;
    }
}
