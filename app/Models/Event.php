<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Event
 * @package App\Models
 * @version March 16, 2021, 5:27 pm UTC
 *
 * @property int $user_id
 * @property string|\Carbon\Carbon $events_datetime
 */
class Event extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'events';
    

    protected $dates = ['deleted_at'];



    public $fillable = [
        'user_id',
        'events_datetime',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'events_datetime' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
