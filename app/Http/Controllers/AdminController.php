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
//        dd($image);
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

    public function getAllProjects(Request $request){
        $page    = $request->get('page');
        $sortBy  = $request->get('sortBy');
        $orderBy = $request->get('orderBy');
        $take    = $request->get('take');
        $search = $request->get('search');
        if ($orderBy != 'ASC' && $orderBy != 'DESC'){
            return response()->json(['success' => false, 'message' => 'wrong order by']);
        }

        if(isset($search)){
            $projects = Project::where('name', $search)->orWhere('name', 'LIKE', $search)->orderBy($sortBy, $orderBy)->get();
        }else{
            if($take != 0){
                $projects = Project::orderBy($sortBy,$orderBy)->get();
            }else{
                if ($page == 0){
                    $offset = 0;
                }else{
                    $offset = $take * $page;
                }
                $projects = Project::take($take)->offset($offset)->orderBy($sortBy, $orderBy)->get();
            }
        }
        if ($projects->isEmpty()){
            return response()->json(['success' => true, 'value' => []]);
        }
        $projectData['projects'] = SupportControllerCosImLazy::parseProjects($projects);
        $projectData['total']    = $this->getProjectCounter();
        return response()->json(['success' => true, 'value' => $projectData]);
    }
    
    public function getSingleProject(Request $request){
        $id = $request->get('projectId');
        $project = Project::find($id);
        if (empty($project)){
            return response()->json(['success' => false, 'message' => 'no such project']);
        }

        $projectData['orders'] = $project->getAllOrders;
        $projectData['project'] = SupportControllerCosImLazy::parseProjects($project);

        if ($project->isEmpty()){
            return response()->json(['success' => true, 'value' => []]);
        }

        return response()->json(['success' => true, 'value' => $projectData]);
    }
    
    public function getAllOrders(Request $request){
        $page    = $request->get('page');
        $sortBy  = $request->get('sortBy');
        $orderBy = $request->get('orderBy');
        $take    = $request->get('take');
        $search = $request->get('search');
        if ($orderBy != 'ASC' && $orderBy != 'DESC'){
            return response()->json(['success' => false, 'message' => 'wrong order by']);
        }

        if(isset($search)){
            $orders = Order::where('name', $search)
                             ->orWhere('name', 'LIKE', $search)
                             ->orWhere('work_area', $search)
                             ->orWhere('work_area', 'LIKE', $search)
                             ->orWhere('description', $search)
                             ->orWhere('description', 'LIKE', $search)
                             ->orderBy($sortBy, $orderBy)
                             ->get();
        }else{
            if($take != 0){
                $orders = Order::orderBy($sortBy,$orderBy)->get();
            }else{
                if ($page == 0){
                    $offset = 0;
                }else{
                    $offset = $take * $page;
                }
                $orders = Order::take($take)->offset($offset)->orderBy($sortBy, $orderBy)->get();
            }
        }
        if ($orders->isEmpty()){
            return response()->json(['success' => true, 'value' => []]);
        }

        $orderData['orders'] = SupportControllerCosImLazy::parseOrder($orders);
        $orderData['total']  = $this->getOrderCounter();
        return response()->json(['success' => true, 'value' => $orderData]);
    }


    public function getAllUsers(Request $request){
        $page    = $request->get('page');
        $sortBy  = $request->get('sortBy');
        $orderBy = $request->get('orderBy');
        $take    = $request->get('take');
        $search = $request->get('search');
        if ($orderBy != 'ASC' && $orderBy != 'DESC'){
            return response()->json(['success' => false, 'message' => 'wrong order by']);
        }


        if (isset($search)){
            $users = User::where('name', $search)
                         ->orWhere('name', 'LIKE', '%'.$search.'%')
                         ->orWhere('email', $search)
                         ->orWhere('email', 'LIKE', '%'.$search.'%')
                         ->orWhere('phone', $search)
                         ->orWhere('phone', 'LIKE', '%'.$search.'%')
                         ->orderBy($sortBy, $orderBy)
                         ->get();
            if ($users->isEmpty()){
                $userData['users'] = [];
            }else{
                $userData['users'] = SupportControllerCosImLazy::parseUsers($users);
            }
        }else{
            if ($take == -1){
                $users = User::orderBy($sortBy, $orderBy)->get();
                $userData['users'] = SupportControllerCosImLazy::parseUsers($users);
            }else{

                if ($page == 0){
                    $offset = 0;
                }else{
                    $offset = $page * $take;
                }
                $users = User::take($take)->offset($offset)->orderBy($sortBy, $orderBy)->get();

                $userData['users'] = SupportControllerCosImLazy::parseUsers($users);
            }
        }


        $userData['total'] = $this->getUserCounter();

        return response()->json(['success' => true, 'value' => $userData]);
    }

    public function getUserCounter(){
        return  User::count();
    }

    public function getProjectCounter(){
        return  Project::count();
    }

    public function getOrderCounter(){
        return  Order::count();
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
        return response()->json(['success' => true, 'value' => $ratings]);
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

    public function addNewUser(Request $request){
        $name               = $request->name;
        $email              = $request->email;
        $city               = $request->city;
        $address            = $request->address;
        $phone              = $request->phone;
        $name_of_business   = $request->name_of_business;
        $business_phone     = $request->business_phone;
        $working_area       = $request->working_area;
        $fax                = $request->fax;
        $password           = $request->password;
        $is_active          = $request->is_active;
        $is_approved        = $request->is_approved;
        $role               = $request->role;

        $sameUser = User::where('email', $email)->where('role', $role)->first();
        if (!empty($sameUser)){
            return response()->json(['success' => false, 'message' => 'user with the same role and email already exists']);
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'city' => $city,
            'address' => $address,
            'phone' => $phone,
            'name_of_business' => $name_of_business,
            'business_phone' => $business_phone,
            'working_area' => $working_area,
            'fax' => $fax,
            'password' => bcrypt($password) ,
            'is_active' => $is_active,
            'is_approved' => $is_approved,
            'role' => $role
        ]);
        $fullUser = SupportControllerCosImLazy::parseUsers($user);
        return response()->json(['success' => true, 'value' => $fullUser]);
    }


    public function editUser(Request $request){
        $id = $request->get('userId');
        $user = User::find($id);

        if (empty($user)){
            return response()->json(['success' => false, 'message' => 'no such user']);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->city = $request->city;
        $user->address = $request->address;
        $user->phone = $request->phone;
        $user->name_of_business = $request->name_of_business;
        $user->business_phone = $request->business_phone;
        $user->working_area = $request->working_area;
        $user->fax = $request->fax;
        $user->is_active = $request->is_active;
        $user->is_approved = $request->is_approved;
        $user->role = $request->role;
        $user->save();

        return response()->json(['success' => true]);
    }

    public function editProject(Request $request){
        $id = $request->get('projectId');
        $project = User::find($id);
        if (empty($project)){
            return response()->json(['success' => false, 'message' => 'no such project']);
        }


    }

    public function editCategory(Request $request){
        $id = $request->get('categoryId');
        $category = User::find($id);
        if (empty($category)){
            return response()->json(['success' => false, 'message' => 'no such category']);
        }
    }

    public function editOrder(Request $request){
        $id = $request->get('orderId');
        $order = Order::find($id);
        if (empty($order)){
            return response()->json(['success' => false, 'message' => 'no such order']);
        }
    }
}
