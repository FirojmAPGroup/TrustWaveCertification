<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    use \App\Traits\TraitModel;
    use HasFactory;
    protected $table = "business";
    protected $fillable = [
        'name',
        'owner_name',
        'owner_number',
        'owner_email',
        'ti_status',
        'pincode',
        'city',
        'state',
        'country',
        'latitude',
        'longitude'
    ];
    public function hasLeads()
    {
        return $this->hasMany(Leads::class, 'business_id');
    }
    public function getLeads()
    {
        return $this->hasLeads;
    }
}
