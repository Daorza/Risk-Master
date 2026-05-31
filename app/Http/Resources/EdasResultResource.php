<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\EdasResult;

class EdasResultResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var EdasResult */
        return [
            'id' => $this->id,
            'rank' => $this->rank,
            'alternative' => $this->whenLoaded('alternative', fn() => [
                'id' => $this->alternative->id,
                'name' => $this->alternative->name,
                'description' => $this->alternative->description,
            ]),
            'pda' => $this->pda,
            'nda' => $this->nda,
            'sp' => $this->sp,
            'sn' => $this->sn,
            'nsp' => $this->nsp,
            'nsn' => $this->nsn,
            'as_score' => $this->appraisal_score,
            'as_formatted' => $this->as_formatted,
            'quality_label' => $this->quality_label,
            'quality_color' => $this->quality_color,
            'calculated_at' => $this->calculated_at?->toIso8601String(),
        ];
    }
}
