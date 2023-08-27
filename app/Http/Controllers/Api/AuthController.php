<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Auth;
use DB; 

class AuthController extends Controller
{
    /**
     * register users
     */ 
    public function register(Request $request): Response
    {   
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users|max:255',
            'password' => 'required|string|min:6|confirmed',
        ]);
      
        $user = User::create(array_merge(
            $validated,
            ['password' => bcrypt($request->password)]
        ));

        return Response(['message'=>'User registered successfully'], 201);  
    }

    /**
     * Store a newly created resource in storage.
     */
    public function login(Request $request): Response
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
  
        if (Auth::attempt($credentials)) {

            config(['auth.guards.api.provider' => 'web']);

            $user = User::select('users.*')->find(Auth::guard('web')->user()->id);
 
            $token = $user->createToken('matri_seba',['user'])->accessToken;
              
            return Response(['access_token'=> $token], 200);
        }

        return Response(['message'=> 'Invalid credentials'], 401); 
    }

    /**
     * Display the specified resource.
     */
    public function profile(Request $request): Response
    {   
        return Response(['user'=>$request->user()],401);

        // if(Auth::guard('api')->check()){   
        //     $user = User::select('id', 'name', 'email')->find(Auth::guard('api')->user()->id);
        //     return Response(['user'=>$user], 200); 
        // }

        // return Response(['message'=>'Un Authorized'],401);
    }

    /**
     * update users profile
     */ 
    public function update(Request $request): Response
    {   
        if(Auth::guard('api')->check()){

            $user = Auth::guard('api')->user();

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            ]);

            $user->name = $request->name;
            $user->email = $request->email;

            if($request->password && $request->password !=''){

                $validated = $request->validate([
                    'password' => 'required|string|min:6|confirmed',
                ]);
    
                $user->password = bcrypt($request->password);
            }
        
            $user->save();

            return Response(['message'=>'Profile update successfully'], 201);  
        }

        return Response(['message'=>'Unauthorized Token'], 401); 
    }

    /**
     * Update the specified resource in storage.
     */
    public function logout(Request $request): Response
    {
        if(Auth::guard('api')->check()){
            $accessToken = Auth::guard('api')->user()->token();
            DB::table('oauth_refresh_tokens')->where('access_token_id', $accessToken->id)->update(['revoked' => true]);
            $accessToken->revoke();
            return Response(['message'=>'Logged out successfully','token'=>null], 200);
        }
        return Response(['message'=>'Unauthorized Token'], 401);
    }
 
}
