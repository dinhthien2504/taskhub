<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\UnsubscribeBirthdateService;
use Illuminate\Http\Request;

class UnsubscribeBirthdateController extends Controller
{
    protected $unsubscribeService;

    public function __construct(UnsubscribeBirthdateService $unsubscribeService)
    {
        $this->unsubscribeService = $unsubscribeService;
    }

    public function unsubscribe_birthdate(Request $request)
    {
        try {
            $request->validate(rules: [
                'user_id' => 'required|exists:users,id',
            ]);

            $this->unsubscribeService->unsubscribeBirthdayEmail($request->user_id);

            return response()->json([
                'message' => 'Đã hủy đăng ký email sinh nhật thành công.'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Có lỗi xảy ra. Vui lòng thử lại.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

}
