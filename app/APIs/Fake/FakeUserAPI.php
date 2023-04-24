<?php

namespace App\APIs\Fake;

use App\Contracts\UserAPI;
use Faker\Factory;

class FakeUserAPI implements UserAPI
{
    public function getUserById(int|string $id, bool $withSensitiveInfo): array
    {
        $user = $this->fakeUser();
        $user['org_id'] = $id;

        if (! $withSensitiveInfo) {
            $user['document_id'] = null;
        }

        return $user;
    }

    public function getUserByLogin(string $login, bool $withSensitiveInfo): array
    {
        $user = $this->fakeUser();
        $faker = Factory::create();

        $login_parts = explode('.', $login);
        $first_name = ucfirst($login_parts[0]);

        $full_name = $first_name.' '.ucfirst($login_parts[1].$faker->lastName());

        $user['full_name'] = $full_name;
        $user['login'] = $login;

        if (! $withSensitiveInfo) {
            $user['document_id'] = null;
        }

        return $user;
    }

    public function authenticate(string $login, string $password, bool $withSensitiveInfo): array
    {
        if ($login !== $password) {
            return $this->getUserByLogin($login, $withSensitiveInfo);
        }

        return [
            'ok' => true,
            'found' => false,
            'message' => 'Invalid login or password',
        ];
    }

    protected function fakeUser(): array
    {
        $faker = Factory::create();

        $gender = (mt_rand() / mt_getrandmax()) >= 0.6 ? 'female' : 'male';

        if ($gender === 'male') {
            $full_name = $faker->firstNameMale().' FakeUserAPI.php'.$faker->lastName();
        } else {
            $full_name = $faker->firstNameFemale().' FakeUserAPI.php'.$faker->lastName();
        }

        return [
            'ok' => true,
            'found' => true,
            'active' => true,
            'login' => strtolower(str_replace(' ', '.', explode(' ', $full_name)[0])),
            'org_id' => '100'.$faker->randomNumber(5),
            'full_name' => $full_name,
            'document_id' => $faker->numerify('#############'),
            'position_id' => '70'.$faker->randomNumber(5),
            'position_name' => $faker->jobTitle(),
            'division_id' => '50'.$faker->randomNumber(5),
            'division_name' => $faker->company(),
            'password_expires_in_days' => $faker->randomNumber(2),
            'remark' => $faker->sentence(),
        ];
    }
}
