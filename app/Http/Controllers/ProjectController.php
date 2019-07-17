<?php

namespace App\Http\Controllers;

use App\Order;
use App\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function addNewProject(Request $request){
        $projectName        = $request->get('projectName');
        $projectDescription = $request->get('projectDescription');
        $orderWorkArea      = $request->get('orderWorkArea');
        $orderName          = $request->get('orderName');
        $orderDescription   = $request->get('orderDescription');
        $categoryId         = $request->get('categoryId');
        $subcategoryId      = $request->get('subcategoryId');

        $project = Project::create([
            'name'          => $projectName,
            'description'   => $projectDescription,
            'userId'        => 1,
            'status'        => 'open'
//            'userId'        => \Auth::id(),
        ]);

        $order = Order::create([
            'projectId'     => $project->id,
            'categoryId'    => $categoryId,
            'subcategoryId' => $subcategoryId,
            'name'          => $orderName,
            'work_area'     => $orderWorkArea,
            'description'   => $orderDescription,

        ]);
        return response()->json(['success' => true], 201);
    }


    public function getAllProjectsByUser(){
        $projects = Project::where('userId', \Auth::id())->where('status', 'open')->get();

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
            'projectId'     => $project->id,
            'categoryId'    => $categoryId,
            'subcategoryId' => $subcategoryId,
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
}
