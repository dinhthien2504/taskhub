<?php
namespace App\Http\Controllers\Campaigns;


use App\Http\Requests\Campaigns\AssignUsersToCampaignRequest;
use App\Http\Resources\Campaigns\CampaignUserAssignResource;
use App\Models\Campaign;
use App\Services\Campaigns\CampaignUserService;

class CampaignUserController
{
    protected $campaignUserService;

    public function __construct(CampaignUserService $campaignUserService)
    {
        $this->campaignUserService = $campaignUserService;
    }

    public function index(Campaign $campaign)
    {
        try {
            $assignedUsers = $this->campaignUserService->getAssignedUsers($campaign);

            return CampaignUserAssignResource::collection($assignedUsers);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Không thể lấy danh sách người dùng đã gán.',
                'error' => $th->getMessage()
            ], 500);
        }
    }


    public function assign(AssignUsersToCampaignRequest $request, $campaignId)
    {
        try {
            $this->campaignUserService->assignUsers($campaignId, $request->user_ids);
            return response()->json([
                'message' => 'Gán người dùng vào chiến dịch thành công.',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Gán user vào campaign thất bại.',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}