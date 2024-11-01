<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Musonza\Chat\Models\Participation;

/**
 * @mixin Participation
 */
class ChatParticipationResource extends JsonResource
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
            'settings' => $this->settings,
            'participant_id' => $this->messageable_id,
            'participant_type' => $this->messageable_type,
            'conversation_id' => $this->conversation_id,
            'participant' => ChatParticipantResource::make($this->whenLoaded('messageable')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
