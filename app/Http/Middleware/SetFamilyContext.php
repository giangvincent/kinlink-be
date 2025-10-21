<?php

namespace App\Http\Middleware;

use App\Models\Family;
use App\Support\FamilyContext;
use Closure;
use Illuminate\Http\Request;

class SetFamilyContext
{
    public function __construct(private readonly FamilyContext $familyContext)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        $familyId = $this->extractFamilyId($request);

        if ($familyId !== null) {
            $this->familyContext->setId($familyId);
        } else {
            $this->familyContext->clear();
        }

        try {
            return $next($request);
        } finally {
            $this->familyContext->clear();
        }
    }

    private function extractFamilyId(Request $request): ?int
    {
        $routeFamily = $request->route('family');

        if ($routeFamily instanceof Family) {
            return $routeFamily->getKey();
        }

        if (is_numeric($routeFamily)) {
            return (int) $routeFamily;
        }

        if (is_string($routeFamily) && $routeFamily !== '') {
            $familyId = Family::query()
                ->where('slug', $routeFamily)
                ->value('id');

            return $familyId ? (int) $familyId : null;
        }

        if ($request->hasHeader('X-Family-ID') && is_numeric($request->header('X-Family-ID'))) {
            return (int) $request->header('X-Family-ID');
        }

        if ($request->hasHeader('X-Family-Slug')) {
            $familyId = Family::query()
                ->where('slug', $request->header('X-Family-Slug'))
                ->value('id');

            return $familyId ? (int) $familyId : null;
        }

        if ($request->filled('family_id') && is_numeric($request->input('family_id'))) {
            return (int) $request->input('family_id');
        }

        return null;
    }
}
