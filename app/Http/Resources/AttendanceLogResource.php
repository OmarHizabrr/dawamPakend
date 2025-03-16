<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'date' => $this->date,
            'attendance_time' => $this->attendance_time,
            'leave_time' => $this->leave_time,
            'type' => $this->type,
            'canceled' => $this->canceled,
            'netPeriod' => $this->netPeriod,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
