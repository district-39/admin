<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
            'invitation' => ['required', 'integer', 'exists:invitations,id'],
        ])->validate();

        $invitation = Invitation::findOrFail($input['invitation']);

        if ($invitation->isAccepted()) {
            throw ValidationException::withMessages([
                'invitation' => 'This invitation has already been used.',
            ]);
        }

        if ($invitation->email !== $input['email']) {
            throw ValidationException::withMessages([
                'email' => 'The email address must match the invitation.',
            ]);
        }

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'],
        ]);

        $invitation->update(['accepted_at' => now()]);

        return $user;
    }
}
