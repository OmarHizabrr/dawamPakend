<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Post
 * @package App\Models
 * @version March 16, 2021, 5:33 pm UTC
 *
 * @property integer $category_id
 * @property string $title
 * @property string $seo_title
 * @property string $excerpt
 * @property string $body
 * @property string $image
 * @property string $slug
 * @property string $meta_description
 * @property string $meta_keywords
 * @property enum('PUBLISHED' $status
 * @property integer $featured
 */
class Post extends Model
{
    //use SoftDeletes;

    use HasFactory;

    public $table = 'posts';
    

    protected $dates = ['deleted_at'];



    public $fillable = [
        'category_id',
        'title',
        'seo_title',
        'excerpt',
        'body',
        'image',
        'slug',
        'meta_description',
        'meta_keywords',
        'status',
        'featured'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'category_id' => 'integer',
        'title' => 'string',
        'seo_title' => 'string',
        'excerpt' => 'string',
        'body' => 'string',
        'image' => 'string',
        'slug' => 'string',
        'meta_description' => 'string',
        'meta_keywords' => 'string',
        'featured' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
