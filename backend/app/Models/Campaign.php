<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campaign extends Model
{
    use SoftDeletes, HasFactory;
    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'status',
        'scheduled_at',
        'created_by',
        'template_id'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'campaign_user');
    }

   public function template()
    {
        return $this->belongsTo(EmailTemplate::class, 'template_id')->withTrashed();
    }


}
