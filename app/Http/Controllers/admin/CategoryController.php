<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\AboutUs;
use App\Category;
use App\HowItWorksArchitect;
use App\HowItWorksWorker;
use App\MainPage;
use App\Order;
use App\Post;
use App\PrivacyPolicy;
use App\Project;
use App\Role;
use App\Subcategory;
use App\TermsAndConditions;
use App\User;
use App\UserRating;
use App\Http\Controllers\SupportControllerCosImLazy;


class CategoryController extends Controller
{
    public function getAllCategories(){

        $categories = Category::all();
        if ($categories->isEmpty()){
            return response()->json(['success' => true, 'value' => []]);
        }
        $categories = SupportControllerCosImLazy::parseCategories($categories);
        return response()->json(['success' => true, 'value' => $categories]);
    }

    public function deleteCategory(Request $request){
        $id = $request->get('id');
        $category = Category::find($id);

        if(empty($category)){
            return response()->json(['success' => false, 'message' => 'No such category']);
        }
        Subcategory::where('category_id', $id)->delete();
        Category::where('id', $id)->delete();
        return response()->json(['success' => true]);
    }

    public function addNewCategory(Request $request){
        $categoryName = $request->get('categoryName');
        $image = $request->file('image');
        $image = json_decode(FileController::uploadPicture('categoryImage', $image));
        $category = Category::create([
            'name' => $categoryName,
            'image' => $image->file
        ]);
        $subcategories = $request->get('subcategories');
        if (isset($subcategories)){
            foreach ($subcategories as $subcategory) {
                Subcategory::create([
                    'name' => $subcategory,
                    'category_id' => $category->id,
                ]);
            }
        }
        $category = SupportControllerCosImLazy::parseCategories($category);
        return response()->json(['success' => true, 'value' => $category]);
    }

    public function deleteSubcategory(Request $request){
        $id = $request->get('id');
        $subCategory = Subcategory::find($id);

        if(empty($subCategory)){
            return response()->json(['success' => false, 'message' => 'No such subcategory']);
        }

        Subcategory::where('id', $id)->delete();
        return response()->json(['success' => true]);
    }
    
    public function editCategory(Request $request){
        $id = $request->get('categoryId');
        $category = Category::find($id);
        if (empty($category)){
            return response()->json(['success' => false, 'message' => 'no such category']);
        }

        $category->name  = $request->categoryName;
        if (!empty($request->file('image'))){
            $file = FileController::uploadPicture('categoryImage', $request->file('image'));
            $category->image = $file->file;
        }
        $category->save();
        return response()->json(['success' => true]);
    }

    public function getSingleCategory(Request $request){

         $id = $request->input('id');
        $record = Category::find($id);
        if (empty($record)){
            return response()->json(['success' => false, 'message' => 'no such category']);
        }
        $data = SupportControllerCosImLazy::parseCategory($record);

        return response()->json(['success' => true, 'value' => $data]);
    }
}
