<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Sale extends Model
{
    protected $table = 'sales';
    protected $fillable = [
        'client_id',
        'user_id'
    ];

    public function client()
    {
        return $this->belongsTo('App\Models\Client');
    }

    public function transactions()
    {
        return $this->hasMany('App\Models\Transaction');
    }

    public function products()
    {
        return $this->hasMany('App\Models\SoldProduct');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', Carbon::now()->month);
    }

    public function scopeThisYear($query)
    {
        return $query->whereYear('created_at', Carbon::now()->year);
    }
}
