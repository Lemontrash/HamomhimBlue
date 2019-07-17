<?php

namespace App\Http\Controllers;

use App\Role;
use App\User;
use App\UserRating;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getAllWorkersPaginated(Request $request){
        $page = $request->get('page');
        if ($page == 0){
            $offset = 0;
        }else{
            $offset = 6*$page;
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

        $user = User::find($ratableId);
        if (empty($user)){
            return response()->json(['success' => false, 'message' => 'No such user']);
        }
        UserRating::create([
            'userId' => \Auth::id(),
            'rating' => $rating,
        ]);
        return response()->json(['success' => true]);
    }


}
