<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSession;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserSessionController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'activated_at' => 'required|date',
            'appointment_at' => 'required|date|after:activated_at',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $session = UserSession::create([
            'user_id' => $request->user_id,
            'activated_at' => Carbon::parse($request->activated_at),
            'appointment_at' => Carbon::parse($request->appointment_at),
        ]);

        return response()->json([
            'message' => 'Session created successfully',
            'session' => $session
        ], 201);
    }

    public function show(UserSession $session): JsonResponse
    {
        return response()->json([
            'session' => $session->load('user')
        ]);
    }
}
