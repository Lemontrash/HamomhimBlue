<?php

namespace App\Http\Controllers;

use App\Order;
use App\Project;
use App\User;
use App\UserFile;
use App\WorkerOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function addNewProject(Request $request){
        $projectName        = $request->get('name');
        $projectDescription = $request->get('description');
        $projectUserId      = $request->get('user_id');


        $project = Project::create([
            'name'          => $projectName,
            'description'   => $projectDescription,
            'user_id'       => $projectUserId,
            'status'        => 'open'
        ]);

        return response()->json(['success' => true], 201);
    }


    public function getAllProjectsByUser(){
        $projects = Project::where('user_id', \Auth::id())->where('status', 'open')->get();

        if ($projects->isEmpty()){
            return response()->json(['success' => false, 'message' => 'No projects found'], 200);
        }

        $data = SupportControllerCosImLazy::parseProjects($projects);
        return response()->json(['success' => true, 'value' => $data], 200);
    }


    public function addOrderToProject(Request $request){
        $id = $request->get('projectId');

        $orderWorkArea      = $request->get('orderWorkArea');
        $orderName          = $request->get('orderName');
        $orderDescription   = $request->get('orderDescription');
        $categoryId         = $request->get('categoryId');
        $subcategoryId      = $request->get('subcategoryId');

        $project = Project::find($id);
        if (empty($project)){
            return response()->json(['success' => false, 'message' => 'No such project']);
        }

        $order = Order::create([
            'project_id'     => $project->id,
            'category_id'    => $categoryId,
            'subcategory_id' => $subcategoryId,
            'name'          => $orderName,
            'work_area'     => $orderWorkArea,
            'description'   => $orderDescription,
        ]);
        return response()->json(['success' => true], 201);
    }

    public function closeProject(Request $request){
        $projectId = $request->get('projectId');
        $project = Project::find($projectId);
        if ($project->status == 'closed'){
            return response()->json(['success' => false, 'message' => 'Project is already closed']);
        }
        $project->status = 'closed';
        $project->save();
        return response()->json(['success' => true], 200);
    }

    public function openProject(Request $request){
        $projectId = $request->get('projectId');
        $project = Project::find($projectId);
        if ($project->status == 'open'){
            return response()->json(['success' => false, 'message' => 'Project is already open']);
        }
        $project->status = 'open';
        $project->save();
        return response()->json(['success' => true], 200);
    }

    public function getAllUsersOnProject(Request $request){
        $projectId = $request->get('projectId');

        $project = Project::find($projectId);
        if(empty($project)){
            return response()->json(['success' => false, 'message' => 'No such project']);
        }

        $orders = $project->getAllOrders;

        foreach ($orders as $key => $order) {
            $data[$key] = SupportControllerCosImLazy::parseAllUsersForOrder($order->id);
        }
        return response()->json(['success' => true, 'value' => $data]);
    }

    public function getProjectOrderForSpecificUser(Request $request){
        $projectId = $request->get('projectId');
        $userId = $request->get('userId');

        $project = Project::find($projectId);
        $user = User::find($userId);
        if (empty($project) || empty($user)){
            return response()->json(['success' => false, 'message' => 'Project or user (or both) were not found']);
        }
        $orders = $project->getAllOrders;

        foreach ($orders as $key => $order) {
            $workerOrders[$key] = WorkerOrder::where('order_id', $order->id)->get();
        }

        if (!isset($workerOrders)){
            return response()->json(['success' => false, 'message' => 'No users were assigned to this project']);
        }

        foreach ($workerOrders as $workerOrder) { // сделать еще один цикл внутренний
            dd($workerOrders);
            if ($workerOrder->userId == $user->id){
                $neededOrderId = $workerOrder->order_id;
            }
        }

        if (!isset($neededOrderId)){
            return response()->json(['success' => false, 'message' => 'This user wasnt assigned to this project']);
        }

        $neededOrder = Order::find($neededOrderId);
        $data = SupportControllerCosImLazy::parseOrder($neededOrder);

        return response()->json(['success' => true, 'value' => $data]);
    }
}
