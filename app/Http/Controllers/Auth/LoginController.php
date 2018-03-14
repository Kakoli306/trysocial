<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Socialite;
use App\User;
use App\SocialProvider;
use Auth;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function redirectToProvider()
    {
        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback()
    {
      //  $user = Socialite::driver('facebook')->user();

        // $user->token;

      //  return $user->getEmail();

      try{
        $userinfo = Socialite::driver('facebook')->user();
        }
      catch(\Exception $e){
        return redirect('/');
      }

      $socialProvider = SocialProvider::where('provider_id',$userinfo->getId())->first();

      if(!$socialProvider){

        $user = User::firstOrCreate(

          ['email' => $userinfo->getEmail()],
          ['name' => $userinfo->getName()]

        );

        $user->socialProvider()->create([
          'provider_id'=> $userinfo->getId(),
          'provider'=> 'facebook'
        ]);
      }

      else

        $user = $socialProvider->user;

        auth()->login($user);

        return redirect('/home');
      }
    }
