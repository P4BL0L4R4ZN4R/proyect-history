<?php

namespace App\Http\Controllers\API\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\CommentVote;

class CommentController extends Controller
{
    // Método público para invitados (sin userVote)
    public function publicIndex()
    {
        $comentarios = Comment::all();
        $result = $comentarios->map(function ($comentario) {
            $data = $comentario->toArray();
            $data['upvotes'] = $comentario->upvotes ?? 0;
            $data['downvotes'] = $comentario->downvotes ?? 0;
            $data['userVote'] = null;
            return $data;
        });
        return response()->json($result);
    }
    public function index(Request $request)
    {
        // Permitir que la consulta funcione aunque el token sea inválido o no haya usuario
        $user = null;
        try {
            $user = auth('sanctum')->user();
        } catch (\Throwable $e) {
            // Si el token es inválido/no existe, ignorar y mostrar comentarios como invitado
            $user = null;
        }
        $comentarios = Comment::all();
        $result = $comentarios->map(function ($comentario) use ($user) {
            $data = $comentario->toArray();
            $data['upvotes'] = $comentario->upvotes ?? 0;
            $data['downvotes'] = $comentario->downvotes ?? 0;
            // Si hay usuario autenticado, buscar su voto
            if ($user) {
                $vote = $comentario->votes()->where('user_id', $user->id)->first();
                $data['userVote'] = $vote ? $vote->vote : null;
            } else {
                $data['userVote'] = null;
            }
            return $data;
        });
        return response()->json($result);
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

    // Puntuar comentario con pulgar arriba
    public function rateUp(Request $request, $id)
    {
        $user = $request->user();
        $comment = Comment::findOrFail($id);
        $vote = CommentVote::where('user_id', $user->id)->where('comment_id', $id)->first();

        $mensaje = '';
        $actualizado = false;
        if ($vote) {
            if ($vote->vote === 'up') {
                // Si ya votó pulgar arriba, quitar el voto
                $vote->delete();
                $comment->decrement('upvotes');
                $mensaje = 'Voto eliminado';
            } else {
                // Si tenía pulgar abajo, cambiar a arriba
                $vote->vote = 'up';
                $vote->save();
                $comment->decrement('downvotes');
                $comment->increment('upvotes');
                $mensaje = '¡Voto actualizado!';
                $actualizado = true;
            }
        } else {
            // Nuevo voto pulgar arriba
            CommentVote::create([
                'user_id' => $user->id,
                'comment_id' => $id,
                'vote' => 'up',
            ]);
            $comment->increment('upvotes');
            $mensaje = '¡Gracias por tu voto!';
        }

        $userVote = null;
        if ($user) {
            $vote = $comment->votes()->where('user_id', $user->id)->first();
            $userVote = $vote ? $vote->vote : null;
        }
        return response()->json([
            'success' => true,
            'message' => $mensaje,
            'comment' => array_merge($comment->toArray(), ['user_id' => $user ? $user->id : null]),
            'upvotes' => $comment->upvotes,
            'downvotes' => $comment->downvotes,
            'userVote' => $userVote,
            'updated' => $actualizado
        ]);
    }

    // Puntuar comentario con pulgar abajo
    public function rateDown(Request $request, $id)
    {
        $user = $request->user();
        $comment = Comment::findOrFail($id);
        $vote = CommentVote::where('user_id', $user->id)->where('comment_id', $id)->first();

        $mensaje = '';
        $actualizado = false;
        if ($vote) {
            if ($vote->vote === 'down') {
                // Si ya votó pulgar abajo, quitar el voto
                $vote->delete();
                $comment->decrement('downvotes');
                $mensaje = 'Voto eliminado';
            } else {
                // Si tenía pulgar arriba, cambiar a abajo
                $vote->vote = 'down';
                $vote->save();
                $comment->decrement('upvotes');
                $comment->increment('downvotes');
                $mensaje = '¡Voto actualizado!';
                $actualizado = true;
            }
        } else {
            // Nuevo voto pulgar abajo
            CommentVote::create([
                'user_id' => $user->id,
                'comment_id' => $id,
                'vote' => 'down',
            ]);
            $comment->increment('downvotes');
            $mensaje = '¡Gracias por tu voto!';
        }

        $userVote = null;
        if ($user) {
            $vote = $comment->votes()->where('user_id', $user->id)->first();
            $userVote = $vote ? $vote->vote : null;
        }
        return response()->json([
            'success' => true,
            'message' => $mensaje,
            'comment' => array_merge($comment->toArray(), ['user_id' => $user ? $user->id : null]),
            'upvotes' => $comment->upvotes,
            'downvotes' => $comment->downvotes,
            'userVote' => $userVote,
            'updated' => $actualizado
        ]);
    }
}