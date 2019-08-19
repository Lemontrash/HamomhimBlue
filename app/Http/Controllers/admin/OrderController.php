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
use App\Http\Controllers\FileController;

class OrderController extends Controller
{
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

    public function getOrderCounter(){
        return  Order::count();
    }

    public function getSingleOrder(Request $request){
        $id = $request->get('orderId');
        $order = Order::find($id);

        if (empty($order)){
            return response()->json(['success' => false, 'message' => 'no such order']);
        }

        $project = Project::find($order->project_id);
        $category = Category::find($order->category_id);

        $orderData['order'] = SupportControllerCosImLazy::parseOrder($order);
        $orderData['project'] = SupportControllerCosImLazy::parseProjects($project);
        $orderData['category'] = SupportControllerCosImLazy::parseCategories($category);

        return response()->json(['success' => true, 'value' => $orderData]);
    }

    public function editOrder(Request $request){
        $id = $request->get('orderId');
        $order = Order::find($id);
        if (empty($order)){
            return response()->json(['success' => false, 'message' => 'no such order']);
        }

        $order->project_id      = $request->projectId;
        $order->category_id     = $request->categoryId;
        $order->subcategory_id  = $request->subcategoryId;
        $order->name            = $request->name;
        $order->work_area       = $request->work_area;
        $order->description     = $request->description;

        $order->save();

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

    public function deleteOrder(Request $request){
        $id = $request->get('id');
        $order = Order::find($id);
        if (empty($order)){
            return response()->json(['success' => false, 'message' => 'No such order']);
        }
        Order::where('id', $id)->delete();
        return response()->json(['success' => true]);
    }
    public function getOrderCounter(){
        return  Order::count();
    }
}
