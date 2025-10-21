<?php

namespace App\Http\Controllers\Api\Family;

use App\Enums\FamilyRole;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\Family\FamilyStoreRequest;
use App\Http\Resources\FamilyResource;
use App\Models\Family;
use App\Support\FamilyContext;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FamilyStoreController extends ApiController
{
    public function __invoke(FamilyStoreRequest $request, FamilyContext $familyContext)
    {
        $user = $request->user();
        $data = $request->validated();

        $family = DB::transaction(function () use ($data, $user) {
            $family = Family::create([
                'name' => $data['name'],
                'slug' => $this->generateUniqueSlug($data['name']),
                'locale' => $data['locale'] ?? $user->locale ?? 'en',
                'owner_user_id' => $user->getKey(),
                'settings' => [],
            ]);

            $family->members()->attach($user->getKey(), ['role' => FamilyRole::OWNER->value]);

            return $family->load('members');
        });

        $familyContext->set($family);

        return $this->created(new FamilyResource($family));
    }

    private function generateUniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $counter = 1;

        while (Family::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }
}
