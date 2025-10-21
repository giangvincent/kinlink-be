<?php

namespace App\Http\Controllers\Api\Exports;

use App\Http\Controllers\Api\ApiController;
use App\Models\Export;
use Illuminate\Http\Request;

class ExportShowController extends ApiController
{
    public function __invoke(Request $request, Export $export)
    {
        $this->authorize('view', $export);

        return $this->ok([
            'id' => $export->getKey(),
            'status' => $export->status,
            'path' => $export->path,
            'disk' => $export->disk,
            'meta' => $export->meta,
            'created_at' => $export->created_at?->toIso8601String(),
            'updated_at' => $export->updated_at?->toIso8601String(),
        ]);
    }
}
