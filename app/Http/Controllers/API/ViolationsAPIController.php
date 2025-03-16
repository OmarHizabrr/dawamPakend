<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Violationtype;
use App\Violation;
use App\Alert;
use App\User;
use App\Category;
use App\AttendanceLog;
use DB;
use DateTime;
use DateTimeZone;
class ViolationsAPIController extends AppBaseController
{
    function getViolationsTypes(){
        $types=Violationtype::select('id as value','vio_name as label')->orderBy('vio_name')->get();
        return $types;
    }
    function add_violations(Request $request){
        $user_id=$request->input('user_id');
        
            date_default_timezone_set("Asia/Aden");
 
          $done_by=$request->input('done_by');
          $done=User::find($done_by); 
          $user=User::where('user_id',$user_id)->get()[0];
          $cat=Category::find($user->category_id);
          
          if($done->role_id=="1" || $done_by==$cat->user_id){
        
        if($request->input('vio_id')>0){
             $vt=Violation::find($request->input('vio_id'));  
        }
       else{
            $vt=new Violation;
       }
          $vt->user_id=$user_id;
          $vt->violationtype_id=$request->input('vio_type');
          $vio_date = new DateTime( $request->input('vio_date'), new DateTimeZone('UTC') );
          $vio_date->setTimezone( new DateTimeZone('Asia/Aden') );
          $vt->vio_date= $vio_date->format('Y-m-d');
          
          $vt->done_by=$done->role_id=="1"?2:3;
          
          $vt->note= $request->input('note')?$request->input('note'):'';
          $vt->money_discount=$request->input('discount')?$request->input('discount'):0;
          
          $vt->status=($request->input('status'))!=null?$request->input('status'):0;
          //$vt->status=$done->role_id=="1"?1:0;

          $vt->save();
          
          }
        
    }
    function getAllViolations($start,$end){
        $vios=DB::select('SELECT users.name as fullname,users.user_id as uid,users.job,categories.name as category,violations.id,violations.vio_date,violations.note,violations.money_discount,violationtypes.vio_name as vio_name,violations.status,violations.done_by,violations.violationtype_id as vio_id FROM users join categories on categories.id=users.category_id join violations on violations.user_id=users.user_id join violationtypes on violationtypes.id=violations.violationtype_id  and violations.vio_date BETWEEN ? and ?'
        ,[$start,$end]);
        return $vios;
    }
    function getCumViolations($start,$end){
        $violations=DB::select('SELECT users.name as fullname,users.user_name,users.user_id,users.job,violationtypes.vio_name,vio.vio_count from users left join (SELECT user_id,violationtype_id,count(violations.id) as vio_count FROM violations where violations.vio_date BETWEEN ? and ? and violations.status!="0" GROUP BY violations.user_id,violations.violationtype_id) as vio on users.user_id=vio.user_id JOIN violationtypes on vio.violationtype_id=violationtypes.id', [$start,$end]);
       return $violations;
    }
    
    function deleteViolation($vio_id){
         $vt=Violation::find($vio_id);
         $vt->delete();
    }
}
