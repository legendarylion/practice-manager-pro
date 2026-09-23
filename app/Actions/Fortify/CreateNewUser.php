<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;
use App\Models\Practice;


class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * 
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'practice_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            // 'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        \Log::info('Creating practice with:', [
            'input' => $input,
            'practice_data' => [
                'name' => $input['practice_name'],
                'email' => $input['email'],
                'timezone' => config('app.timezone'),
                'settings' => ['setup_completed' => false]
            ]
        ]);

        $practice = Practice::create([
            'name' => $input['practice_name'],
            'email' => $input['email'],  
            'timezone' => config('app.timezone'),  
            'settings' => [
                'setup_completed' => false  
            ]
        ]);


        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'practice_id' => $practice->id
        ]);
    }
}
