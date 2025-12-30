<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['text'];

    // Relación: un comentario tiene muchos votos
    public function votes()
    {
        return $this->hasMany(CommentVote::class);
    }
}