<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\TraitModel;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,HasRoles,TraitModel;

    use Notifiable;

    protected $guard = 'web';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'title',
        'ti_status',
        'phone_number',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    public function hasLeads(){
        return $this->hasMany(Leads::class,'team_id','id');
    }

    public function getLeads(){
        return $this->hasLeads;
    }

    public static function totalTeam(){
       return self::with("roles")->whereHas("roles", function($q) {
            $q->whereIn("name", ["user"]);
        })->count();
    }
}
