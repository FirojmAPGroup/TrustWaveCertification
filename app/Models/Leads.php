<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leads extends Model
{
    use HasFactory;
    protected $table = 'leads';
    protected $fillable = [
        'business_id',
        'ti_status',
        'location',
        'remark',
        'selfie'
    ];


    public function hasBusiness(){
        return $this->hasOne(Business::class,'id','business_id');
    }
    public function getBusiness(){
        return $this->hasBusiness;
    }

    public function hasUser(){
        return $this->hasOne(User::class,'id','user_id');
    }
    public function getUser(){
        return $this->hasUser;
    }

    public static function TotalVisits(){
        return self::count();
    }

    public static function completedVisit(){
        return self::where('ti_status',1)->count();
    }

    public static function pendingVisit(){
        return self::where('ti_status',0)->count();
    }
}
