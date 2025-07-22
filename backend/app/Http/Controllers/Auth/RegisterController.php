<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\Auth\RegisterResource;
use App\Services\Auth\RegisterService;
class RegisterController extends Controller
{
    protected $registerService;

    public function __construct(RegisterService $registerService)
    {
        $this->registerService = $registerService;
    }

    public function store(RegisterRequest $request)
    {
        try {
            $registerData = $this->registerService->register($request);
            
            return (new RegisterResource($registerData))
                ->response()
                ->setStatusCode(201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Đăng ký thất bại.',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
