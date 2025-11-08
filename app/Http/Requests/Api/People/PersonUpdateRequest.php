<?php

namespace App\Http\Requests\Api\People;

use App\Enums\PersonGender;
use App\Enums\PersonVisibility;
use App\Http\Requests\Api\ApiRequest;
use App\Models\Person;
use App\Support\FamilyContext;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class PersonUpdateRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'given_name' => ['sometimes', 'string', 'max:255'],
            'middle_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'surname' => ['sometimes', 'string', 'max:255'],
            'display_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'gender' => ['sometimes', 'nullable', Rule::in(PersonGender::values())],
            'birth_date' => ['sometimes', 'nullable', 'date'],
            'death_date' => ['sometimes', 'nullable', 'date', 'after_or_equal:birth_date', 'required_if:is_deceased,true'],
            'is_deceased' => ['sometimes', 'boolean'],
            'clear_death_date' => ['sometimes', 'boolean'],
            'visibility' => ['sometimes', 'nullable', Rule::in(PersonVisibility::values())],
            'meta' => ['sometimes', 'nullable', 'array'],
            'closest_relative_id' => ['sometimes', 'nullable', 'integer', 'exists:people,id'],
            'closest_relationship' => ['sometimes', 'nullable', 'string', 'max:255'],
            'avatar_url' => ['sometimes', 'nullable', 'url'],
            'photo_urls' => ['sometimes', 'array', 'max:10'],
            'photo_urls.*' => ['url'],
            'avatar' => ['sometimes', 'nullable', 'image', 'max:5120'],
            'photos' => ['sometimes', 'array', 'max:10'],
            'photos.*' => ['image', 'max:8192'],
            'clear_avatar' => ['sometimes', 'boolean'],
            'clear_photos' => ['sometimes', 'boolean'],
            'notify_relatives' => ['sometimes', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (! $this->has('closest_relative_id')) {
                return;
            }

            $closestId = $this->input('closest_relative_id');
            if (! $closestId) {
                return;
            }

            $familyId = app(FamilyContext::class)->currentFamilyId();
            if (! $familyId) {
                $validator->errors()->add('closest_relative_id', __('Family context missing.'));

                return;
            }

            $exists = Person::query()
                ->whereKey($closestId)
                ->where('family_id', $familyId)
                ->exists();

            if (! $exists) {
                $validator->errors()->add('closest_relative_id', __('Closest member must belong to this family.'));
            }
        });
    }
}
