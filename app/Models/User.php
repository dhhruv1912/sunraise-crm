<?php
namespace App\Models;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Settings;

class User extends Authenticatable
{
    use HasRoles;
    use Notifiable;

    protected $casts = [
        'company_access' => 'array',
        'email_verified_at' => 'datetime',
    ];

    protected $fillable = [
        'fname','lname','email','mobile','password','role','status','email_verified_at','remember_token','company_access','reset_token'
    ];

    public function getRole($id = false){
        $role = Settings::getValue('user_roles');
        // dd($role);
        // $role = [
        //     1 => "Admin",
        //     2 => "Devloper",
        //     3 => "CMO",
        //     4 => "Marketing Head",
        //     5 => "Marketing Exicutive",
        //     6 => "Project Head",
        //     7 => "Project Superviser",
        //     8 => "Lisoner",
        //     9 => "Site Engineer",
        // ];
        if($id){
            return isset($role[$id]) ? $role[$id] : "";
        }else{
            return $role;
        }
    }

    protected $hidden = ['password', 'remember_token'];
}
