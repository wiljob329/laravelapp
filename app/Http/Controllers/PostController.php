<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class PostController extends Controller
{

    function actuallyUpdate(Post $post, Request $request)
    {
        $updateFields = $request->validate([
            'title' => 'required',
            'body' => 'required'
        ]);
        $updateFields['title'] = strip_tags($updateFields['title']);
        $updateFields['body'] = strip_tags($updateFields['body']);

        $post->update($updateFields);

        return back()->with('success', 'Post actualizado con exito!');
    }


    public function showEditForm(Post $post)
    {
        return view('edit-post', ['post' => $post]);
    }


    public function delete(Post $post)
    {
        $post->delete();
        return redirect('/profile/' . auth()->user()->username)->with('success', 'Post Eliminado con exito!');
    }


    public function viewSinglePost(Post $post)
    {
        $post['body'] = Str::markdown($post->body);
        return view('single-post', ['post' => $post]);
    }


    public function storeNewPost(Request $request)
    {
        $inFields = $request->validate([
            'title' => 'required',
            'body' => 'required'
        ]);
        $inFields['title'] = strip_tags($inFields['title']);
        $inFields['body'] = strip_tags($inFields['body']);
        $inFields['user_id'] = auth()->id();
        $newPost = Post::create($inFields);

        return redirect("/post/{$newPost->id}")->with('success', 'Post creado con exito');
    }


    public function showCreateForm()
    {
        return view('create-post');
    }
}
