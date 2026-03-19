<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;
use Illuminate\Support\Facades\DB;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        $adminKey = env('ADMIN_KEY', 'nrgKnSD$ZJP9sUh');

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
            'admin_key' => ['nullable', 'string'], 
        ])->after(function ($validator) use ($input, $adminKey) {
            if (isset($input['admin_key']) && $input['admin_key'] !== $adminKey) {
                $validator->errors()->add(
                    'admin_key',
                    'Kunci admin tidak valid.'
                );
            }
        })->validate();

        $isAdmin = (isset($input['admin_key']) && $input['admin_key'] === $adminKey);

        return DB::transaction(function () use ($input, $isAdmin) {
            $user = User::create([
                // 'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
                'is_admin' => $isAdmin, 
            ]);

            if ($isAdmin) {
                Admin::create([
                    'user_id' => $user->id,
                    'name' => $input['name'],
                    'prodi_id' => null,
                ]);
            }

            return $user;
        });
    }
}
