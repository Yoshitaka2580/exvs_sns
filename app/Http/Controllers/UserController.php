<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use App\User;
use Storage;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show(string $name)
    {
        $user = User::where('name', $name)->first()
            ->load(['posts.user', 'posts.likes', 'posts.tags', 'posts.comments', 'posts.category']);

        $posts = $user->posts->sortByDesc('created_at');

        return view('users.show', [
            'user' => $user,
            'posts' => $posts,
        ]);
    }

    public function edit()
    {
        $user = User::where('id', Auth::id())->first();

        return view('users.edit', compact('user'));
    }

    public function update(UserRequest $request, string $name)
    {
        $uploadfile = $request->file('thumbnail');

        if (!empty($uploadfile)) {
            if (app()->isLocal() || app()->runningUnitTests()) {
                $thumbnailname = $request->file('thumbnail')->hashName();
                $request->file('thumbnail')->storeAs('public/user', $thumbnailname);
            } else {
                $path = Storage::disk('s3')->putFile('vs-connect', $uploadfile, 'public');
                $thumbnailname = Storage::disk('s3')->url($path);
            }
            $param = [
                'thumbnail' => $thumbnailname,
                'body' => $request->body,
            ];
        } else {
            $param = [
                'body' => $request->body,
            ];
        }

        User::where('id', $request->user_id)->update($param);

        return redirect(route('users.show', compact('name')));
    }

    public function likes(string $name)
    {
        $user = User::where('name', $name)->first()
            ->load(['likes.user', 'likes.likes', 'likes.tags', 'likes.category', 'likes.comments']);
        $posts = $user->likes->sortByDesc('created_at');

        return view('users.likes', [
            'user' => $user,
            'posts' => $posts,
        ]);
    }

    public function followings(string $name)
    {
        $user = User::where('name', $name)->first()
            ->load('followings.followers');

        $followings = $user->followings->sortByDesc('created_at');

        return view('users.followings', [
            'user' => $user,
            'followings' => $followings,
        ]);
    }

    public function followers(string $name)
    {
        $user = User::where('name', $name)->first()
            ->load('followers.followers');

        $followers = $user->followers->sortByDesc('created_at');

        return view('users.followers', [
            'user' => $user,
            'followers' => $followers,
        ]);
    }

    /**
     * $nameはフォローされる側のモデル
     */
    public function follow(Request $request, string $name)
    {
        $user = User::where('name', $name)->first();

        //HTTPの例外を投げる
        if ($user->id === $request->user()->id) {
            return abort('404');
        }

        $request->user()->followings()->detach($user);
        $request->user()->followings()->attach($user);

        //どのユーザーをフォローしたかJSON形式で変換
        return ['name' => $name];
    }

    public function unfollow(Request $request, string $name)
    {
        $user = User::where('name', $name)->first();

        if ($user->id === $request->user()->id) {
            return abort('404');
        }

        $request->user()->followings()->detach($user);

        return ['name' => $name];
    }
}
