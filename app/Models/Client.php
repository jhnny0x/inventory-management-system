<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    protected $table = 'clients';
    protected $fillable = [
        'name',
        'email',
        'phone',
        'document_type',
        'document_id'
    ];

    public function sales()
    {
        return $this->hasMany('App\Models\Sale');
    }

    public function transactions()
    {
        return $this->hasMany('App\Models\Transaction');
    }
}
