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

class ProjectController extends Controller
{
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

    public function addNewProject(Request $request){
        $name = $request->name;
        $description = $request->description;
        $userId = $request->userId;
        $status = $request->status;

        $user = User::find($userId);
        if (empty($user)){
            return response()->json(['success' => false, 'message' => 'no such user']);
        }
        Project::create([
            'name' => $name,
            'description' => $description,
            'user_id' => $userId,
            'status' => $status
        ]);
        return response()->json(['success' => true]);
    }

    public function editProject(Request $request){
        $id = $request->get('projectId');
        $project = User::find($id);
        if (empty($project)){
            return response()->json(['success' => false, 'message' => 'no such project']);
        }

        $project->name          = $request->name;
        $project->description   = $request->description;
        $project->status        = $request->status;
        $project->save();
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
    
    public function getProjectCounter(){
        return  Project::count();
    }


}
