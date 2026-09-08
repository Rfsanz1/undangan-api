<?php

namespace App\Repositories;

use Core\Model\Model;

interface GuestContract
{
    public function create(array $data): Model;
    public function getAll(int $user_id, int $limit, int $offset, string|null $search = null, bool|null $presence = null): Model;
    public function count(int $user_id, string|null $search = null, bool|null $presence = null): int;
    public function getByUuid(int $user_id, string $uuid): Model;
    public function getByToken(string $token): Model;
    public function updatePresence(int $id, bool $presence): int;
    public function update(Model $guest, array $data): int;
    public function delete(Model $guest): int;
}