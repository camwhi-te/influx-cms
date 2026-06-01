<?php

namespace App\Http\Requests\Groups;

use App\Enums\GroupRole;
use App\Rules\UniqueGroupInvitation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateGroupInvitationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255', new UniqueGroupInvitation($this->route('group'))],
            'role' => ['required', 'string', Rule::enum(GroupRole::class)],
        ];
    }
}
