<?php

namespace App\Http\Controllers\CheckIns;

use App\Http\Controllers\Controller;
use App\Http\Resources\CheckIns\CheckInResource;
use App\Services\CheckIns\CheckInService;
use Illuminate\Http\Request;

class CheckInController extends Controller
{
    protected $checkIn_service;

    public function __construct(CheckInService $checkIn_service)
    {
        $this->checkIn_service = $checkIn_service;
    }

    public function index(Request $request) 
    {
        try {
            $response = $this->checkIn_service->getCheckInLogs($request);
            return CheckInResource::collection($response);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi lấy log.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function getTodayLog()
    {
        try {
            $response = $this->checkIn_service->getTodayLog();
            return new CheckInResource($response);
        } catch (\Throwable $th) {
            return response()->json([
                'messsage' => 'Lấy log thất bại.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function checkIn(Request $request)
    {
        try {
            $response = $this->checkIn_service->checkIn();
            return new CheckInResource($response);
        } catch (\Throwable $th) {
            return response()->json([
                'messsage' => 'Check in thất bại.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function checkOut(Request $request)
    {
        try {
            $response = $this->checkIn_service->checkOut();
            return new CheckInResource($response);
        } catch (\Throwable $th) {
            return response()->json([
                'messsage' => 'Check out thất bại.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function exportCSV(Request $request)
    {
        try {
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="check_in_logs.csv"',
            ];
            $callback = $this->checkIn_service->exportCsv($request);
            return response()->stream($callback, 200, $headers);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'export thất bại.',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
