<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded = [];
    public static function getSelectOptions()
    {
        $options = self::select('id','title')->where('status',1)->orderBy('sort','desc')->get();
        $selectOption = [];
        foreach ($options as $option){
            $selectOption[$option->id] = $option->title;
        }
        return $selectOption;
    }
}
