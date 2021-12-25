<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Utils\Utils;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

use App\Models\User;
use App\Models\CustomEvent;


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
        $existing_email = User::where('email', $request->email)->first();
        if ($existing_email) return response()->json([
            "success" => false,
            "error" => "User with this email currently exists"
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
            try {
                $user = Utils::handleExpand($user, $request->expand); 
            } catch (Exception $e) {
                return response()->json([
                    "success" => false,
                    "error" => $e
                ]);
            }
        }
        return response()->json([
            "success" => true,
            "user" => $user
        ]);
    }

    public function update(Request $request)
    {
        $id = request()->route('id');
        $logged_user = $request->user();
        if (!($logged_user->id == $id  || $logged_user->hasPermission('UpdateAllUsers'))) return response('Access Denied', 403);
        $user = User::find($id);
        if (!$user) return response()->json([
            "success" => false,
            "error" => "No user was found"
        ]);
        $user->email = $request->email ?? $user->email;
        $user->password = $request->password ? Hash::make($request->password) : $user->password;
        $user->save();
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
        $userByEmail = User::where('email', $request->email)->first();
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
        $event = CustomEvent::find($request->event_id);
        if (!($user && $event)) return response()->json([
            "success" => true,
            "error" => sprintf("There are no matches for %s %s", !$user ? "User" : "", !$event ? "Event" : "")
        ]);
        $user->events()->attach($request->event_id);
        return response()->json([[
            "success" => true
        ]]);
    }
}
