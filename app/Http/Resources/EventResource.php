<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
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
            'title' => $this->title,
            'urlTitle' => $this->urlTitle,
            'img' => $this->img,
            'type' => $this->type,
            'date' => $this->date,
            'location' => $this->location,
            'details' => $this->details,
            'isFeatured' => $this->isFeatured,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
