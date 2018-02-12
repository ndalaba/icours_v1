<?php namespace App\Http\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

    use Authenticatable, CanResetPassword;

    protected $table = 'users';
    protected $fillable = ['name', 'email', 'password', 'phone', 'fonction','droit'];
    protected $hidden = ['password', 'remember_token'];

    public static $rules = ['name' => 'required', 'email' => 'required|email'];

    public function getFonctionAttribute($value) {
        if ($this->droit == 1)
            return "Lecteur";
        if ($this->droit == 5)
            return "Editeur";
        if ($this->droit == 10)
            return "Administrateur";
    }
}
