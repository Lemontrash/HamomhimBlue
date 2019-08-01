<?php

namespace App\Http\Controllers;

use App\AboutUs;
use App\Category;
use App\HowItWorksArchitect;
use App\HowItWorksWorker;
use App\MainPage;
use App\PrivacyPolicy;
use App\Project;
use App\Subcategory;
use App\TermsAndConditions;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function getAllCategories(){
        $categories = Category::all();
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

    public function deleteSubcategory(Request $request){
        $id = $request->get('id');
        $subCategory = Subcategory::find($id);

        if(empty($subCategory)){
            return response()->json(['success' => false, 'message' => 'No such subcategory']);
        }

        Subcategory::where('id', $id)->delete();
        return response()->json(['success' => true]);
    }

    public function getAllProjects(){
        $projects = Project::all();
        $projects = SupportControllerCosImLazy::parseProjects($projects);
        return response()->json(['success' => true, 'value' => $projects]);
    }

    public function getAllComments(){

    }
}
