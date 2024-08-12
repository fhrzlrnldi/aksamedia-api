<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\DivisionResource;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'image' => $this->image, 
            'name' => $this->name,
            'phone' => $this->phone,
            'division' => new DivisionResource($this->whenLoaded('division')), // Menggunakan DivisionResource untuk data relasi
            'position' => $this->position,
        ];
    }
}
