<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
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

        return [
            'شماره'=>$this->id,
            'نام'=>$this->name,
            'نام نمایشی'=>$this->display_name,
            'محصولات'=>ProductResource::collection($this->whenLoaded('products')->load('images'))
        ];
    }
}
