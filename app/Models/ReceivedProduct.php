<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceivedProduct extends Model
{
    protected $fillable = [
        'receipt_id', 'product_id', 'stock', 'stock_defective'
    ];

    public function receipt()
    {
        return $this->belongsTo('App\Models\Receipt');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }
}
