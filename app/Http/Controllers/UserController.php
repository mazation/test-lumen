<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

use App\Models\User;


use Laravel\Lumen\Routing\Controller as BaseController;

class UserController extends BaseController
{
    public function create(Request $request) {
        $this->validate($request, [
            "api_key" => 'required',
            "name" => "required",
            "email" => "required"
        ]);
        if ($request->api_key != env('REGISTER_API_KEY')) return response('Unauthorized', 401);
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->api_token = Str::random(40);
        $user->save();
        return response()->json([
            'success' => true,
            'user' => $user,
            'token' => $user->api_token
        ]);
    }
    
}
