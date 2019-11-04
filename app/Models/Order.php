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

    public function getShipDataAttribute($value)
    {
        $arr = json_decode($value,true);

        $arr['express_company']=isset($arr['express_company']) ? $arr['express_company'] : '';
        if(array_key_exists('express_company',$arr) &&  $arr['express_company'] != '' && request()->route()->getPrefix() != 'admin'){
            $arr['express_company'] = self::express()[$arr['express_company']];
        }
        return $arr;
    }

    public static function express()
    {
        return [
            'yuantong' => '圆通快递',
            'yunda' => '韵达快递',
            'zhongtong' => '中通快递',
            'shunfeng' => '顺丰速运',
            'huitongkuaidi' => '百世快递',
            'shentong' => '申通快递',
            'ems' => 'ems',
            'tiantian' =>'天天快递',
            'debangwuliu' => '德邦物流',
            'debangkuaidi' => '德邦快递'
        ];
    }
}
