<?php

namespace App\Models;

use Core\Model\Model;

final class Guest extends Model
{
    protected $table = 'guests';

    protected $primaryKey = 'id';

    protected $typeKey = 'int';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'user_id',
        'uuid',
        'token',
        'name',
        'greeting',
        'category',
        'presence',
    ];

    protected $casts = [
        'presence' => 'bool',
    ];

    public function user(): Model
    {
        return User::where('id', $this->user_id)->limit(1)->first();
    }

    public function comments(): Model
    {
        return Comment::where('guest_id', $this->id)->get();
    }
}