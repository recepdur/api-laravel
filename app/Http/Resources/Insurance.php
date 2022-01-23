<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Insurance extends JsonResource
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
            'customer_id' => $this->customer_id, 
            'status' => $this->status,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'plate_no' => $this->plate_no,
            'car_register_no' => $this->car_register_no,
            'company' => $this->company,
            'policy_no' => $this->policy_no,
            'description' => $this->description,
            'commission_rate' => $this->commission_rate,
            'gross_price' => $this->gross_price,
            'net_price' => $this->net_price,
            'commission_price' => $this->commission_price,
            'created_at' => $this->created_at->format('m/d/Y'),
            'updated_at' => $this->updated_at->format('m/d/Y'),
        ];
    }
}