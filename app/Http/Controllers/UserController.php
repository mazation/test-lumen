<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Event;


use Laravel\Lumen\Routing\Controller as BaseController;

class UserController extends BaseController
{
    public function create(Request $request)
    {
        $this->validate($request, [
            "name" => "required",
            "email" => "required",
            "password" => "required"
        ]);
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->api_token = Str::random(40);
        $user->save();
        return response()->json([
            'success' => true,
            'user' => $user,
            'token' => $user->api_token
        ]);
    }

    public function get(Request $request)
    {
        $id = request()->route('id');
        $logged_user = $request->user();
        if (!($logged_user->id == $id  || $logged_user->hasPermission('GetAllUsers'))) return response('Access Denied', 403);
        $user = User::find($id);
        if (!$user) return response()->json([
            "success" => false,
            "error" => "User with this id doesn't exists"
        ]);
        if ($request->expand) {
            $expand_array = explode(",", $request->expand);
            foreach ($expand_array as $relation) {
                $user->{$relation . "_expand"} = $user->{$relation}->get();
            }
        }
        return response()->json([
            "success" => true,
            "user" => $user
        ]);
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            "email" => "required",
            "password" => "required",
        ]);
        $userByEmail = User::where('email', $request->email);
        if (!$userByEmail) return response()->json([
            "success" => false,
            "error" => "User with this email wasn't found"
        ]);
        if (Hash::check($request->password, $userByEmail->password)) {
            return response()->json([
                "success" => true,
                "api_token" => $userByEmail->api_token
            ]);
        } else {
            return response()->json([
                "success" => false,
                "error" => "Password is incorrect"
            ]);
        }
    }

    public function addEvent(Request $request)
    {
        $this->validate($request, [
            "user_id" => "required",
            "event_id" => "required",
        ]);
        $logged_user = $request->user();
        if (!($logged_user->id == $request->user_id || $logged_user->hasPermission('AttachEventsAllUsers'))) return response('Access Denied', 403);
        $user = User::find($request->user_id);
        $event = Event::find($request->event_id);
        if (!($user || $event)) return response()->json([
            "success" => true,
            "error" => sprintf("There are no matches for %s %s", $user ? "User," : "", $event ? "Event" : "")
        ]);
        $user->events()->attach($request->event_id);
    }
}
