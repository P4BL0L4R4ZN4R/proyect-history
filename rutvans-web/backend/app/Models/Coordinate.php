<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Coordinate extends Model
{
    use HasFactory;

    protected $table = 'coordinators';
    
    protected $fillable = [
        'user_id', 
        'employee_code', 
        'photo_path',
        'site_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    // Scope para filtrar por site
    public function scopeBySite($query, $siteId)
    {
        return $query->where('site_id', $siteId);
    }
}
