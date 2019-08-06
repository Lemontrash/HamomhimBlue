<?php

namespace App\Http\Controllers;

use App\Order;
use App\Project;
use App\Role;
use App\User;
use App\UserFile;
use App\UserRating;
use App\WorkerOrder;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getAllWorkersPaginated(Request $request){
        $page = $request->get('page');
        if ($page == 0){
            $offset = 0;
        }else{
            $offset = 6 * $page;
        }

        $roles = Role::where('role', 'worker')->take(6)->offset($offset)->get();
        foreach ($roles as $role) {
            $users[] = $role->getUser()->first();
        }

        $data = SupportControllerCosImLazy::parseUsers($users);
        return response()->json($data, 200);
    }

    public function changePersonalInfo(Request $request){
        $id                 = $request->get('userId');
        $name               = $request->get('name');
        $city               = $request->get('city');
        $address            = $request->get('address');
        $phone              = $request->get('phone');
        $name_of_business   = $request->get('name_of_business');
        $business_phone     = $request->get('business_phone');
        $working_area       = $request->get('working_area');
        $fax                = $request->get('fax');
        $avatar             = $request->file('avatar');

        $flag = false;
        if (isset($avatar)){
            $flag = true;
            $success = json_decode(FileController::uploadPicture('avatar', $avatar));
            if ($success->success == false){
                return response()->json(['success' => false, 'message' => 'Something went wrong with the file']);
            }
        }

        $user = User::find($id);
        $user->name             = $name;
        $user->city             = $city;
        $user->address          = $address;
        $user->phone            = $phone;
        $user->name_of_business = $name_of_business;
        $user->business_phone   = $business_phone;
        $user->working_area     = $working_area;
        $user->fax              = $fax;
        if ($flag == true){
            $user->avatar       = $success->avatar;
        }
        $user->save();

        return response()->json(['success' => true], 200);
    }

    public function changePassword(Request $request){
        $userId = $request->get('userId');
        $oldPass = $request->get('oldPassword');
        $newPass = $request->get('newPassword');

        $user = User::find($userId);
//        dd($user);
        if (\Hash::check($oldPass, $user->password)){
            $user->password = \Hash::make($newPass);
            $user->save();
        }else{
            return response()->json(['success' => false, 'message' => 'Password does not match with the current password']);
        }
        return response()->json(['success' => true]);
    }

    public function addRatingOnUser(Request $request){
        $ratableId  = $request->get('ratableId');
        $rating     = $request->get('rating');
        $title      = $request->get('title');
        $content    = $request->get('content');

        $user = User::find($ratableId);
        if (empty($user)){
            return response()->json(['success' => false, 'message' => 'No such user']);
        }
        UserRating::create([
            'user_id' => \Auth::id(),
            'rating' => $rating,
            'title' => $title,
            'content' => $content,
        ]);
        return response()->json(['success' => true]);
    }

    public function addPersonalFile(Request $request){
        $id = \Auth::id();
        $file = $request->file('file');
        $success = json_decode(FileController::uploadPicture('personal', $file));
        if ($success->success == false){
            return response()->json(['success' => false, 'message' => 'Something went wrong']);
        }
        UserFile::create([
            'user_id' => $id,
            'file' => $success->file,
        ]);

        return response()->json(['success' => true]);
    }

    public function getAllUserFiles(Request $request){
        $userId = $request->get('userId');
        $user = User::find($userId);
        if (empty($user)){
            return response()->json(['success' => false, 'message' => 'No such user']);
        }
        $files = UserFile::where('userId', $userId)->get();
        if ($files->isEmpty()){
            return response(['success' => false, 'message' => 'No files found']);
        }

        $data = SupportControllerCosImLazy::parseUserFiles($files);

        return response()->json(['success' => true, 'value' => $data]);
    }

    public function getAllWorkerProjects(Request $request){
        $userId = $request->get('userId');

        $orders = WorkerOrder::where('userId', $userId)->get();
        if ($orders->isEmpty()){
            return response()->json(['success' => false, 'message' => 'User has no assigned projects']);
        }
        foreach ($orders as $order) {
            $allOrders[] = Order::find($order->orderId);
        }

        $ids = [];
        foreach ($allOrders as $allOrder) {
            if (!in_array($allOrder->projectId, $ids)){
                $allProjects[] = Project::find($allOrder->projectId);
                $ids[] = $allOrder->projectId;
            }
        }
//        dd($ids);

        $data = SupportControllerCosImLazy::parseProjects($allProjects);

        return response()->json(['success' => true, 'value' => $data]);

    }


}
