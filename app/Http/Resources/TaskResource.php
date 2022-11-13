<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id"            => $this->id,
            "title"         => $this->title,
            "due_date"      => $this->due_date,
            "description"   => $this->description,
            "created_at"    => $this->created_at,
            "updated_at"    => $this->updated_at,
            'category'      => new CategoryResource($this->whenLoaded('category')), // use new for one output
            'files'         => FilesResource::collection($this->whenLoaded('files')), // use new for one output
            'comments'      => CommentResource::collection($this->whenLoaded('comments')), // use new for one output
        ];
    }
}
