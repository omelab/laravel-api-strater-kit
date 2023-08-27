<?php

namespace App\Http\Controllers\Api;

use App\Models\Module;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */ 
    public function index(Request $request): Response
    {
        $modules =  Module::all();
        return Response(['modules'=> $modules], 200);
    }

    /**
     * create modules
     */
    public function store(Request $request): Response
    {
        $validated = $request->validate([ 
            'name' => 'required|string|unique:modules|max:255', 
        ]);
      
        $module = Module::create($validated);

        return Response(['module'=> $module], 201);
    }

    /**
     * update modules
     */
    public function update(Request $request, $id): Response
    {
        $validated = $request->validate([ 
            'name' => 'required|string|unique:modules,'.$id,
        ]);
      
        $module = Module::find($id);
        $module->name = $request->name;
        $module->save();

        return Response(['module'=> $module], 201);
    }

 
    /**
     * Display the specified resource.
     */
    public function show($id): Response
    {
        $module = Module::findOrFail($id);
        return Response(['module'=> $module], 200);
    }
 

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): Response
    {
        $module = Module::findOrFail($id);
        $module->delete();

        return Response(['message'=> 'Module deleted successfully'], 200);
    }
}
