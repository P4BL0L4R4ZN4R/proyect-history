<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cashier extends Model
{
    use HasFactory;


    // Tabla explícita, aunque Laravel infiere 'drivers' bien
    protected $table = 'cashiers';
    
    protected $fillable = [
        'user_id', 
        'employee_code', 
        'photo_path',
        'site_id'
    ];

    /**
     * Relación con User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    /**
     * Relación con Site
     */
    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    // Scope para filtrar por site
    public function scopeBySite($query, $siteId)
    {
        return $query->where('site_id', $siteId);
    }
}
