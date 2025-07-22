<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\VerifyEmailService;
use Illuminate\Http\Request;


class VerifyEmailController extends Controller
{
    protected $verifyEmailService;

    public function __construct(VerifyEmailService $verifyEmailService)
    {
        $this->verifyEmailService = $verifyEmailService;
    }

    public function __invoke(Request $request, $id, $hash)
    {
        $verificationResponse = $this->verifyEmailService->verify(
            $id,
            $hash,
            $request
        );

        return response()->json($verificationResponse, $verificationResponse['code']);
    }
}
