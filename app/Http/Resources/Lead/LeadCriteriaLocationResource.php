<?php

namespace App\Http\Resources\Lead;

use Illuminate\Http\Resources\Json\JsonResource;

class LeadCriteriaLocationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // dd($this->parentable_type);
        $model =$this->parentable_type;
        if ($model == 'App\Models\CommunityProperty') {
            $model = 'CommunityProperty';
        }
        if ($model == 'App\Models\Community') {
            $model = 'Community';
        }
        if ($model == 'App\Models\SubCommunity') {
            $model = 'SubCommunity';
        }


        if ($this->parentable) {
            return [
                'id' => $this->parentable->id ?? null,
                'name' => $this->parentable->name ?? null,
                'slug' => $this->parentable->slug ?? null,
                'description' => $this->parentable->description ?? null,
                'path_name' => $this->parentable->path_name ?? null,
                'latitude' => $this->parentable->latitude ?? null,
                'longitude' => $this->parentable->longitude ?? null,
                'alternate_name' => $this->parentable->alternate_name ?? null,
                'alternate_path_name' => $this->parentable->alternate_path_name ?? null,
                'type' => $model,
            ];
        }

        return [
            'id' => $this->parentable->id ?? null,
            'name' => $this->parentable->name ?? null,
            'slug' => $this->parentable->slug ?? null,
            'description' => $this->parentable->description ?? null,
            'path_name' => $this->parentable->path_name ?? null,
            'latitude' => $this->parentable->latitude ?? null,
            'longitude' => $this->parentable->longitude ?? null,
            'type' => $model,
        ];

        // dd($this->path_name);
        // if($this->parentable!=null){
        //     return new LocationResource($this->parentable);

        // }

        return parent::toArray($request);

    }
}
