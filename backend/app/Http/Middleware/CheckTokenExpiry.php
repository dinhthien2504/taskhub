<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class CheckTokenExpiry
{
    public function handle(Request $request, Closure $next): Response
    {
        $tokenString = $request->bearerToken();

        if (!$tokenString) {
            return response()->json(['message' => 'Token không tồn tại.'], 401);
        }

        $accessToken = PersonalAccessToken::findToken($tokenString);

        if (!$accessToken) {
            return response()->json(['message' => 'Token không hợp lệ.'], 401);
        }

        if ($accessToken->expires_at && $accessToken->expires_at->isPast()) {
            $accessToken->delete();
            return response()->json(['message' => 'Token đã hết hạn, vui lòng đăng nhập lại.'], 401);
        }

        Auth::login($accessToken->tokenable);

        return $next($request);
    }
}
