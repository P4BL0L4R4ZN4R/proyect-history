<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
    use HasFactory;

    protected $table = "sales";
    protected $primaryKey = "id";

    protected $fillable = [
        'folio',
        'user_id',
        'payment_id',
        'route_unit_schedule_id',
        'rate_id',
        'data',
        'amount',
        'status',
        'site_id'
    ];

    protected $casts = [
        'data' => 'array',
        'amount' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function routeUnitSchedule()
    {
        return $this->belongsTo(RouteUnitSchedule::class);
    }

    public function rate()
    {
        return $this->belongsTo(Rate::class);
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
