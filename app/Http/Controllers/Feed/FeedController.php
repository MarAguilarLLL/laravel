<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Models\Feed;
use App\Models\Like;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedController extends Controller
{

    public function index()
    {
        $feeds = Feed::with('user')->latest()->get();
        return response([
            'feeds' => $feeds
        ], 200);
    }
    public function store(PostRequest $request)
    {
        $request -> validated();

        auth()->user()->feeds()->create([
            'content' => $request->content
        ]);

        return response([
            'message' => 'Logrado',
        ], 201);
    }

    public function likePost($feed_id)
    {
        //seleccionar feed con feed_id
        $feed = Feed::whereId($feed_id)->first();

        if(!$feed){
            return response([
                'message' => '404 Not found'
            ], 500);
        }

        $existingLike = Like::where('user_id', auth()->id())
                          ->where('feed_id', $feed_id)
                          ->first();

        if($existingLike){
            // Si ya existe el like, lo removemos (toggle)
            $existingLike->delete();
            return response([
                'message' => 'Unliked'
            ], 200);
        } else {
            // Si no existe, creamos el like
            Like::create([
                'user_id' => auth()->id(),
                'feed_id' => $feed_id
            ]);
            return response([
                'message' => 'Liked'
            ], 200);
        }
    }

    public function comment(Request $request, $feed_id)
    {

        $request->validate([
            'body'=>'required'
        ]);

        $comment = Comment::create([
            'user_id'=>auth()->id(),
            'feed_id'=>$feed_id,
            'body'=>$request->body
        ]);

        return response([
            'message' => 'success'
        ], 201);
    }

    public function getComments($feed_id)
    {
        $comments = Comment::with('feed')->with('user')->whereFeedId($feed_id)->latest()->get();

        return response([
            'comments'=>$comments
        ], 200);
    }
}
