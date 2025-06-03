<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Transaction extends Model
{
    protected $table = 'transactions';
    protected $fillable = [
        'title',
        'reference',
        'amount',
        'payment_method_id',
        'type',
        'client_id',
        'user_id',
        'sale_id',
        'provider_id',
        'transfer_id'
    ];

    public function method()
    {
        return $this->belongsTo('App\Models\PaymentMethod', 'payment_method_id');
    }

    public function provider()
    {
        return $this->belongsTo('App\Models\Provider');
    }

    public function sale()
    {
        return $this->belongsTo('App\Models\Sale');
    }

    public function client()
    {
        return $this->belongsTo('App\Models\Client');
    }

    public function transfer()
    {
        return $this->belongsTo('App\Models\Transfer');
    }

    public function scopeThisYear($query)
    {
        return $query->whereYear('created_at', Carbon::now()->year);
    }

    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    public function scopeExpense($query)
    {
        return $query->whereIn('type', ['expense', 'payment']);
    }
}
