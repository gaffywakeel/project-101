<?php

namespace App\Http\Resources;

use App\Models\UserDocument;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin UserDocument
 */
class UserDocumentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        if (is_null($this->resource)) {
            return parent::toArray($request);
        }

        return [
            'id' => $this->id,
            'status' => $this->status,
            'data' => $this->data,
            'user' => UserResource::make($this->whenLoaded('user')),
            'requirement' => RequiredDocumentResource::make($this->whenLoaded('requirement')),
            'created_at' => $this->created_at,
        ];
    }
}
