<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];
    protected $casts = [
        'address'   => 'json',
        'ship_data' => 'json',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->no) {
                $model->no = static::findAvailableNo();
                if (!$model->no) return false;
            }
        });
    }

    public static function findAvailableNo()
    {
        $prefix = date('YmdHis');
        for ($i = 0; $i < 10; $i++) {
            $no = $prefix . str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            if (!static::where('no', $no)->exists()) {
                return $no;
            }
        }

        return false;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public static function express()
    {
        return [
            'yuantong' => '圆通',
            'yunda' => '韵达'
        ];
    }
}
