<?php

namespace App\Http\Resources;

use App\Models\RequiredDocument;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin RequiredDocument
 */
class RequiredDocumentResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
            'created_at' => $this->created_at,

            $this->mergeWhen($request->user()?->can('manage:settings'), [
                'pending_count' => $this->whenNotNull($this->pending_count),
                'approved_count' => $this->whenNotNull($this->approved_count),
                'rejected_count' => $this->whenNotNull($this->rejected_count),
            ]),
        ];
    }
}
