<?php

namespace App\Http\Requests\Api\People;

use App\Enums\PersonGender;
use App\Enums\PersonVisibility;
use App\Http\Requests\Api\ApiRequest;
use App\Models\Person;
use App\Support\FamilyContext;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class PersonStoreRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'given_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'display_name' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', Rule::in(PersonGender::values())],
            'birth_date' => ['nullable', 'date'],
            'death_date' => ['nullable', 'date', 'after_or_equal:birth_date', 'required_if:is_deceased,true'],
            'is_deceased' => ['nullable', 'boolean'],
            'clear_death_date' => ['sometimes', 'boolean'],
            'visibility' => ['nullable', Rule::in(PersonVisibility::values())],
            'meta' => ['nullable', 'array'],
            'closest_relative_id' => ['nullable', 'integer', 'exists:people,id'],
            'closest_relationship' => ['nullable', 'string', 'max:255'],
            'avatar_url' => ['nullable', 'url'],
            'photo_urls' => ['nullable', 'array', 'max:10'],
            'photo_urls.*' => ['url'],
            'avatar' => ['nullable', 'image', 'max:5120'],
            'photos' => ['nullable', 'array', 'max:10'],
            'photos.*' => ['image', 'max:8192'],
            'clear_avatar' => ['sometimes', 'boolean'],
            'clear_photos' => ['sometimes', 'boolean'],
            'notify_relatives' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (! $this->filled('display_name') && $this->filled('given_name')) {
            $this->merge([
                'display_name' => trim(
                    collect([$this->input('given_name'), $this->input('middle_name'), $this->input('surname')])
                        ->filter()
                        ->implode(' ')
                ),
            ]);
        }
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
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
