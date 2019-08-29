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
}
