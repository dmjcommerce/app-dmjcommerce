<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class address extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 
        'first_name', 
        'last_name', 
        'phone', 
        'street_address',
        'city',
        'zip_code',
    ];

    public function order(){
        return $this->belongsTo(order::class);
    }

    public function getFullNameAttribute(){
        return "{$this->first_name} {$this->last_name}";
    }

}
