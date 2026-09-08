<?php

namespace App\Repositories;

use App\Models\Guest;
use App\Models\Comment;
use Core\Model\Model;
use Ramsey\Uuid\Uuid;

class GuestRepositories implements GuestContract
{
    private function generateToken(): string
    {
        do {
            $token = bin2hex(random_bytes(32));
        } while (Guest::where('token', $token)->limit(1)->first()->exist());

        return $token;
    }

    public function create(array $data): Model
    {
        return Guest::create([
            'uuid' => Uuid::uuid4()->toString(),
            'token' => $this->generateToken(),
            ...$data,
        ]);
    }

    public function getAll(int $user_id, int $limit, int $offset, string|null $search = null, bool|null $presence = null): Model
    {
        $query = Guest::where('user_id', $user_id);

        if ($search !== null && trim($search) !== '') {
            $query->where('name', '%' . trim($search) . '%', 'LIKE');
        }

        if ($presence !== null) {
            $query->where('presence', $presence);
        }

        return $query->select([
            'uuid',
            'token',
            'name',
            'greeting',
            'category',
            'presence',
            'created_at',
            'updated_at',
        ])->orderBy('id', 'DESC')->limit(abs($limit))->offset($offset)->get();
    }

    public function count(int $user_id, string|null $search = null, bool|null $presence = null): int
    {
        $query = Guest::where('user_id', $user_id);

        if ($search !== null && trim($search) !== '') {
            $query->where('name', '%' . trim($search) . '%', 'LIKE');
        }

        if ($presence !== null) {
            $query->where('presence', $presence);
        }

        return $query->count('id', 'guest_count')->first()->guest_count;
    }

    public function getByUuid(int $user_id, string $uuid): Model
    {
        return Guest::where('uuid', $uuid)->where('user_id', $user_id)->limit(1)->first();
    }

    public function getByToken(string $token): Model
    {
        return Guest::where('token', $token)->limit(1)->first();
    }

    public function updatePresence(int $id, bool $presence): int
    {
        return Guest::where('id', $id)->update(['presence' => $presence]);
    }

    public function update(Model $guest, array $data): int
    {
        return $guest->only(['id'])->fill($data)->save();
    }

    public function delete(Model $guest): int
    {
        Comment::where('guest_id', $guest->id)->update(['guest_id' => null]);
        return $guest->delete();
    }
}