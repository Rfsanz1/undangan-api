<?php

namespace App\Controllers\Api;

use App\Repositories\GuestContract;
use App\Request\GuestRequest;
use App\Request\GuestUpdateRequest;
use App\Response\JsonResponse;
use Core\Auth\Auth;
use Core\Http\Request;
use Core\Http\Respond;
use Core\Routing\Controller;

class GuestController extends Controller
{
    private $guest;
    private $json;

    public function __construct(GuestContract $guest, JsonResponse $json)
    {
        $this->guest = $guest;
        $this->json = $json;
    }

    private function adminGuest(string $uuid): object
    {
        return $this->guest->getByUuid(Auth::id(), $uuid);
    }

    private function adminData(object $guest): array
    {
        return [
            'uuid' => $guest->uuid,
            'token' => $guest->token,
            'name' => $guest->name,
            'greeting' => $guest->greeting,
            'category' => $guest->category,
            'presence' => $guest->presence,
            'created_at' => $guest->created_at,
            'updated_at' => $guest->updated_at,
        ];
    }

    private function publicData(object $guest): array
    {
        return [
            'name' => $guest->name,
            'greeting' => $guest->greeting,
            'category' => $guest->category,
            'presence' => $guest->presence,
        ];
    }

    public function index(Request $request): JsonResponse
    {
        $per = max(1, min(100, intval($request->get('per', 20))));
        $next = max(0, intval($request->get('next', 0)));
        $search = $request->get('search');
        $presence = $request->get('presence');

        if ($presence !== null && $presence !== '' && $presence !== 'true' && $presence !== 'false') {
            return $this->json->errorBadRequest(['presence must be a boolean']);
        }

        $presence = $presence === '' || $presence === null ? null : $presence === 'true';

        return $this->json->successOK([
            'count' => $this->guest->count(Auth::id(), $search, $presence),
            'lists' => $this->guest->getAll(Auth::id(), $per, $next, $search, $presence)
                ->map(fn ($guest) => $this->adminData($guest)),
        ]);
    }

    public function show(string $uuid): JsonResponse
    {
        $guest = $this->adminGuest($uuid);
        if (!$guest->exist()) {
            return $this->json->errorNotFound();
        }

        return $this->json->successOK($this->adminData($guest));
    }

    public function create(GuestRequest $request): JsonResponse
    {
        $valid = $request->validated();
        if ($valid->fails()) {
            return $this->json->errorBadRequest($valid->messages());
        }

        $guest = $this->guest->create([
            'user_id' => Auth::id(),
            ...$valid->only(['name', 'greeting', 'category', 'presence']),
        ]);

        return $this->json->success($this->adminData($guest), Respond::HTTP_CREATED);
    }

    public function update(string $uuid, GuestUpdateRequest $request): JsonResponse
    {
        $guest = $this->adminGuest($uuid);
        if (!$guest->exist()) {
            return $this->json->errorNotFound();
        }

        $valid = $request->validated();
        if ($valid->fails()) {
            return $this->json->errorBadRequest($valid->messages());
        }

        $data = $valid->only(['name', 'greeting', 'category', 'presence']);
        if (count($data) === 0) {
            return $this->json->errorBadRequest(['guest data cannot be empty']);
        }

        if ($this->guest->update($guest, $data) !== 1) {
            return $this->json->errorServer();
        }

        return $this->json->successStatusTrue();
    }

    public function destroy(string $uuid): JsonResponse
    {
        $guest = $this->adminGuest($uuid);
        if (!$guest->exist()) {
            return $this->json->errorNotFound();
        }

        if ($this->guest->delete($guest) !== 1) {
            return $this->json->errorServer();
        }

        return $this->json->successStatusTrue();
    }

    public function public(string $token): JsonResponse
    {
        if (!preg_match('/\A[a-f0-9]{64}\z/D', $token)) {
            return $this->json->errorNotFound();
        }

        $guest = $this->guest->getByToken($token);
        if (!$guest->exist()) {
            return $this->json->errorNotFound();
        }

        return $this->json->successOK($this->publicData($guest));
    }
}