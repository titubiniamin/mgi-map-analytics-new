<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'designation_id',
        'department_id',
        'zone_id',
        'district_id',
        'office_id',
        'blood_group_id',
        'employee_id',
        'brand_id'

    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }


    public function get_roles()
    {
        $roles = [];
        foreach ($this->getRoleNames() as $key => $role) {
            $roles[$key] = $role;
        }

        return $roles;
    }

    public function designation(){
        return $this->belongsTo(Designation::class);
    }
    public function department(){
        return $this->belongsTo(Department::class);
    }
    public function zone(){
        return $this->belongsTo(Zone::class);
    }
    public function district(){
        return $this->belongsTo(District::class);
    }
    public function office(){
        return $this->belongsTo(Office::class);
    }
    public function bloodGroup(){
        return $this->belongsTo(BloodGroup::class);
    }
    public function brand(){
        return $this->belongsTo(Brand::class);
    }

}
