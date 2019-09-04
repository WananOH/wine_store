<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

    public function setImagesAttribute($images)
    {
        if (is_array($images)) {
            $this->attributes['images'] = json_encode($images);
        }
    }

    public function getImagesAttribute($images)
    {
        return json_decode($images, true);
    }

    public function scopeFilter($query)
    {
        $query->where('status',1)
            ->when(request('keyword'),function ($query){
                $query->where('title','like','%'.request('keyword').'%');
            })
            ->when(request('category_id'),function ($query){
                $query->where('category_id',request('category_id'));
            })
            ->paginate(10);
    }


    public function decreaseStock($amount)
    {
        if ($amount < 0) {
            throw new \Exception('減少的库存量不可小于0');
        }

        return $this->query()
            ->where('id', $this->id)
            ->where('stock', '>=', $amount)
            ->decrement('stock', $amount);
    }

    public function addStock($amount)
    {
        if ($amount < 0) {
            throw new \Exception('增加的库存量不可小于0');
        }

        $this->increment('stock', $amount);
    }
}
