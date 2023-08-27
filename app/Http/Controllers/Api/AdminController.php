<?php

namespace App\Http\Controllers\Api;

use App\Models\Admin;
use App\Models\Permission;
use Spatie\Permission\Models\Role;  
use Illuminate\Database\Eloquent\Builder;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response; 
use Auth;
use DB; 

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:admin-list|admin-create', ['only' => ['index','register']]);
        // $this->middleware('permission:admin-create', ['only' => ['register']]);
        $this->middleware('permission:admin-view', ['only' => ['view','profile']]);
        // $this->middleware('permission:admin-update', ['only' => ['update']]);
        // $this->middleware('permission:admin-delete', ['only' => ['destroy']]);
    }

    /**
     * register users
     */ 
    public function index(Request $request): Response
    {
        //get all admin users with roles
        $users = Admin::with('roles')->get();

        // Remove pivot information from each role collection
        $users->transform(function ($user) {
            $user->roles->makeHidden(['pivot']);
            return $user;
        }); 

        return Response(['users'=>$users], 200);  
    }

    /**
     * register users
     */ 
    public function view(Request $request, $id): Response
    {
        return Response(['admins'=>'admin user list'], 200);  
    }
    

    /**
     * update admin user
     */ 
    public function update(Request $request, $id): Response
    {

        $admin = Auth::findOrFail($id);

        if( $admin ){  
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:admins,email,'.$admin->id,
                'roles' => 'required'
            ]);

            $admin->name = $request->name;
            $admin->email = $request->email;
  
            if($request->password && $request->password !=''){

                $validated = $request->validate([
                    'password' => 'required|string|min:6|confirmed',
                ]);
    
                $admin->password = bcrypt($request->password);
            }

            if($request->hasFile('image')) {
                $this->validate($request, [
                    'image' => 'required|image|mimes:jpeg,png,jpg|max:1000',
                ]); 
                $imageName = time().'.'.$request->image->getClientOriginalExtension();
                $request->image->move(public_path('images'), $imageName);
                $admin->picture = $imageName;
            }
        
            $admin->save(); 

            //assign roles
            $user->syncRoles($request->roles);

            return Response(['message'=>'Profile update successfully'], 201);  
        }

        return Response(['message'=>'User not found'], 404); 
    }


    /**
     * register users
     */ 
    public function destroy(Request $request, $id): Response
    {
        return Response(['admins'=>'admin user list'], 200);  
    }


   /**
     * register users
     */ 
    public function register(Request $request): Response
    {   
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:admins|max:255',
            'password' => 'required|string|min:6|confirmed',
            'roles' => 'required'
        ]);

        $admin = new Admin();
        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->password =  bcrypt($request->password);
        $admin->save();
       
        //assign roles
        if(isset($request->roles) && !empty($request->roles)){ 
                
            //get permissions by roles
            $permissions = Permission::whereHas('roles', function (Builder $query) {
                $query->whereIn('name', $request->roles);
            })->pluck('name');
    
            //assign roles
            $admin->syncRoles($request->roles);

            //assign permission
            $admin->syncPermissions($permissions); 
        }

        if($admin->id){
            return Response(['message'=>'User registered successfully'], 201);  
        }
 
        return Response(['message'=>'Unprocessable Entity'], 422);  
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

        if (Auth::guard('admin')->attempt($credentials)) {

            config(['auth.guards.api.provider' => 'admin']);

            $user = Admin::select('admins.*')->find(Auth::guard('admin')->user()->id);
 
            $token = $user->createToken('matri_seba',['admin'])->accessToken;
              
            return Response(['access_token'=> $token], 200);
        }
        return Response(['message'=> 'Invalid credentials'], 401); 
    }


    /**
     * Display the specified resource.
     */
    public function profile(Request $request): Response
    {
        $user = $request->user();

        if($user) {

            $user->roles->makeHidden(['pivot','guard_name','id','created_at','updated_at']);

            $user->permissions->makeHidden(['pivot','module_id','guard_name','id','created_at','updated_at']);

            return Response(['user'=> $user], 200); 
        }
  
        return Response(['message'=>'Un Authorized'],401);
    }

     /**
     * update profile
     */ 
    public function updateProfile(Request $request): Response
    {   
        if(Auth::guard('api-admin')->check()){

            $admin = Auth::guard('api-admin')->user();

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:admins,email,'.$admin->id,
                'roles' => 'required'
            ]);

            $admin->name = $request->name;
            $admin->email = $request->email;
  
            if($request->password && $request->password !=''){

                $validated = $request->validate([
                    'password' => 'required|string|min:6|confirmed',
                ]);
    
                $admin->password = bcrypt($request->password);
            }

            if($request->hasFile('image')) {
                $this->validate($request, [
                    'image' => 'required|image|mimes:jpeg,png,jpg|max:1000',
                ]); 
                $imageName = time().'.'.$request->image->getClientOriginalExtension();
                $request->image->move(public_path('images'), $imageName);
                $admin->picture = $imageName;
            }
        
            $admin->save(); 

            //assign roles
            if(isset($request->roles) && !empty($request->roles)){ 

                //get permissions by roles
                $permissions = Permission::whereHas('roles', function (Builder $query) {
                    $query->whereIn('name', $request->roles);
                })->pluck('name');
        
                //assign roles
                $admin->syncRoles($request->roles);

                //assign permission
                $admin->syncPermissions($permissions); 
            }
 
            return Response(['message'=>'Profile update successfully'], 201);  
        }

        return Response(['message'=>'Unauthorized Token'], 401); 
    }



    /**
     * Update the specified resource in storage.
     */
    public function logout(Request $request): Response
    {
        if(Auth::guard('api-admin')->check()){
            $accessToken = Auth::guard('api-admin')->user()->token();
            DB::table('oauth_refresh_tokens')->where('access_token_id', $accessToken->id)->update(['revoked' => true]);
            $accessToken->revoke();
            return Response(['message'=>'Logged out successfully','token'=>null], 200);
        }
        return Response(['message'=>'Unauthorized Token'], 401);
    }


}
