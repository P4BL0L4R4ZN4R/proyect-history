<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;

class CommentController extends Controller
{
    public function index()
    {
        return response()->json(Comment::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'text' => 'required|string'
        ]);

        $comment = Comment::create([
            'text' => $request->input('text'),
        ]);

        return response()->json($comment, 201);
    }

    public function update(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);
        $request->validate([
            'text' => 'required|string'
        ]);
        $comment->update([
            'text' => $request->input('text'),
        ]);
        return response()->json($comment);
    }

    public function destroy($id)
    {
        Comment::destroy($id);
        return response()->json(['message' => 'Comentario eliminado'], 200);
    }
}