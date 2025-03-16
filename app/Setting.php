<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Events\SettingUpdated;

class Setting extends Model
{
    protected $table = 'settings';

    protected $guarded = [];

    public $timestamps = false;

    protected $dispatchesEvents = [
        'updating' => SettingUpdated::class,
    ];
}
