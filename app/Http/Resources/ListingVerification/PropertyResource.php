<?php

namespace App\Http\Resources\ListingVerification;

use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $documents = [];

        if ($this->titleDeed) {
            $documents[] = [
                "id" => $this->titleDeed->id,
                "title" => $this->titleDeed->title,
                "path" => $this->titleDeed->path,
                "selected" => true,
            ];
        }

        if ($this->ownerPassportCopy) {
            $documents[] = [
                "id" => $this->ownerPassportCopy->id,
                "title" => $this->ownerPassportCopy->title,
                "path" => $this->ownerPassportCopy->path,
                "selected" => true,
            ];
        }

        if ($this->signedListingForm) {
            $documents[] = [
                "id" => $this->signedListingForm->id,
                "title" => $this->signedListingForm->title,
                "path" => $this->signedListingForm->path,
                "selected" => true,
            ];
        }

        if ($this->otherDocuments) {
            foreach ($this->otherDocuments as $doc) {
                $documents[] = [
                    "id" => $doc->id,
                    "title" => $doc->title,
                    "path" => $doc->path,
                    "selected" => false,
                ];
            }
        }
        return [
            "id" => $this->id,
            "ref_no" => $this->ref_no,
            "category" => $this->category,
            "location" => $this->location,
            "title" => $this->title,
            "for" => $this->for,
            "price" => $this->price->price ?? 0,
            "documents" => $documents,
            "thumbnail" => $this->first_image->thumbnail ?? null,
        ];
    }
}
