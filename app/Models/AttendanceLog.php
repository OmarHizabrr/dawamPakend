<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class AttendanceLog
 * @package App\Models
 * @version April 20, 2021, 10:04 am UTC
 *
 * @property integer $user_id
 * @property string $date
 * @property time $attendance_time
 * @property time $leave_time
 * @property integer $type
 * @property integer $canceled
 * @property string $netPeriod
 */
class AttendanceLog extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'attendancelogs';
    

    protected $dates = ['deleted_at'];



    public $fillable = [
        'user_id',
        'date',
        'attendance_time',
        'leave_time',
        'type',
        'canceled',
        'netPeriod'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'date' => 'date',
        'type' => 'integer',
        'canceled' => 'integer',
        'netPeriod' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
