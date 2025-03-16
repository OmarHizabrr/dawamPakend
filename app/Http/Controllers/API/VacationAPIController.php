<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Vacationstype;
use App\VacationsUser;
use App\Vacation;
use App\Alert;
use App\User;
use App\Category;
use App\AttendanceLog;
use DB;
use DateTime;
use App\Type;
use DateTimeZone;
use App\Http\Controllers\AppBaseController;
class VacationAPIController extends AppBaseController
{
    function whatType($user_id){
        $user=User::find($user_id);
        $cat=Category::where('user_id',$user->user_id)->get();
        if(count($cat)){
        if( $cat[0]->user_id==$user->user_id && $cat[0]->parent_id=="")
           return 1;
        else  if( $cat[0]->user_id==$user->user_id && $cat[0]->parent_id!="")
           return 2;
        }
        else
        return 3;
    } 

    function getTasksType(){
        $types=Vacationstype::select('id as value','name as label')->get();
        return $types;
    }
    function getTasksAmount($user_id){
        $amounts=DB::select('SELECT uid,vid,round(sec_to_time((amount*60)-time_to_sec(vac_duration))) as rest FROM (SELECT `vacationstype_id`,SUM(`amount`) as amount,user_id FROM `vacations_users` GROUP BY user_id, `vacationstype_id`) vs JOIN cumvacations on cumvacations.uid=vs.user_id and cumvacations.vid=vs.vacationstype_id and uid=?',[$user_id]);
        
        return $amounts;
    }
    function getTaskStatment($user_id,$year){
        $records=DB::Select('select id,user_id,vacationstype_id as task_id,amount,type,note from vacations_users where YEAR(updated_at)=? and user_id=?',[$year,$user_id]);
        return $records;
    }
    function getTasksTypeRe(){
        $types=Vacationstype::select('id as value','name as label')->where('vac_report',1)->get();
        return $types;
    }

    function getTasks($id,$start,$end){
        $end=$end." 23:59:00";
        $tasks=DB::select('select vacations.id,vacations.created_at, vacationstypes.name,vacationstypes.id as vac_id,date_from,date_to, SUBSTRING_INDEX(getTaskdays(?,date_from,date_to), ",",1) as days, SUBSTRING_INDEX(getTaskdays(?,date_from,date_to), ",", -1) as period,description,if(hr_manager is Null,"في الانتظار",if(accepted=1,"معتمدة","مرفوضة")) as hr_manager,if(dept_manager is Null or dept_accepted is Null,"في الانتظار",if(dept_accepted=1,"معتمدة","مرفوضة")) as dept_manager,  if(gerenal_sec is Null,"في الانتظار",if(general_accepted=1,"معتمدة","مرفوضة")) as gerenal_sec from vacations left join vacationstypes on vacations.vacationtype_id = vacationstypes.id where user_id  = ? and date_from between ? and ? order by date_from desc'
        ,[$id,$id,$id,$start,$end]);
        
        return $tasks;
    }
    function getAllAcceptedTasks($start,$end){
        $tasks=DB::select('SELECT users.name as fullname,users.user_id as uid,users.job,categories.name as category,vacations.id,vacations.attendance_time as date_from,vacations.leave_time as date_to,vacations.notes as description,vacations.netPeriod,vacationstypes.id as vac_id,vacationstypes.name as vac_name FROM users join categories on categories.id=users.category_id join attendancelogs as vacations on vacations.user_id=users.user_id join vacationstypes on vacationstypes.id=vacations.type and vacations.date BETWEEN ? and ?'
        ,[$start,$end]);
        return $tasks;
    }
    function getCumTasks($start,$end){
         $end=$end." 23:59:00";
        $categories=Category::whereNull('parent_id')->orderBy('order')->get();
        $tasks=DB::select('select users.name as fullname,users.level,users.salary,users.user_id as uid,users.job,categories.name as category,vacationstypes.name as vac_name,sec_to_time(vac.vacation_duration) as vac_duration from (SELECT user_id,type,sum(time_to_sec(timediff(leave_time,attendance_time))) as vacation_duration FROM `attendancelogs` where type>0 and attendance_time BETWEEN ? and ? GROUP BY user_id,type) vac right join users on users.user_id=vac.user_id left join categories on users.category_id=categories.id left join vacationstypes on vacationstypes.id=vac.type and vacationstypes.att_report=1 where users.status=16 order by users.level,users.salary desc', [$start,$end]);
        return compact('tasks','categories');
    }
    
    function getAnnualyReport($vac_id,$year){

         $sql="";
        
        for($i=1;$i<=12;$i++){
            
            if($i==1)
                $start=($year-1)."-12-23";
            else
                $start=$year."-".($i-1)."-23";
                
            $end=$year."-".$i."-22";
            
          //  $sql .=",ifnull(SUM(CASE WHEN date between '".$start."' and '".$end."' THEN time_to_sec(timediff(leave_time,attendance_time)) END),0) as m".$i;
            $sql .= ",IFNULL(SUM(CASE 
                    WHEN date BETWEEN '".$start."' AND '".$end."' 
                        THEN 
                            CASE 
                                WHEN leave_time IS NOT NULL 
                                    AND attendance_time IS NOT NULL 
                                THEN time_to_sec(timediff(leave_time, attendance_time))
                                ELSE time_to_sec(netPeriod)
                            END
                    END), 0) AS m".$i;

        }

//echo $sql;

        $tasks=DB::select('SELECT users.user_id,    
    COALESCE(ann.m1, 0) as m1,
    COALESCE(ann.m2, 0) as m2,
    COALESCE(ann.m3, 0) as m3,
    COALESCE(ann.m4, 0) as m4,
    COALESCE(ann.m5, 0) as m5,
    COALESCE(ann.m6, 0) as m6,
    COALESCE(ann.m7, 0) as m7,
    COALESCE(ann.m8, 0) as m8,
    COALESCE(ann.m9, 0) as m9,
    COALESCE(ann.m10, 0) as m10,
    COALESCE(ann.m11, 0) as m11,
    COALESCE(ann.m12, 0) as m12,users.name,users.job,categories.name as category,vu.* from (SELECT user_id '.$sql.' from attendancelogs WHERE type=? GROUP BY user_id) ann right join users on ann.user_id=users.user_id left join categories on users.category_id=categories.id
        join (SELECT user_id,ifnull(SUM(CASE WHEN type=35 THEN amount END),0) as prev,ifnull(SUM(CASE WHEN type=36 THEN amount END),0) as curr,ifnull(SUM(CASE WHEN type=37 THEN amount END),0) as trans FROM vacations_users where vacations_users.vacationstype_id=? and created_at like "'.$year.'%" GROUP BY user_id) vu on vu.user_id=users.user_id ORDER by users.name
       ',[$vac_id,$vac_id]);
    
        return $tasks;
    }
    
    function getCumUserTasks($user_id,$start,$end){
        $tasks=DB::select('select vacationstypes.name as vac_name,sec_to_time(vac.vacation_duration) as vac_duration,vac.user_id from (SELECT user_id,type,sum(time_to_sec(timediff(leave_time,attendance_time))) as vacation_duration FROM `attendancelogs` where user_id=? and type>0 and attendance_time BETWEEN ? and ? GROUP BY user_id,type) vac join vacationstypes on vacationstypes.id=vac.type and vacationstypes.att_report=1', [$user_id,$start,$end]);
       return $tasks;
    }
    
    function discountTask(Request $request){
         $al=new AttendanceLog;
         $al->date=$request->input('discount_date');
         
        $al->netPeriod = sprintf('%02d:%02d', floor($request->input('amount') / 60), $request->input('amount') % 60);
     
         
         $al->type=$request->input('task_id');
         $al->user_id=$request->input('user_id');
        $al->notes=$request->input('note');

         $al->save();
    }
    
    function getRestTasks($year){
        $categories=Category::whereNull('parent_id')->orderBy('order')->get();
        
        $start=($year-1)."-12-23";
        $end=$year."-12-22";
        
        $tasks=DB::select('SELECT vacacc.*,vacacc.user_id uid,categories.name as category,ifnull(vaccons.vac_cons_m,0) as vac_cons_m,ifnull(vacacc.amount_m,0)-ifnull(vaccons.vac_cons_m,0) as rest FROM (Select users.name as fullname,users.job,users.category_id,vac_acc.* From (SELECT user_id,vacationstype_id as vid,SUM(`amount`) as amount_m FROM vacations_users where vacations_users.created_at BETWEEN ? and ? GROUP BY vacations_users.user_id,vacations_users.vacationstype_id) vac_acc right JOIN users on vac_acc.user_id=users.user_id) vacacc LEFT JOIN (SELECT user_id,type as vac_id,sum(time_to_sec(timediff(`attendancelogs`.`leave_time`,`attendancelogs`.`attendance_time`))/60) as vac_cons_m FROM attendancelogs WHERE date BETWEEN ? and ? and type>0 GROUP BY attendancelogs.user_id,attendancelogs.type) vaccons on vaccons.user_id=vacacc.user_id and vaccons.vac_id=vacacc.vid JOIN categories on vacacc.category_id=categories.id',[$start,$end,$start,$end]);
        
        $types=Type::select('id as value','name as label','parent_id as parent')->get();
      
       return compact('tasks','types','categories');
    }
    
    function getTasksRequests($id,$start,$end){
        
        $end=$end." 23:59:00";
    
        $user=User::where('user_id',$id)->get()[0];
        
        $types=Vacationstype::all();
        $type=0;
        if($user->role_id==1 || $user->general_manager){
             $tasks=DB::select('select vacationstypes.name as vactype,vacations.id as vid,vacations.created_at,users.name user,users.user_id user_id,users.job,categories.name category,vacationstypes.id vacation,date_from,date_to, SUBSTRING_INDEX(getTaskdays(vacations.user_id,date_from,date_to), ",",1) as days, SUBSTRING_INDEX(getTaskdays(vacations.user_id,date_from,date_to), ",", -1) as period,description, if(direct_manager is Null,"في الانتظار","معتمدة") as direct_manager, if(dept_manager is Null or dept_accepted is Null,"في الانتظار",if(dept_accepted=1,"معتمدة","مرفوضة")) as dept_manager, if(gerenal_sec is Null,"في الانتظار",if(general_accepted=1,"معتمدة","مرفوضة")) as gerenal_sec, if(hr_manager is Null ,"في الانتظار",if(accepted=1,"معتمدة","مرفوضة")) as hr_manager from vacations inner join vacationstypes inner join users join categories on vacations.vacationtype_id = vacationstypes.id and vacations.user_id=users.user_id and users.category_id=categories.id and vacations.date_from BETWEEN ? and ? order by vacations.date_from desc',[$start,$end]);
        }
        else if($this->whatType($user->id)==1 || $this->whatType($user->id)==2){
            $type=$this->whatType($user->id);
            
            $cat=Category::where('user_id',$user->user_id)->get()[0];

            $tasks=DB::select('select vacationstypes.name as vactype,vacations.id as vid,vacations.created_at,users.name user,users.user_id user_id,users.job,categories.name category,vacationstypes.id vacation,date_from,date_to, SUBSTRING_INDEX(getTaskdays(vacations.user_id,date_from,date_to), ",",1) as days, SUBSTRING_INDEX(getTaskdays(vacations.user_id,date_from,date_to), ",", -1) as period,description,if(direct_manager is Null,"في الانتظار","معتمدة") as direct_manager, if(dept_manager is Null or dept_accepted is Null,"في الانتظار",if(dept_accepted=1,"معتمدة","مرفوضة")) as dept_manager, if(gerenal_sec is Null,"في الانتظار",if(general_accepted=1,"معتمدة","مرفوضة")) as gerenal_sec, if(hr_manager is Null,"في الانتظار",if(accepted=1,"معتمدة","مرفوضة")) as hr_manager from vacations inner join vacationstypes inner join users join categories on vacations.vacationtype_id = vacationstypes.id and vacations.user_id=users.user_id and users.category_id=categories.id and vacations.user_id in (SELECT users.user_id FROM `users` join categories on users.category_id=categories.id and (category_id=? or categories.parent_id=?)) and  vacations.user_id !=? and vacations.date_from BETWEEN ? and ? order by vacations.date_from desc',[$cat->id,$cat->id,$user->user_id,$start,$end]);       
        }
        else{
            $type=$this->whatType($user->id);
            $tasks=DB::select('select vacationstypes.name as vactype,vacations.id as vid,vacations.created_at,users.name user,users.user_id user_id,users.job,categories.name category,vacationstypes.id vacation,date_from,date_to, SUBSTRING_INDEX(getTaskdays(vacations.user_id,date_from,date_to), ",",1) as days, SUBSTRING_INDEX(getTaskdays(vacations.user_id,date_from,date_to), ",", -1) as period,description,if(direct_manager is Null,"في الانتظار","معتمدة") as direct_manager, if(dept_manager is Null or dept_accepted is Null,"في الانتظار",if(dept_accepted=1,"معتمدة","مرفوضة")) as dept_manager, if(gerenal_sec is Null,"في الانتظار",if(general_accepted=1,"معتمدة","مرفوضة")) as gerenal_sec, if(hr_manager is Null,"في الانتظار",if(accepted=1,"معتمدة","مرفوضة")) as hr_manager from vacations inner join vacationstypes inner join users join categories on vacations.vacationtype_id = vacationstypes.id and vacations.user_id=users.user_id and users.category_id=categories.id and vacations.user_id = ? and vacations.date_from between ? and ? and vacations.user_id!=? order by vacations.date_from desc ',[$user->user_id,$start,$end,$user->user_id]);
        }
        
        if($user->role_id==1 || $user->general_manager)
             $count=DB::select('select count(hr_manager) as done,count(vacations.id) as total from vacations where date_from between ? and ?',[$start,$end]);
        else{
            $cat=Category::where('user_id',$user->user_id)->get();
            if(count($cat))
            $count=DB::select('select count(dept_manager) as done,count(vacations.id) as total from vacations where user_id <> ? and user_id in (select user_id from users where category_id=?) and date_from between ? and ?',[$user->user_id,$cat[0]->id,$start,$end]);
            else
             $count=array('count'=>0);
            
        }
        return compact('tasks','type','types','count');
    }

    function getDirectRes($user_id){
        $user=User::find($user_id);
        return $user->category->user_id;
    }
    function isDept($user_id){
        $user=User::find($user_id);
        return $user->category->parent_id=="";
    }
    function accept_task(Request $request){
        
        $vt=Vacation::find($request->input('vid'));      
        $vt->note=$request->input('note');
        $vt->description.=" | ".$request->input('note');
        $vt->vacationtype_id=$request->input('vacationtype_id');
        $vt->date_from=$request->input('date_from');
        $vt->date_to=$request->input('date_to');
        $vt->status=$request->input('status');
        $cleanedNotes=str_replace([' ', '|'], '', $vt->note);
        
    if(!(empty($cleanedNotes))){
        $nt=new Alert;
        $accepter=User::where('user_id',$request->input('user_id'))->get();
        $vacation=Vacationstype::find($vt->vacationtype_id);
        if($request->input('status')==1)
            $vac_status="قبول";
        else
            $vac_status="رفض";

        $alert_text="قام   ". $accepter[0]->user_name."بـ" . $vac_status." إجازتك : ".  $vacation->name . " للفترة " . $vt->date_from . "-". $vt->date_to;
        $alert_text.= " مع الملاحظات التالية: ".$vt->note;
        
        $nt->text=$alert_text;
        $nt->all=0;
        $nt->user_id= $vt->user_id;

        $nt->status=0;
        $nt->save();
        }
        
        $user=User::where('user_id',$request->input('user_id'))->get()[0];
        if($user && $user->general_manager==1){
            date_default_timezone_set("Asia/Aden");
            $vt->gerenal_sec=date("Y-m-d H:i:s");
            $vt->general_accepted=$request->input('status');
        }
        else
        if($request->input('accepter')==0){
            $vt->accepted=$request->input('status');
  
            date_default_timezone_set("Asia/Aden");
            $vt->hr_manager=date("Y-m-d H:i:s");
            $start=strtotime($vt->date_from);
            $end=strtotime($vt->date_to);
            $count=floor(abs($end-$start)/(86400))+1;
            
            $al=AttendanceLog::where('refrence_id',$vt->id);
            
            $al->delete();
            
        for($i=0;$i<$count && $request->input('status')==1;$i++){
        
            $dates=DB::select("SELECT COUNT(*) as count from dates WHERE date=? and type=1",[date("Y-m-d",$start+(86400*$i))]);
            
            if($dates[0]->count==0){
            $al=new AttendanceLog;
            
            $al->notes= $vt->description;
            $al->date=date("Y-m-d",$start+(86400*$i));
            if($count==1){
            $al->attendance_time = $vt->date_from;
            $al->leave_time = $vt->date_to;
            }
            else{   
                $adate=date('Y-m-d H:i:00',$start+(86400*$i));
                $ldate=date('Y-m-d H:i:00',$end-(86400*($count-$i-1)));

                $al->attendance_time =  $adate;
                $al->leave_time =  $ldate;
            }
            $al->type= $request->input('vacationtype_id');
            $al->user_id=$vt->user_id;
            $al->refrence_id=$vt->id;
           $al->save();
            }
           
            }
            
        }
        else if($request->input('accepter')==1){
            date_default_timezone_set("Asia/Aden");
            $vt->dept_manager=date("Y-m-d H:i:s");
            $vt->dept_accepted=$request->input('status');
        }
        else if($request->input('accepter')==2){
            date_default_timezone_set("Asia/Aden");
            $vt->direct_manager=date("Y-m-d H:i:s");
        }
        
            
        
        $vt->save();
       
    }
    function add_accepted_tasks(Request $request){
          $user_id=$request->input('user_id');
          $tasks=$request->input('tasks');
          date_default_timezone_set("Asia/Aden");
          foreach($tasks as $task){
            $vt=new Vacation;
            $vt->user_id=$user_id;
            $vt->vacationtype_id=$task['task_type'];
            $vt->hr_manager=date("Y-m-d H:i:s");
            $vt->accepted=1;
            $start_date = new DateTime( $task['date_range'][0], new DateTimeZone('UTC') );
            $start_date->setTimezone( new DateTimeZone('Asia/Aden') );
            $vt->date_from= $start_date->format('Y-m-d H:i:00');
            $end_date = new DateTime( $task['date_range'][1], new DateTimeZone('UTC') );
            $end_date->setTimezone( new DateTimeZone('Asia/Aden') );
            $vt->date_to= $end_date->format('Y-m-d H:i:00');
            if(isset($task['description']))
            $vt->description=$task['description'];
            $vt->save();
            //record vacation in attendancelogs
            $start=strtotime($start_date->format('Y-m-d H:i:00'));
            $end=strtotime($end_date->format('Y-m-d H:i:00'));
            $count=floor(abs($end-$start)/(86400))+1;
            for($i=0;$i<$count;$i++){
                $al=new AttendanceLog;
                $al->date=date("Y-m-d",$start+(86400*$i));
                if($count==1){
                    $al->attendance_time = $start_date->format('Y-m-d H:i:00');
                    $al->leave_time =  $end_date->format('Y-m-d H:i:00');
                }
                else{   
                    $adate=date('Y-m-d H:i:00',$start+(86400*$i));
                    $ldate=date('Y-m-d H:i:00',$end-(86400*($count-$i-1)));
                    $al->attendance_time =  $adate;
                    $al->leave_time =  $ldate;
                }
                $al->type= $task['task_type'];
                if(isset($task['description']))
                    $al->notes=$task['description'];
                $al->user_id=$user_id;
                $al->refrence_id=$vt->id;
                $al->save();
               
            }

          }
      }
      
    function add_balance_tasks(Request $request){
        
    foreach($request->input('tasks') as $vac){
        
        $vid=array_key_exists('id',$vac)?$vac['id']:null;
        $vu=VacationsUser::find($vid);
        
        if(!$vu)
            $vu= new VacationsUser;
        
        $vu->user_id= $vac['user_id'];
        $vu->vacationstype_id= $vac['task_id'];
         $vu->type= $vac['type'];
        $vu->amount= $vac['amount'];
        $vu->note=array_key_exists('note',$vac)?$vac['note']:null;
        $vu->save();
    }
        
    }
    
    function add_task(Request $request){
            $count=DB::select("SELECT count(*) count FROM attendancelogs WHERE user_id=? and (DATE_FORMAT(?, '%Y-%m-%d %H:%i:00') < DATE_FORMAT(leave_time, '%Y-%m-%d %H:%i:00')) AND DATE_FORMAT(?, '%Y-%m-%d %H:%i:00')> DATE_FORMAT(attendance_time, '%Y-%m-%d %H:%i:00')",[$request->input('user_id'),$request->input('startDate'),$request->input('endDate')])[0]->count;
            
            $countV=DB::select("SELECT count(*) count FROM vacations WHERE user_id=? and (DATE_FORMAT(?, '%Y-%m-%d %H:%i:00') < DATE_FORMAT(date_to, '%Y-%m-%d %H:%i:00')) AND DATE_FORMAT(?, '%Y-%m-%d %H:%i:00')> DATE_FORMAT(date_from, '%Y-%m-%d %H:%i:00')",[$request->input('user_id'),$request->input('startDate'),$request->input('endDate')])[0]->count;

            if($count){
                return response()->json(['message' => 'هناك تعارض مع وقت دوامك أو إجازة أخرى معتمدة '], 409);
            }
            else if( $countV){
                return response()->json(['message' => 'لديك إجازة -في الانتظار- بنفس هذا الوقت'], 409);
            }
            else if($request->input('type')==null){
                return response()->json(['message' => 'يرجى تحديد نوع الإحازة'], 409);
            }
            else
            {
            $vt=new Vacation;
            $vt->user_id=$request->input('user_id');
            $vt->vacationtype_id=$request->input('type');
            $vt->date_from=$request->input('startDate');
            $vt->date_to=$request->input('endDate');   
            $vt->description=$request->input('note');
            $vt->save(); 
    
       
            $user=User::where('user_id', $vt->user_id)->get()[0];
        
        
            if($this->getDirectRes($user->id)==$user->user_id){
            
           foreach(User::where('role_id',1)->get() as $usera){ 
            $nt=new Alert;  
            $vid=$request->input('type');
            $vacation=Vacationstype::find($vid);

            $nt->text="تقدم ". $user->user_name ." بطلب ".  $vacation->name . " للفترة " . $vacation->date_from . "-". $vacation->date_to;
            $nt->all=0;
            $nt->user_id= $usera->user_id;
            $nt->status=0;
            $nt->save();
           }
            
        }
            else{
            
            if(User::where('user_id', $this->getDirectRes($user->id))->get()[0]){  
            $nt=new Alert;
            $vid=$request->input('type');
            $vacation=Vacationstype::find($vid);

            $nt->text="تقدم ". $user->user_name ." بطلب ".  $vacation->name . " للفترة " . $vacation->date_from . "-". $vacation->date_to;
            $nt->all=0;
            $nt->user_id=User::where('user_id', $this->getDirectRes($user->id))->get()[0]->user_id;
            $nt->status=0;
            $nt->save();
            }
        }

            return response()->json(['data' => $vt], 200);

            }
}
    function add_all_tasks(Request $request){
        $users=$request->input('users');
        $task=$request->input('task_type');
        $des=$request->input('desc');
        date_default_timezone_set("Asia/Aden");
        $start=$request->input('start');
        $end=$request->input('end');
       foreach($users as $user){
           //echo ($user);
         $user=str_replace('"','',$user);
          if(isset($user)){
            $vt=new Vacation;
            $vt->user_id=$user;
            $vt->vacationtype_id=$task;
            $vt->hr_manager=date("Y-m-d H:i:s");
            $vt->accepted=1;
            $vt->date_from= $start;
            $vt->date_to= $end;
            if(isset($des))
            $vt->description=$des;
           $vt->save();
            //record vacation in attendancelogs
            $starta=strtotime($start);
            $enda=strtotime($end);
            $count=floor(abs($enda-$starta)/(86400))+1;
            for($i=0;$i<$count;$i++){
                $al=new AttendanceLog;
                $al->date=date("Y-m-d",$starta+(86400*$i));
                if($count==1){
                    $al->attendance_time = $start;
                    $al->leave_time =  $end;
                }
                else{   
                    $adate=date('Y-m-d H:i:00',$starta+(86400*$i));
                    $ldate=date('Y-m-d H:i:00',$enda-(86400*($count-$i-1)));
                    $al->attendance_time =  $adate;
                    $al->leave_time =  $ldate;
                }
                $al->type= $task;
                if(isset($des))
                $al->notes=$des;
                $al->user_id=(int) $user;
               // var_dump($al);
               $al->refrence_id=$vt->id;
              $al->save();
                
            }
        }
       }
        
      }
    function deleteTask($id){
          $vt=Vacation::find($id);
          $al=AttendanceLog::where('refrence_id',$id);
          $al->delete();
          $vt->delete();
      }
    function updateTask(Request $request){
        
            $id=$request->input('id');
            $type=$request->input('type');
            $datefrom=$request->input('startDate');
            $dateto=$request->input('endDate');
            $note=$request->input('note');
          DB::table('vacations')
            ->where('id',$id)
            ->update(['vacationtype_id' => $type,'date_from'=>$datefrom,'date_to'=>$dateto,'description'=>$note]);
             $vt=Vacation::find($id);
             $al=AttendanceLog::where('refrence_id',$vt->id)->get();
            
           
         if(!$al->isEmpty()){
            $al->delete();
            $starta=strtotime($datefrom);
            $enda=strtotime($dateto);
            $count=floor(abs($enda-$starta)/(86400))+1;
            for($i=0;$i<$count;$i++){
                $al=new AttendanceLog;
                $al->date=date("Y-m-d",$starta+(86400*$i));
                if($count==1){
                    $al->attendance_time = $datefrom;
                    $al->leave_time =  $dateto;
                }
                else{   
                    $adate=date('Y-m-d H:i:00',$starta+(86400*$i));
                    $ldate=date('Y-m-d H:i:00',$enda-(86400*($count-$i-1)));
                    $al->attendance_time =  $adate;
                    $al->leave_time =  $ldate;
                }
                $al->type= $type;
                $al->notes=$vt->description;
                $al->user_id=$vt->user_id;
                $al->refrence_id=$vt->id;
                $al->save();
            }
           }
      }
        
}