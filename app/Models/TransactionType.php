<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionType extends Model
{
    protected $table = 'transaction_types';
    protected $fillable = ['type', 'description'];

    public function transactions()
    {
        return $this->hasMany('App\Models\Transaction');
    }
}
