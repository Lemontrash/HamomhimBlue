<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function searchByName(Request $request){
        $name = $request->get('name');

        $categories = Category::where('name', $name)->orWhere('name', 'like', '%'.$name.'%')->get();
        if ($categories->isEmpty()){
            return response()->json(['success' => false, 'message' => 'nothing found'], 202);
        }else{
            foreach ($categories as $key => $category) {
                $data[$key]['id'] = $category->id;
                $data[$key]['value'] = $category->name;
                $data[$key]['image'] = $category->image;
            }
            return response()->json($data, 200);
        }
    }

    public function getAllCategories(){
        $categories = Category::all();

        foreach ($categories as $key => $category) {
            $data[$key]['id'] = $category->id;
            $data[$key]['name'] = $category->name;
            $data[$key]['image'] = $category->image;
        }
    }
}
