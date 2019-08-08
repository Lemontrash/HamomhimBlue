<?php

namespace App\Http\Controllers;

use App\AboutUs;
use App\Category;
use App\HowItWorksArchitect;
use App\HowItWorksWorker;
use App\MainPage;
use App\Order;
use App\PrivacyPolicy;
use App\Project;
use App\Role;
use App\Subcategory;
use App\TermsAndConditions;
use App\User;
use App\UserRating;
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

    public function addNewCategory(Request $request){
        $categoryName = $request->get('categoryName');
        $image = $request->file('image');
        $image = json_decode(FileController::uploadPicture('categoryImage', $image));
        $category = Category::create([
            'name' => $categoryName,
            'image' => $image['file']
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

    //@TODO сделать сортировку, ордеры и юзеров
    public function getAllProjects(Request $request){
        $sort = $request->get('sort');
        $sortOrder = $request->get('order');
        if($sort == 'projectName'){
            $projects = Project::orderBy('name', $sortOrder)->get();
            $projects = SupportControllerCosImLazy::parseProjects($projects);
            foreach ($projects as $key => $project){
                $order = Order::where('project_id', $project['id'])->get();
                $projects[$key]['orders'] = json_decode(SupportControllerCosImLazy::parseOrder($order));
                $user = User::where('id', $project['user_id'])->get();
                $projects[$key]['user'] = json_decode(SupportControllerCosImLazy::parseUsers($user));
            }
        }

        return response()->json(['success' => true, 'value' => $projects]);
    }


    public function getAllUsers(Request $request){
        $page    = $request->get('page');
        $sortBy  = $request->get('sortBy');
        $orderBy = $request->get('orderBy');
        $take    = $request->get('take');

        if ($orderBy != 'ASC' && $orderBy != 'DESC'){
            return response()->json(['success' => false, 'message' => 'wrong order by']);
        }
        if ($page == 0){
            $offset = 0;
        }else{
            $offset = $page * 20;
        }
        $users = User::take($take)->offset($offset)->orderBy($sortBy, $orderBy)->get();
        if ($page == 1){
            dd($users);
        }
        $userData['users'] = SupportControllerCosImLazy::parseUsers($users);
        $userData['total'] = $this->getUserCounter();

        return response()->json(['success' => true, 'value' => $userData]);
    }

    public function getUserCounter(){
        return  User::count();
    }

    public function deleteUser(Request $request){
        $id = $request->get('userId');
        $user = User::find($id);
        if (empty($user)){
            return response()->json(['success' => false, 'message' => 'bo such user']);
        }
        User::where('id', $id)->delete();
        return response()->json(['success' => true]);

    }

    public function getAllComments(){
        $ratings = UserRating::all();
        $ratings = SupportControllerCosImLazy::parseUserRatings($ratings);
        return response()->json(['sucess' => true, 'value' => $ratings]);
    }

    public function getAllOrders(){
        $orders = Order::all();
        $orders = SupportControllerCosImLazy::parseOrder($orders);
        return response()->json(['sucess' => true, 'value' => $orders]);
    }

    public function deleteOrder(Request $request){
        $id = $request->get('id');
        $order = Order::find($id);
        if (empty($order)){
            return response()->json(['success' => false, 'message' => 'No such order']);
        }
        Order::where('id', $id)->delete();
        return response()->json(['success' => true]);
    }

    public function changeOrder(Request $request){
        $id = $request->get('id');
        $order = Order::find($id);
        if (empty($order)){
            return response()->json(['success' => false, 'message' => 'No such order']);
        }
        $projectId      = $request->get('projectId');
        $categoryId     = $request->get('categoryId');
        $subcategoryId  = $request->get('subcategoryId');
        $name           = $request->get('name');
        $workArea       = $request->get('workArea');
        $description    = $request->get('description');

        $order->project_id      = $projectId;
        $order->category_id     = $categoryId;
        $order->subcategory_id  = $subcategoryId;
        $order->name            = $name;
        $order->work_area       = $workArea;
        $order->description     = $description;
        $order->save();

        return response()->json(['success' => true]);
    }

    public function deleteProject(Request $request){
        $id = $request->get('id');
        $project = Project::find($id);
        if (empty($project)){
            return response()->json(['success' => false, 'message' => 'No such order']);
        }
        Project::where('id', $id)->delete();
        return response()->json(['success' => true]);
    }
}
