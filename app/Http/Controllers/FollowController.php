<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function createFollow(User $user)
    {
        // no puede seguirse a si mismo 
        if ($user->id == auth()->user()->id) {
            return back()->with('failure', 'No puedes seguirte a ti mismo');
        }

        // no puede seguir a alguien que ya sigue
        $existCheck = Follow::where([['user_id', '=', auth()->user()->id], ['followeduser', '=', $user->id]])->count();

        if ($existCheck) {
            return back()->with('failure', 'Ya sigues a este usuario.');
        }


        $newFollow = new Follow();
        $newFollow->user_id = auth()->user()->id;
        $newFollow->followeduser = $user->id;
        $newFollow->save();

        return back()->with('success', 'Usuario seguido con exito');
    }

    public function removeFollow()
    {
    }
}
