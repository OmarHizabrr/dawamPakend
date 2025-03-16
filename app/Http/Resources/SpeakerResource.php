<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SpeakerResource extends JsonResource
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
            'name' => $this->name,
            'title' => $this->title,
            'academicRank' => $this->academicRank,
            'field' => $this->field,
            'affiliation' => $this->affiliation,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
