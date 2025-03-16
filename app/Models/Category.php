<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Category
 * @package App\Models
 * @version March 16, 2021, 5:34 pm UTC
 *
 * @property  $parent_id
 * @property integer $order
 * @property string $name
 * @property string $slug
 */
class Category extends Model
{
    //use SoftDeletes;

    use HasFactory;

    public $table = 'categories';
    

    protected $dates = ['deleted_at'];



    public $fillable = [
        'parent_id',
        'order',
        'name',
        'slug'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'order' => 'integer',
        'name' => 'string',
        'slug' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
