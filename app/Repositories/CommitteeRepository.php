<?php

namespace App\Repositories;

use App\Models\Committee;
use App\Repositories\BaseRepository;

/**
 * Class CommitteeRepository
 * @package App\Repositories
 * @version March 16, 2021, 5:29 pm UTC
*/

class CommitteeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'description'
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
        return Committee::class;
    }
}
