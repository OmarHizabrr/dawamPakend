<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Speaker
 * @package App\Models
 * @version March 15, 2021, 6:16 am UTC
 *
 * @property string $name
 * @property string $title
 * @property string $academicRank
 * @property string $field
 * @property string $affiliation
 */
class Speaker extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'speakers';
    

    protected $dates = ['deleted_at'];



    public $fillable = [
        'name',
        'title',
        'academicRank',
        'field',
        'affiliation'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'title' => 'string',
        'academicRank' => 'string',
        'field' => 'string',
        'affiliation' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
