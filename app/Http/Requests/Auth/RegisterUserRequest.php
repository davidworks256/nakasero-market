<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class RegisterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['nullable', 'string', 'max:20', 'unique:'.User::class],
            'role' => ['required', 'string', 'in:farmer,buyer'],
            'stall' => ['nullable', 'string', 'max:255', 'required_if:role,farmer'],
            'location' => ['nullable', 'string', 'max:255', 'required_if:role,farmer'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'consent' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'consent.accepted' => 'You must agree to the privacy notice to create an account.',
        ];
    }

    public function toUser(): User
    {
        $user = User::create([
            'name' => $this->input('name'),
            'email' => $this->input('email'),
            'phone' => $this->input('phone') ?: null,
            'password' => $this->input('password'),
            'role' => $this->input('role'),
            'consent_at' => now(),
        ]);

        if ($user->isFarmer()) {
            $user->farmerProfile()->create([
                'stall' => $this->input('stall'),
                'location' => $this->input('location'),
            ]);
        }

        return $user;
    }
}
