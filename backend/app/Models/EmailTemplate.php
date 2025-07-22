<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class EmailTemplate extends Model
{
    use SoftDeletes, HasFactory;
    protected $fillable = [
        'name',
        'subject',
        'content',
        'description',
        'type',
        'is_active',
        'created_by',
        'deleted_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function campaigns()
    {
        return $this->hasMany(Campaign::class, 'template_id');
    }
}
