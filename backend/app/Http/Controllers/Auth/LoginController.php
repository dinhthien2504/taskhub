<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\LoginResource;
use App\Services\Auth\LoginService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    protected $loginService;

    public function __construct(LoginService $loginService)
    {
        $this->loginService = $loginService;
    }

    public function store(LoginRequest $request)
    {
        try {
            $request->authenticate();

            $user = $request->user();

            $loginData = $this->loginService->login($user, $request);

            return (new LoginResource($loginData));
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Đăng nhập thất bại.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Đăng nhập thất bại.',
                'error' => $th->getMessage()
            ], 500);
        }

    }


    public function destroy(Request $request)
    {
        $this->loginService->logout($request->user());

        return response()->json(['message' => 'Đăng xuất thành công'], 200);
    }
}
