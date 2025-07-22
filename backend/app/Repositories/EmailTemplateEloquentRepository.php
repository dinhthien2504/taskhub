<?php

namespace App\Repositories;

use App\Models\EmailTemplate;

class EmailTemplateEloquentRepository extends EloquentRepository
{
    public function getModel()
    {
        return EmailTemplate::class;
    }

    public function getAll($with = [], ?string $search = null, int $perPage = null, $isActive = null)
    {
        $query = $this->_model::with($with);

        if (!is_null($isActive)) {
            $query->where('is_active', (bool) $isActive);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%");
            });
        }

        if($perPage) {
            return $query->orderBy('created_at', 'desc')->paginate($perPage);
        } else {
            return $query->orderBy('created_at', 'desc')->get();    
        }
       
    }

    public function find($id)
    {
        return $this->_model::findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->_model::create($data);
    }

    public function update($id, array $data)
    {
        $template = $this->find($id);
        $template->update($data);
        return $template;
    }

    public function delete($id)
    {
        $template = $this->find($id);
        return $template->delete();
    }

    public function getDeletedTemplates()
    {
        return $this->_model::onlyTrashed()->orderBy('deleted_at', 'desc')->get();
    }
}
