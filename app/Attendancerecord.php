<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
class Attendancerecord extends Model
{
    use HasFactory;
    protected $table='attendancerecords';
    public function user()
    {
        return $this->belongsTo(User::class,'user_id','user_id');
    }
  
}
