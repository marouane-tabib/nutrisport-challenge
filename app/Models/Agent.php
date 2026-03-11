<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Agent extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\AgentFactory> */
    use HasFactory;

    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = ['password'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function hasFullAccess(): bool
    {
        return $this->id === 1;
    }
}
