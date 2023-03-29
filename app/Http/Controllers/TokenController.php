<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTokenRequest;
use App\Http\Requests\ViewTokenRequest;
use App\Models\User;
use Illuminate\Http\Request;

class TokenController extends Controller
{
    public function view(ViewTokenRequest $request) {

        $user = User::firstWhere('email', $request->email);

        return response()->json(['user' => $user]);
    }

    public function store(StoreTokenRequest $request) {

        $user = User::create($request->validated());

        return response()->json(['user' => $user, 'message' => 'Token stored']);
    }
}
