<?php

namespace App\Repositories;

use App\Models\WorkingTime;


class WorkingTimeEloquentRepository extends EloquentRepository
{
    public function getModel()
    {
        return WorkingTime::class;
    }

    public function upsert(array $data)
    {
        $record = $this->_model::first();

        if ($record) {
            $record->update($data);
        } else {
            $record = $this->_model::create($data);
        }

        return $record;
    }

    public function getWorkingTime()
    {
        return $this->_model::first();
    }
}