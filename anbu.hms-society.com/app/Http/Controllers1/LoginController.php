<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use DB;

use Carbon\Carbon;


class LoginController extends Controller
{
   public function login_form() {

     return view('auth.login');

   }

  public function register(Request $request)
{
    $data = [
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ];

    // Insert and get inserted ID
    $id = DB::table('user')->insertGetId($data);

    // Fetch the user model (needed for Auth::login)
    $user = User::find($id);

    // Login
    Auth::login($user);

    return redirect('/dashboard')->with('success', 'Registered & Logged in!');
}

    public function register_form() {

        return view('auth.register');
    }

      public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
    
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard')->with('success', 'Logged in successfully!');
        }
    
        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ]);
    }

    // ✅ Logout Function
   public function logout(Request $request)
{
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/'); // Redirect to login page
}

public function dashboard()
{    
      
    return view('dashboard');
}




}
