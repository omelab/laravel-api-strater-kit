<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $permissions = Permission::all();

        return Response(['permissions'=> $permissions], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): Response
    {
        $validated = $request->validate([ 
            'name' => 'required|string|unique:permissions|max:255', 
            'guard_name' => 'required|string|max:255', 
            'module_id' => 'required', 
        ]);
      
        $permission = Permission::create($validated);

        return Response(['permission'=> $permission], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): Response
    {
        $permission = Permission::findOrFail($id);
        return Response(['permission'=> $permission], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): Response
    {
        $validated = $request->validate([ 
            'name' => 'required|string|max:255|unique:permissions,id,'.$id,
            'guard_name' => 'required|string|max:255', 
            'module_id' => 'required', 
        ]);
      
        $permission = Permission::findOrFail($id);
        $permission->name = $request->name;
        $permission->guard_name = $request->guard_name;
        $permission->module_id = $request->module_id;

        $permission->save();

        return Response(['permission'=> $permission], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): Response
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();
        return Response(['message'=> 'Permission Deleted Successfully'], 200);
    }
}
