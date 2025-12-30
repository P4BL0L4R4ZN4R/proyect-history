<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentVote extends Model
{
    protected $fillable = [
        'user_id',
        'comment_id',
        'vote',
    ];

    // Relación con el usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con el comentario
    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }
}
