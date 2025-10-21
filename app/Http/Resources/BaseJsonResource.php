<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

abstract class BaseJsonResource extends JsonResource
{
    protected array $meta = [];

    public function withMeta(array $meta): static
    {
        $this->meta = $meta;

        return $this;
    }

    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }

    public function additionalMeta(): array
    {
        return $this->meta;
    }
}
