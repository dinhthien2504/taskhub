<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\EmailVerificationService;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    protected $emailVerificationService;

    public function __construct(EmailVerificationService $emailVerificationService)
    {
        $this->emailVerificationService = $emailVerificationService;
    }

    public function store(Request $request)
    {
        $verificationStatus = $this->emailVerificationService->sendVerification($request->user());

        return response()->json(['status' => $verificationStatus['message']]);
    }
}
