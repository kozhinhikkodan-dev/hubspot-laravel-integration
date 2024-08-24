<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
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
            'firstname' => $this->firstname ?? '',
            'lastname' => $this->lastname ?? '',
            'email' => $this->email ?? '',
            'mobilephone' => $this->mobilephone ?? '',
            'created_at' => (new \DateTime($this->created_at))->setTimezone(new \DateTimeZone(config('app.timezone')))->format('d-m-Y h:i:s A') ?? '',
            'updated_at' => (new \DateTime($this->updated_at))->setTimezone(new \DateTimeZone(config('app.timezone')))->format('d-m-Y h:i:s A') ?? '',
        ];
    }
}
