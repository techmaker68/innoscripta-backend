<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRegisterRequest;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Notifications\passwordNotification;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
class registercontroller extends Controller
{

    public function login(LoginRequest $request)
    {

        $user = User::where('email', $request->email)->first();
        if ($user) {
            if ((Hash::check($request->password, $user->password))) {
                $token = $user->createToken($user)->plainTextToken;
                $data['id'] = $user->id;
                $data['name'] = $user->name;
                $data['email'] = $user->email;
                $data['token'] = $token;
                $data['role'] = $user->role;
                return response()->json($data, 201);
            }
        }
        return response()->json(['error' => 'The provided credentials do not match our records.'], 400);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function Register(UserRegisterRequest $request)
    {
        try {
            $str =  Str::random(10);
            DB::beginTransaction();
            $password = $request->password . $str;
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($password),
                'role' => $request->role,
            ]);
            $token = $user->createToken($user)->plainTextToken;
            $user->notify(new passwordNotification($user, $password));
            DB::commit();
            return response()->json(['success' => 'user registered successfully'], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return $e;
        }
    }

}
