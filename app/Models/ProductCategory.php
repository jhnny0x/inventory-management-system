<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class ProductCategory extends Model
{
    use SoftDeletes;

    protected $table = 'product_categories';
    protected $fillable = ['name'];

    public function products()
    {
        return $this->hasMany('App\Models\Product');
    }

    public function scopeThisYear($query)
    {
        return $query->whereYear('created_at', Carbon::now()->year);
    }
}
