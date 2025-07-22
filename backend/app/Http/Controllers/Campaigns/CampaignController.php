<?php
namespace App\Http\Controllers\Campaigns;

use App\Http\Requests\Campaigns\StoreCampaignRequest;
use App\Http\Requests\Campaigns\UpdateCampaignRequest;
use App\Http\Resources\Campaigns\CampaignResource;
use App\Models\Campaign;
use App\Models\User;
use App\Services\Campaigns\CampaignService;
use Illuminate\Http\Request;


class CampaignController
{
    protected $campaignService;

    public function __construct(CampaignService $campaignService)
    {
        $this->campaignService = $campaignService;
    }

    public function index(Request $request)
    {
        try {
            $campaigns = $this->campaignService->getCampaigns($request);
            return CampaignResource::collection($campaigns);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lấy chiến dịch không thành công.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function store(StoreCampaignRequest $request)
    {
        try {
            $campaign = $this->campaignService->createCampaign($request);
            return new CampaignResource($campaign);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Thêm chiến dịch thất bại.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function update(UpdateCampaignRequest $request, $id)
    {
        try {
            $campaign = $this->campaignService->updateCampaign($request, $id);
            return new CampaignResource($campaign);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Cập nhật chiến dịch thất bại.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->campaignService->deleteCampaign($id);
            return response()->json([
                'message' => 'Xóa chiến dịch thành công.'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Xóa chiến dịch thất bại.',
                'error' => $th->getMessage()
            ]);
        }
    }

    public function getCampaignsTrash()
    {
        try {
            $deletedCampaigns = $this->campaignService->getDeletedCampaigns();
            return CampaignResource::collection($deletedCampaigns)->resolve();
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lấy dữ liệu thất bại.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function restoreCampaign($id)
    {
        try {
            $this->campaignService->restoreCampaign($id);
            return response()->json([
                'message' => 'Chiến dịch đã được khôi phục thành công.'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Chiến dịch khôi phục thất bại.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function send($id)
    {
        try {
            $campaign = $this->campaignService->sendCampaign($id);
            return new CampaignResource($campaign);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi khi gửi chiến dịch',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}