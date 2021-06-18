<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request) {

    	$credenciais = $request->all(['email', 'password']);

    	$token = auth('api')->attempt($credenciais);

    	if($token) {

    		return response()->json(['token' => $token]);

    	} else {

    		return response()->json(['erro' => 'Usuario ou Senha invalido!'], 403);

    	}

    	dd($token);
    }

    public function logout() {
    	return 'logout';
    }

    public function refresh() {
    	return 'refresh';
    }

    public function me() {
    	return response()->json(auth()->user());
    }
}
