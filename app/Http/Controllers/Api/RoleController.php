<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $roles = Role::all();

        return Response(['roles'=> $roles], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): Response
    {
        $validated = $request->validate([ 
            'name' => 'required|string|unique:roles|max:255',  
            'permissions' => 'required|array', 
        ]);

        $role = new Role();
        $role->name = $request->name;
        
        if($role->save()){
            $role->syncPermissions($request->permissions); 
            return Response(['role'=> $role], 201);
        }
         
        return Response(['message'=> 'Something went wrong!'], 402);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): Response
    {
        $role = Role::findOrFail($id);
        return Response(['role'=> $role], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): Response
    { 
        $validated = $request->validate([ 
            'name' => 'required|string|max:255|unique:roles,id,'.$id,  
            'permissions' => 'required|array', 
        ]);

        $role = Role::findOrFail($id);
        $role->name = $request->name;
        
        if($role->save()){
            $role->syncPermissions($request->permissions); 
            return Response(['role'=> $role], 201);
        }
         
        return Response(['message'=> 'Something went wrong!'], 402); 
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): Response
    {
        $role = Role::findOrFail($id);
        $role->delete();
        return Response(['message'=> 'Role Deleted Successfully'], 200);
    }
}
