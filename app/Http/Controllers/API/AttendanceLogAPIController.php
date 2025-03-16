<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAttendanceLogAPIRequest;
use App\Http\Requests\API\UpdateAttendanceLogAPIRequest;
use App\AttendanceLog;
use App\Repositories\AttendanceLogRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\AttendanceLogResource;
use Response;

/**
 * Class AttendanceLogController
 * @package App\Http\Controllers\API
 */

class AttendanceLogAPIController extends AppBaseController
{
    /** @var  AttendanceLogRepository */
    private $attendanceLogRepository;

    public function __construct(AttendanceLogRepository $attendanceLogRepo)
    {
      
    }

    /**
     * Display a listing of the AttendanceLog.
     * GET|HEAD /attendanceLogs
     *
     * @param Request $request
     * @return Response
     */
    public function index($user_id,$start="",$end="")
    {
      $at=AttendanceLog::where('user_id',$user_id)->get();
      return $at;
    }

    public function summary($user_id){
        $attendanceLogs = DB::table('attendancelogs')->select('user_id','date')->groupBy('user_id','date')->selectRaw('sum(netPeriod)');
        //SELECT user_id,date,SEC_TO_TIME(SUM(TIME_TO_SEC(`netPeriod`))) FROM `attendancelogs` GROUP BY `user_id`,`date`
        return $this->sendResponse(AttendanceLogResource::collection($attendanceLogs), 'Attendance Logs retrieved successfully');
        }
    /**
     * Store a newly created AttendanceLog in storage.
     * POST /attendanceLogs
     *
     * @param CreateAttendanceLogAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateAttendanceLogAPIRequest $request)
    {
      
    }

    /**
     * Display the specified AttendanceLog.
     * GET|HEAD /attendanceLogs/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        
    }

    /**
     * Update the specified AttendanceLog in storage.
     * PUT/PATCH /attendanceLogs/{id}
     *
     * @param int $id
     * @param UpdateAttendanceLogAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAttendanceLogAPIRequest $request)
    {
        
    }

    /**
     * Remove the specified AttendanceLog from storage.
     * DELETE /attendanceLogs/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {

    }
}
/*
DECLARE result time;

Set result=SELECT `startTime` FROM `durations` WHERE start < allowedstart AND currentDate BETWEEN `startDate` AND `endDate`;

 IF(!result)
 Set result=start;
 END IF;
 
 RETURN result;
 */
/*
SELECT user_id,DAYNAME(date),date,MIN(attendance_time) as attendance_time,MAX(`leave_time`) as leave_time,SEC_TO_TIME(SUM(TIME_TO_SEC(timediff(calceTime(leave_time,date),calcTime(attendance_time,date))))) as netDawam,SEC_TO_TIME(TIME_TO_SEC(getDuration(`date`))-(SUM(TIME_TO_SEC(timediff(calceTime(leave_time,date),calcTime(attendance_time,date)))))) as lateTime, getTBouns(`leave_time`,date) AS bonusTime, round((SUM(TIME_TO_SEC(timediff(calceTime(leave_time,date),calcTime(attendance_time,date))))/60)*getMinutePrice(2,date),0) as dialySalary FROM `attendancelogs` GROUP BY `user_id`,`date`
SELECT user_id,DAYNAME(date),date,MIN(attendance_time) as attendance_time,MAX(`leave_time`) as leave_time,SEC_TO_TIME(SUM(TIME_TO_SEC(timediff(calceTime(leave_time,date,user_id),calcTime(attendance_time,date,user_id))))) as netDawam,SEC_TO_TIME(TIME_TO_SEC(getDuration(`date`,user_id))-(SUM(TIME_TO_SEC(timediff(calceTime(leave_time,date,user_id),calcTime(attendance_time,date,user_id)))))) as lateTime, getTBouns(`leave_time`,date,user_id) AS bonusTime, round((SUM(TIME_TO_SEC(timediff(calceTime(leave_time,date,user_id),calcTime(attendance_time,date,user_id))))/60)*getMinutePrice(user_id,date),0) as dialySalary FROM `attendancelogs` GROUP BY `user_id`,`date`
*/