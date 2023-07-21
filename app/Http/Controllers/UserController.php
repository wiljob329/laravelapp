<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{

    public function profile(User $user)
    {
        return view('profile-posts', [
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
        if (auth()->attempt(['username'=>$loginFields['loginusername'], 'password'=>$loginFields['loginpassword']]))
        {
            $request->session()->regenerate();
            return redirect('/')->with('success', 'LogIn con exito!');
        } else
        {
            return redirect('/')->with('failure','Logeo Invalido');
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
        return redirect('/')->with('success','Cuenta creada con exito!');
    }
}
