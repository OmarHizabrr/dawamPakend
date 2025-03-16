<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Member
 * @package App\Models
 * @version December 31, 2020, 3:42 am UTC
 *
 * @property string $firstName
 * @property string $lastName
 * @property string $email
 * @property integer $memberType
 * @property string $specialization
 * @property string $department
 * @property string $educationQualification
 * @property string $state
 * @property string $country
 * @property string $city
 * @property string $status
 */
class Member extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'members';
    

    protected $dates = ['deleted_at'];



    public $fillable = [
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
        'status'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'firstName' => 'string',
        'lastName' => 'string',
        'email' => 'string',
        'memberType' => 'integer',
        'specialization' => 'string',
        'department' => 'string',
        'educationQualification' => 'string',
        'state' => 'string',
        'country' => 'string',
        'city' => 'string',
        'status'=>'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    
}
