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

class UserController extends Controller
{
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
    
    public function editUser(Request $request){
        $id = $request->get('userId');
        $user = User::find($id);

        if (empty($user)){
            return response()->json(['success' => false, 'message' => 'no such user']);
        }

        $user->name             = $request->name;
        $user->email            = $request->email;
        $user->city             = $request->city;
        $user->address          = $request->address;
        $user->phone            = $request->phone;
        $user->name_of_business = $request->name_of_business;
        $user->business_phone   = $request->business_phone;
        $user->working_area     = $request->working_area;
        $user->fax              = $request->fax;
        $user->is_active        = $request->is_active;
        $user->is_approved      = $request->is_approved;
        $user->role             = $request->role;
        $user->save();

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

    public function getAllComments(){
        $ratings = UserRating::all();
        $ratings = SupportControllerCosImLazy::parseUserRatings($ratings);
        return response()->json(['success' => true, 'value' => $ratings]);
    }

    public function getUserCounter(){
        return  User::count();
    }
}
