<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseAllocationResource extends JsonResource
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
      'id' => $this->id,
      'name' => $this->transaction_category->name,
      'icon' => $this->transaction_category->icon,
      'amount' => $this->amount,
      'color' => $this->color,
      'percentage' => $this->percentage
    ];
  }
}
