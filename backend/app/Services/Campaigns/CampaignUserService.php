<?php

namespace App\Services\Campaigns;

use App\Repositories\CampaignEloquentRepository;

class CampaignUserService
{
    protected $campaignRepository;

    public function __construct(CampaignEloquentRepository $campaignRepository)
    {
        $this->campaignRepository = $campaignRepository;
    }

    public function getAssignedUsers($campaign)
    {
        return $campaign->users()->get();
    }

    public function assignUsers($campaignId, array $userIds)
    {
        $campaign = $this->campaignRepository->find($campaignId);
        if (!$campaign) {
            throw new \Exception('Campaign not found');
        }
        $campaign->users()->sync($userIds);
        return $campaign->load('users');
    }

}