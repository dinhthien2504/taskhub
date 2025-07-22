<?php

namespace App\Services\Campaigns;

use App\Jobs\SendCampaignJob;
use App\Repositories\CampaignEloquentRepository;
use Log;

class CampaignService
{
    protected $campaignRepository;

    public function __construct(CampaignEloquentRepository $campaignRepository)
    {
        $this->campaignRepository = $campaignRepository;
    }

    public function getCampaigns($request)
    {
        $search = $request->input('search') ?? null;
        $perPage = $request->input('per_page') ?? 10;
        $month = $request->input('month') ?? null;
        $year = $request->input('year') ?? null;
        $status = $request->input('status') ?? null;
        $campaigns = $this->campaignRepository->getAllCampaigns(
            with: [],
            search: $search,
            perPage: $perPage,
            month: $month,
            year: $year,
            status: $status
        );
        $campaigns->load('creator');
        return $campaigns;
    }

    public function createCampaign($request)
    {
        $data = [];

        if ($request->has('name')) {
            $data['name'] = $request->name;
        }
        if ($request->has('description')) {
            $data['description'] = $request->description;
        }
        if ($request->has('start_date')) {
            $data['start_date'] = $request->start_date;
        }
        if ($request->has('end_date')) {
            $data['end_date'] = $request->end_date;
        }
        if ($request->has('status')) {
            $data['status'] = $request->status;
        }
        if ($request->has('scheduled_at')) {
            $data['scheduled_at'] = $request->scheduled_at;
        }
        if ($request->has('template_id')) {
            $data['template_id'] = $request->template_id;
        }
        if (auth()->check()) {
            $data['created_by'] = auth()->id();
        }
        $campaign = $this->campaignRepository->create($data);
        $campaign->load('creator');
        return $campaign;
    }

    public function updateCampaign($request, $id)
    {
        $campaign = $this->campaignRepository->find($id);
        if (!$campaign) {
            throw new \Exception('Campaign not found');
        }

        $data = [];
        if ($request->has('name')) {
            $data['name'] = $request->name;
        }
        if ($request->has('description')) {
            $data['description'] = $request->description;
        }
        if ($request->has('start_date')) {
            $data['start_date'] = $request->start_date;
        }
        if ($request->has('end_date')) {
            $data['end_date'] = $request->end_date;
        }
        if ($request->has('status')) {
            $data['status'] = $request->status;
        }
        if ($request->has('scheduled_at')) {
            $data['scheduled_at'] = $request->scheduled_at;
        }

        $campaign->update($data);
        $campaign->load('creator');
        return $campaign;
    }

    public function deleteCampaign($id)
    {
        $campaign = $this->campaignRepository->find($id);
        if (!$campaign) {
            throw new \Exception('Campaign not found');
        }
        $campaign->delete();
    }

    public function getDeletedCampaigns()
    {
        return $this->campaignRepository->getDeletedCampaigns();
    }

    public function restoreCampaign($id): array
    {
        $campaign = $this->campaignRepository->findWithTrashed($id);
        if (!$campaign) {
            throw new \Exception("Campaign not found.");
        }
        $campaign->restore();
        return $campaign->toArray();
    }

    public function sendCampaign($id)
    {
        $campaign = $this->campaignRepository->find($id);

        if (!$campaign) {
            throw new \Exception('Chiến dịch không tồn tại.');
        }
        if ($campaign->status !== 'pending') {
            throw new \Exception('Chiến dịch đã được gửi hoặc không hợp lệ để gửi lại.');
        }
        $campaign->update(['status' => 'sending']);
        SendCampaignJob::dispatch($campaign);

        return $campaign;
    }
}