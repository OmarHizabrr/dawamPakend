<?php

namespace App\Repositories;

use App\Models\AttendanceLog;
use App\Repositories\BaseRepository;

/**
 * Class AttendanceLogRepository
 * @package App\Repositories
 * @version April 20, 2021, 10:04 am UTC
*/

class AttendanceLogRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user_id',
        'date',
        'attendance_time',
        'leave_time',
        'type',
        'canceled',
        'netPeriod'
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
        return AttendanceLog::class;
    }
}
