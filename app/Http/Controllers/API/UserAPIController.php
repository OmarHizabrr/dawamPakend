<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\User;
use App\Phone;
use App\Qualification;
use App\Prework;
use App\Attachment;
use App\Allownce;
use App\Deduction;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;
use App\Setting;
use DB;


class UserAPIController extends AppBaseController
{
    public function login(Request $request){
      
      $user=User::with('category')->where('user_id',$request->get('user_id'))->first();
      
      $date = now()->toDateString();

      $existingSettings = Setting::all();

      $maxId = $existingSettings->max('id');

    
      if(Hash::check($request->get('password'), $user->password)){
          
    $result = DB::table('users')
        ->join('durations', 'users.durationtype_id', '=', 'durations.durationtype_id')
        ->selectRaw('durations.startTime, durations.endTime')
        ->where('users.user_id', $user->user_id)
        ->whereRaw('? between durations.startDate and durations.endDate', [$date])
        ->limit(1)
        ->first();

    if ($result) {
        $startTime = $result->startTime;
        $endTime = $result->endTime;

        $nextIdStart = $maxId + 1;
        $nextIdEnd = $maxId + 2;


        $newSettings = collect([
            new Setting(['id' => $nextIdStart, 'key' => 'duration_start', 'value' => $startTime]),
            new Setting(['id' => $nextIdEnd, 'key' => 'duration_end', 'value' => $endTime]),
        ]);

        $settings = $existingSettings->merge($newSettings);

    } else {
        $settings=$existingSettings;
    }
    
        return compact('user','settings');
      }
      else{
          return null;
      }
      
    }
    
    public function remove($id){
      
      $user=User::find($id);
      $user->delete();

    }    

    public function factorUser(Request $request)
    {
    $users = $request->input('users');
    
    foreach ($users as $user) {
        $nuser = User::find($user['id']);

        // Assuming you have a 'users' table with a column named $user['field']
        $columnName = $user['field'];
        $amount = $user['amount'];

        // Dynamically update the specified column with the 'amount' value
        $nuser->$columnName = $amount;
        $nuser->save();
    }

    return response()->json(['message' => 'Users updated successfully'], 200);
    }
    
    public function addUser(Request $request){
      
     // print_r($request->get('password')=='null');
      
    // if($request->get('id')!=null && $request->get('id')!="")
         $user=User::find($request->get('id')); 
     
    // else
    if(!$user)
      $user=new User;
      
    
      
      $user->name=$request->get('name');
      $user->sex=$request->get('sex');
      $user->birth_date=$request->get('birth_date')!=null? date('Y-m-d',strtotime($request->get('birth_date'))):$user->birth_date;
      $user->marital_status=$request->get('marital_status');
      $user->children_no=$request->get('children_no');
      $user->id_no=$request->get('id_no');
      $user->id_type=$request->get('id_type');
      $user->job=$request->get('job');
      $user->category_id=$request->get('category_id');
      $user->assignment_date=$request->get('assignment_date')!=null? date('Y-m-d',strtotime($request->get('assignment_date'))):$user->assignment_date;

      $user->salary=$request->get('salary');
      $user->salary_currency=$request->get('salary_currency');
      $user->transfer_value=$request->get('transfer_value');
      $user->symbiosis=$request->get('symbiosis');
      $user->address=$request->get('address');
      $user->email=$request->get('email');
      $user->user_id=$request->get('user_id');
      $user->fingerprint_type=$request->get('fingerprint_type');
      $user->user_name=$request->get('user_name');
      $user->role_id=$request->get('role_id');
      $user->control_panel=$request->get('control_panel');
     $user->general_manager=$request->get('general_manager');
      if( !($request->get('password')=='null' || $request->get('password')==""))
        $user->password=Hash::make($request->get('password'));
        
      $user->durationtype_id=$request->get('durationtype_id');
      $user->status=$request->get('status');
      $user->level=$request->get('level');
      $user->birth_place=$request->get('birth_place');
      $user->save();
     // print_r($user);
      $contacts=$request->input('contacts');
      $allownces=$request->input('allownces');
      $deductions=$request->input('deductions');

      $qualifications=$request->input('qualifications');
      $preworks=$request->input('preworks');
      $attachments=$request->input('attachments');
      
      if(is_array($contacts) && !empty($contacts))
      foreach($contacts as $contact){
          
        if(isset($contact['id'])){
         $phone=Phone::find($contact['id']); 
        }
        else{
           $phone=new Phone;
        }
        $phone->phone_type=$contact['phone_type'];
        $phone->phone_number=$contact['phone_number'];
        $phone->user_id= $user->id;
        $phone->save();
      }
//---------------------------------------------
    Allownce::where('user_id',$user->user_id)->delete();
    
    if(is_array($allownces) && !empty($allownces))
      foreach($allownces as $allownce){
        $allow=new Allownce;
        $allow->allownce_type=$allownce['allownce_type'];
        $allow->allownce_amount=$allownce['allownce_amount'];
        $allow->user_id= $user->user_id;
        $allow->save();
      }
//---------------------------------------------
    Deduction::where('user_id',$user->user_id)->delete();
    
    if(is_array($deductions) && !empty($deductions))
      foreach($deductions as $deduction){
        $deduct=new Deduction;
        $deduct->deduction_type=$deduction['deduction_type'];
        $deduct->deduction_amount=$deduction['deduction_amount'];
        $deduct->user_id= $user->user_id;
        $deduct->save();
      } 
//=======================================================
    if(is_array($qualifications) && !empty($qualifications))
    foreach($qualifications as $qualification){
          
        if(isset($qualification['id'])){
         $qual=Qualification::find($qualification['id']); 
        }
        else{
           $qual=new Qualification;
        }
        $qual->qual_name=$qualification['qual_name'];
        $qual->qual_year=date('Y',strtotime($qualification['qual_year']));
        $qual->qual_source=$qualification['qual_source'];
        $qual->user_id= $user->id;
        $qual->save();
      }
//=======================================================
      if(is_array($preworks) && !empty($preworks))
    foreach($preworks as $prework){
          
        if(isset($prework['id'])){
         $work=Prework::find($prework['id']); 
        }
        else{
           $work=new Prework;
        }
        $work->job_name=$prework['job_name'];
        $work->date_from=date('Y',strtotime($prework['work_period'][0]));
        $work->date_to=date('Y',strtotime($prework['work_period'][1]));
        $work->work_place=$prework['work_place'];
        $work->user_id= $user->id;
        $work->save();
      }
    


//=======================================================
         if(is_array($request->attachments) && !empty($request->attachments))
    foreach($request->attachments as $attachment){
          
         if(isset($attachment['id'])){
         $file=Attachment::find($attachment['id']); 
        }
        else{
           $file=new Attachment;
        }
         $file->attach_name=$attachment['attach_name'];
        
        if(gettype($attachment['attach_path'])!="string"){
            $path =$attachment['attach_path']->store('attachments','public');
            $file->attach_path= $path;
        } 

        $file->user_id= $user->id;
        $file->save();
      }    
     
    }
}
