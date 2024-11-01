<?php

namespace App\Http\Resources;

use App\Models\CommerceCustomer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin CommerceCustomer
 *
 * @property CommerceCustomer $resource
 */
class CommerceCustomerResource extends JsonResource
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
            'email' => $this->email,

            $this->mergeWhen($request->user()?->can('view', $this->resource), [
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'deletable' => $request->user()?->can('delete', $this->resource),
                'transactions_count' => $this->whenCounted('transactions'),
            ]),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
