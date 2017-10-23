<?php

namespace App\Http\Controllers;

use App\Comment;
use App\CommentVote;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
            if(CommentVote::create($data))
                return "true";
        }
        if($type == "spam"){
            $this->validate($request,[
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
            if(CommentSpam::create($data))

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
