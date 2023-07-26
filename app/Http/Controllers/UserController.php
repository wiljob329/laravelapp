<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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


    public function profile(User $user)
    {
        return view('profile-posts', [
            'avatar' => $user->avatar,
            'username' => $user->username,
            'posts' => $user->posts()->latest()->get(),
            'postCount' => $user->posts()->count()
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
            return view('homepage-fade');
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
