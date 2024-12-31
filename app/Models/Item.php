<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;
    protected $fillable = ['name','description','image','description','item_type_id'];
    public function brands()
    {
        return $this->belongsToMany(Brand::class,'brand_item');
    }
    public function itemType(){
        return $this->belongsTo(ItemType::class,'item_type_id');
    }
    public function itemStock()
    {
        return $this->hasMany(ItemStock::class, 'item_id');
    }

}
