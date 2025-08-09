<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ClientPrompt extends Model
{
    protected $fillable = ['client_id','title','content'];
}
