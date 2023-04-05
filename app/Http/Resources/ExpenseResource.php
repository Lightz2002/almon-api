<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
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
            "expense_category_name" => $this->expense_category->name,
            "expense_category_id" => $this->expense_category->id,
            "expense_category_icon" => $this->expense_category->icon,
            "date" => $this->date,
            "amount" => $this->amount,
            "note" => $this->note,
        ];
    }
}
