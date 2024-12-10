<?php

namespace App\Contracts;

interface UserAPI
{
    public function getUserById(int|string $id, bool $withSensitiveInfo): array;

    public function getUserByLogin(string $login, bool $withSensitiveInfo): array;

    public function authenticate(string $login, string $password, bool $withSensitiveInfo): array;

    public function authenticateADFS(string $login, string $password): array;
}
