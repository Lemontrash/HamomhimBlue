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
            $data = SupportControllerCosImLazy::parseCategories($categories);
            return response()->json($data, 200);
        }
    }

    public function getAllCategories(){
        $categories = Category::all();
        dd($categories);
        
        $data = SupportControllerCosImLazy::parseCategories($categories);
        return response()->json($data);
    }
}
