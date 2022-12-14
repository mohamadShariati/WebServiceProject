<?php

namespace App\Http\Resources\V1;

use App\Http\Resources\V1\ProductResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);

        return[
            'id'=>$this->id,
            'parent_id'=>$this->parent_id,
            'name'=>$this->name,
            'description'=>$this->description,
            'children'=>CategoryResource::collection($this->whenLoaded('children')),
            'parent'=>new CategoryResource($this->whenLoaded('parent')),
            'products'=>CategoryResource::collection($this->whenLoaded('products')),
            'categoryProducts'=>ProductResource::collection($this->whenLoaded('CategoryProducts')->load('images'))
        ];
    }
}
