<?php


namespace App\Traits;


trait Tree
{
    public function parents($data,$pid)
    {
        static $tree = [];

        foreach($data as $key => $value) {
            if($value['id'] == $pid) {
                $tree[] = $value;

                $this->parents($data , $value['parent_id']);
            }
        }
        return $tree;
    }
}
