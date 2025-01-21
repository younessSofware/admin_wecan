<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class HealthTipListResource extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = HealthTipResource::collection($this->collection);
        $pagination = new PaginationResource($this);
        return [
            'items' => $data,
            'pagination' => $pagination
        ];
    }
}
