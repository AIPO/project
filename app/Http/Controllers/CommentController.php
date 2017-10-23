<?php

namespace App\Http\Controllers;

use App\Comment;
use App\CommentVote;
use App\CommentSpam;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($pageId)
    {
        $comments = Comment::where('page_id', $pageId)->get();
        $commentData = [];
        foreach ($comments as $key) {
            $user = User::find($key->users_id);
            $name = $user->name;
            $replies = $this->replies($key->id);
            $photo = $user->first()->photo_url;
            $reply = 0;
            $vote = 0;
            $voteStatus = 0;
            $spam = 0;
            if (Auth::user()) {
                $voteByUser = CommentVote::where('comment_id', $key->id)
                    ->where('user_id', Auth::user()->id())->first();
                $spamComment = CommentSpam::where('comment_id', $key->id)
                    ->where('user_id', Auth::user()->id)->first();
                if ($voteByUser) {
                    $vote = 1;
                    $voteStatus = $voteByUser->vote;
                }
                if ($spamComment) {
                    $spam = 1;
                }
            }
            if (sizeof($replies) > 0) {
                $reply = 1;
            }
            if (!$spam) {
                array_push($commentData, [
                    "name" => $name,
                    "photo_url" => (string)$photo,
                    "comment_id" => $key->id,
                    "comment" => $key->comment,
                    "votes" => $key->votes,
                    "reply" => $reply,
                    "votedByUser" => $vote,
                    "vote" => $voteStatus,
                    "spam" => $spam,
                    "replies" => $replies,
                    "date" => $key->created_at->toDateTimeString()
                ]);
            }
        }
        $collection = collect($commentData);
        return $collection->sortBy('votes');
    }

    protected function replies()
    {
        
}
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'comment' => 'required',
            'reply_id' => 'filled',
            'page_id' => 'filled',
            'users_id' => 'required',
        ]);
        $comment = Comment::create($request->all());
        if ($comment) {
            return ["status" => "true", "commentId" => $comment->id];
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Comment $comment
     * @return \Illuminate\Http\Response
     */
    public function show(Comment $comment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Comment $comment
     * @return \Illuminate\Http\Response
     */
    public function edit(Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Comment $comment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $commentId, $type)
    {
        if ($type == "vote") {
            $this->validate($request, [
                'vote' => 'required',
                'users_id' => 'required'
            ]);
            $comments = Comment::find($commentId);
            $data = [
                'comment_id' => $commentId,
                'vote' => $request->vote,
                'user_id' => 'users_id',
            ];
            if ($request->vote == "up") {
                $comment = $comments->first();
                $vote = $comment->vote;
                $vote++;
                $comment->vote = $vote;
                $comments->save();
            }
            if ($request->vote == "down") {
                $comment = $comments->first();
                $vote = $comment->vote;
                $vote--;
                $comment->vote = $vote;
                $comments->save();
            }
            if (CommentVote::create($data))
                return "true";
        }
        if ($type == "spam") {
            $this->validate($request, [
                "users_id" => 'required'
            ]);
            $comments = Comment::find($commentId);

            $comment = $comments->first();

            $spam = $comment->spam;

            $spam++;

            $comments->spam = $spam;

            $comments->save();

            $data = [

                "comment_id" => $commentId,

                'user_id' => $request->users_id,

            ];
            if (CommentSpam::create($data))

                return "true";
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Comment $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment)
    {
        //
    }
}
