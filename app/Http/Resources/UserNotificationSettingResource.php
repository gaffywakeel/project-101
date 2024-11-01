<?php

namespace App\Http\Resources;

use App\Models\UserNotificationSetting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin UserNotificationSetting
 */
class UserNotificationSettingResource extends JsonResource
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
            'title' => $this->title,
            'name' => $this->name,
            'email' => $this->when(!$this->isDisabled('email'), $this->email),
            'database' => $this->when(!$this->isDisabled('database'), $this->database),
            'sms' => $this->when(!$this->isDisabled('sms'), $this->sms),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
