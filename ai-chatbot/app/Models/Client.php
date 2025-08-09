<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Authenticatable
{
    protected $fillable = [
        'name','email','password','api_token','language','plan',
        'dialog_limit','dialog_used','prompts_limit','prompt_max_length',
        'rate_limit','is_active','last_active_at'
    ];
    protected $hidden = ['password','api_token'];

    public function domains(): HasMany { return $this->hasMany(ClientDomain::class); }
    public function prompts(): HasMany { return $this->hasMany(ClientPrompt::class); }
}
