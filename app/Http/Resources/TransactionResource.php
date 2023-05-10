<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "transaction_category_name" => $this->transaction_category->name,
            "transaction_category_id" => $this->transaction_category->id,
            "transaction_category_icon" => $this->transaction_category->icon,
            "date" => $this->date,
            "amount" => $this->amount,
            "note" => $this->note,
            "type" => $this->type,
        ];
    }
}
