<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;

class UserController extends Controller
{

    public function storeAvatar(Request $request)
    {
        // $request->file('avatar')->store('public/avatars');
        // return 'Hello';
        $request->validate([
            'avatar' => 'required|image|max:3000'
        ]);

        $user = auth()->user();
        $filename = $user->id . '-' . uniqid() . '.jpg';

        $imgData = Image::make($request->file('avatar'))->fit(120)->encode('jpg');
        Storage::put('public/avatars/' . $filename, $imgData);

        $oldAvatar = $user->avatar;

        $user->avatar = $filename;
        $user->save();

        if ($oldAvatar != "/fallback-avatar.jpg") {
            Storage::delete(str_replace("/storage/", "public/", $oldAvatar));
        }


        return back()->with('success', 'Congrats on the new avatar!');
    }


    public function showAvatarForm()
    {
        return view('avatar-form');
    }


    public function getShareData($user)
    {
        $currentFollowing = 0;

        if (auth()->check()) {

            $currentFollowing = Follow::where([['user_id', '=', auth()->user()->id], ['followeduser', '=', $user->id]])->count();
        }

        View::share("sharedData", [
            'currentFollowing' => $currentFollowing,
            'avatar' => $user->avatar,
            'username' => $user->username,
            'postCount' => $user->posts()->count(),
            'followerCount' => $user->followers()->count(),
            'followingCount' => $user->followingTheseUsers()->count()
        ]);
    }


    public function profile(User $user)
    {
        $this->getShareData($user);
        return view('profile-posts', [
            'posts' => $user->posts()->latest()->get()
        ]);
    }

    public function profileFollowers(User $user)
    {
        $this->getShareData($user);
        //return $user->followers()->latest()->get();
        return view('profile-followers', [
            'followers' => $user->followers()->latest()->get(),
        ]);
    }


    public function profileFollowing(User $user)
    {
        $this->getShareData($user);
        return view('profile-following', [
            'following' => $user->followingTheseUsers()->latest()->get(),
        ]);
    }



    public function logout()
    {
        auth()->logout();
        return redirect('/')->with('success', 'Saliste con exito!');
    }

    public function showCorrectHomepage()
    {
        if (auth()->check()) {
            return view('homepage-feed', [
                'posts' => auth()->user()->feedPosts()->latest()->get()
            ]);
        } else {
            //return 'No estas logeado!!';
            return view('homepage');
        }
    }


    public function login(Request $request)
    {
        $loginFields = $request->validate([
            'loginusername' => 'required',
            'loginpassword' => 'required'
        ]);
        if (auth()->attempt(['username' => $loginFields['loginusername'], 'password' => $loginFields['loginpassword']])) {
            $request->session()->regenerate();
            return redirect('/')->with('success', 'LogIn con exito!');
        } else {
            return redirect('/')->with('failure', 'Logeo Invalido');
        }
    }

    public function register(Request $request)
    {
        $fields = $request->validate([
            'username' => ['required', 'min:2', 'max:20', Rule::unique('users', 'username')],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);
        //$incomingFields['password'] = bcrypt($incomingFields['password']);

        $user = User::create($fields);
        auth()->login($user);
        return redirect('/')->with('success', 'Cuenta creada con exito!');
    }
}
