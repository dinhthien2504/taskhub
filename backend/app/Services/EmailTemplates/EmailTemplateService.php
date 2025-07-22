<?php

namespace App\Services\EmailTemplates;

use App\Repositories\EmailTemplateEloquentRepository;

class EmailTemplateService
{
    protected $repo;

    public function __construct(EmailTemplateEloquentRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getAll($request)
    {
        $status = $request->input('status');

        if ($status === 'active') {
            $isActive = true;
        } elseif ($status === 'inactive') {
            $isActive = false;
        } else {
            $isActive = null;
        }
        $search = $request->input('search') ?? null;
        $perPage = $request->input('per_page') ?? null;
        $email_tempaltes = $this->repo->getAll([], $search, $perPage, $isActive);
        $email_tempaltes->load('creator');
        return $email_tempaltes;
    }

    public function create(array $data)
    {
        $user = auth()->user();
        if ($user) {
            $data['created_by'] = $user->id;
        }

        $email_template = $this->repo->create($data);
        $email_template->load('creator');
        return $email_template;
    }

    public function update($id, array $data)
    {
        $email_tempalate = $this->repo->update($id, $data);
        $email_tempalate->load('creator');
        return $email_tempalate;
    }

    public function delete($id)
    {
        return $this->repo->delete($id);
    }

    public function getDeletedTemplates()
    {
        return $this->repo->getDeletedTemplates();
    }

    public function restore($id)
    {
        $template = $this->repo->findWithTrashed($id);
        if (!$template) {
            throw new \Exception("Template không tồn tại.");
        }
        $template->restore();
        return $template->toArray();
    }

}
