<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = Category::all();
        //HEADAR => accept: application/json
        if ($request->expectsJson()) {
            return response()->json(['status' => true, 'message' => 'Success', 'data' => $data], Response::HTTP_OK);
        }
        return response()->view('cms.categories.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response()->view('cms.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $validator = Validator($request->all(),[
        //     'name'=>'required|string|min:3|max:30',
        //     'info'=>'required|string|max:150',
        //     'active'=>'nullable|string|in:on',
        // ]);

        $request->validate([
            'name' => 'required|string|min:3|max:30',
            'info' => 'required|string|max:50',
            'active' => 'nullable|string|in:on',
        ]);

        // Eloquent
        $category = new Category();
        $category->name = $request->input('name');
        $category->info = $request->input('info');
        $category->active = $request->has('active');
        $saved = $category->save();

        // return redirect()->back();
        return redirect()->route('categories.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::findOrFail($id);
        return response()->json(['status' => true, 'message' => 'Success', 'object' => $category], Response::HTTP_OK);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $category = Category::findOrFail($id);
        return response()->view('cms.categories.update', ['category' => $category]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|min:3|max:30',
            'info' => 'required|string|max:50',
            'active' => 'nullable|string|in:on',
        ]);

        // Eloquent
        $category = Category::findOrFail($id);
        $category->name = $request->input('name');
        $category->info =  $request->input('info');
        $category->active = $request->has('active');
        $updated = $category->save();

        return redirect()->route('categories.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Eloquent
        $category = Category::findOrFail($id);
        $deleted = $category->delete();
        return redirect()->back();
    }
}
