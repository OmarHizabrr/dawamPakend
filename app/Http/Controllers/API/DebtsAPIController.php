<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\User;
use App\Debt;
use App\Account;
use App\Category;
use DB;
class DebtsAPIController extends AppBaseController
{
    function calcLongDebts($start,$end){
        
        $debts=DB::select('SELECT accounts.id as id,accounts.debt_date,users.user_id as user_id,users.name as name,users.salary,users.job ,categories.name as category,amount,accounts.note from accounts join users join categories on accounts.user_id=users.user_id and accounts.owner=1 and users.category_id=categories.id and accounts.debt_date between ? and ? order by users.name desc',[$start,$end]);
        $categories=Category::whereNull('parent_id')->orderBy('order')->get();

        return compact('debts','categories');
        
    }
    
    function updateAmount($id,$newValue) {
        $in=Debt::find($id);
        $in->amount=$newValue;
        $in->save();
        return $in;
    }
    function deleteDebt($id){
        $in=Debt::find($id);
        $in->delete();
    }
    function addDebts(Request $request){
        $debts=$request->input('debts');
        $debt_date= date('Y-m-d',strtotime($request->get('debt_date')));
       // print_r($request->get('debt_date'));
        foreach($debts as $debt){
           $d=new Debt;
           $d->user_id=$debt['user_id'];
           $d->amount=$debt['debt_value'];
           $d->debt_date=$debt_date;
           $d->type='نصف شهرية';
           $d->save();
        }
    }
    
function addShortDebts(Request $request){
        $debts=$request->input('debts');
        $debt_date= date('Y-m-d',strtotime($request->get('debt_date')));
       
        foreach($debts as $debt){
           $d=new Account;
           $d->user_id=$debt['user_id'];
           $d->amount=$debt['debt_value'];
           $d->owner=0;
           $d->debt_date=$debt_date;
           if(isset($debt['note']))
            $d->note=$debt['note'];
           $d->save();
           $d=new Account;
           $d->user_id=$debt['user_id'];
           $d->amount=$debt['debt_value'];
           $d->owner=1;
           $d->debt_date=$debt_date;
           if(isset($debt['note']))
            $d->note=$debt['note'];
           $d->save();
        }
    }

function addLongDebt(Request $request){
           $d=new Account;
           $d->user_id=$request->input('user_id');
           $d->amount=$request->input('amount');
           $d->owner=0;
           $d->debt_date=$request->input('debt_date');
           $d->note=$request->input('note');
           $d->save(); 
           return $d;
    }

function updateLongDebt(Request $request){
    
           $acc=Account::find($request->input('id'));
           
           $acc->user_id=$request->input('user_id');
           $acc->amount=$request->input('amount');
           $acc->owner=1;
           $acc->debt_date=$request->input('debt_date');
           $acc->note=$request->input('note');
           $acc->save();
           
           return $acc;
    }
 
 function updateDebt(Request $request){
    

           $d=Debt::find($request->input('id'));
           $d->user_id=$request->input('user_id');
           $d->amount=$request->input('amount');
           $d->debt_date=date('Y-m-d',strtotime($request->get('debt_date')));
           $d->type='نصف شهرية';
           $d->save();
           
           return $d;
    }
    
function deleteLongDebt($id){
    $acc=Account::find($id);
    $acc->delete();
}   

function payDebt(Request $request){
        $d=new Account;
        $d->user_id=$request->input('user_id');
        $d->amount=$request->input('amount');
        $d->owner=1;
        $d->debt_date=$request->input('debt_date');
        $d->note=$request->input('note');
        $d->save(); 
        return $d;
 }
function getUsersDebts(){
     //   $users=User::select('user_id as user_id','salary as debt_value')->where('name','<>','Admin')->orderBy('name')->get();
       $users=DB::select("SELECT user_id,round(salary/2,-3) as debt_value FROM users WHERE user_id is not NULL and name <> 'Admin' order by name");
        return $users;
 }
 function getUsersLongDebts(){
       $users=DB::select("SELECT user_id FROM users WHERE user_id is not NULL and is_hidden <> 1 and status=16 order by name");
        return $users;
 }
}
