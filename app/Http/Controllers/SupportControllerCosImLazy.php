<?php

namespace App\Http\Controllers;

use App\User;
use App\WorkerOrder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class SupportControllerCosImLazy extends Controller
{
    public static function parseUsers( $users){
        dd($users);
        if ($users instanceof Collection){
            foreach ($users as $key => $user) {
                $data[$key]['id']               = $user->id;
                $data[$key]['name']             = $user->name;
                $data[$key]['email']            = $user->email;
                $data[$key]['city']             = $user->city;
                $data[$key]['address']          = $user->address;
                $data[$key]['phone']            = $user->phone;
                $data[$key]['name_of_business'] = $user->name_of_business;
                $data[$key]['business_phone']   = $user->business_phone;
                $data[$key]['working_area']     = $user->working_area;
                $data[$key]['fax']              = $user->fax;
                $data[$key]['role']              = $user->role;
                $data[$key]['created_at']       = $user->created_at->timestamp;
                $data[$key]['updated_at']       = $user->updated_at->timestamp;
            }
        }else{
            foreach ($users as $key => $user) {
                $data['id']               = $users->id;
                $data['name']             = $users->name;
                $data['email']            = $users->email;
                $data['city']             = $users->city;
                $data['address']          = $users->address;
                $data['phone']            = $users->phone;
                $data['name_of_business'] = $users->name_of_business;
                $data['business_phone']   = $users->business_phone;
                $data['working_area']     = $users->working_area;
                $data['fax']              = $users->fax;
                $data['role']             = $users->role;
                $data['created_at']       = $users->created_at->timestamp;
                $data['updated_at']       = $users->updated_at->timestamp;
            }
        }

        return $data;
    }

    public static function parseProjects( $projects){
        foreach ($projects as $key => $project) {
            $data[$key]['id']           = $project->id;
            $data[$key]['name']         = $project->name;
            $data[$key]['description']  = $project->description;
            $data[$key]['userId']       = $project->user_id;
            $data[$key]['created_at']   = $project->created_at->timestamp;
        }
        return $data;
    }

    public static function parseCategories( $categories){
        foreach ($categories as $key => $category) {
            $subcatetegories = $category->getAllSubcategories;
            $data[$key]['id']           = $category->id;
            $data[$key]['name']         = $category->name;
            $data[$key]['image']        = $category->image;
            $data[$key]['created_at']   = $category->created_at->timestamp;
            if (!$subcatetegories->isEmpty()){
                $data[$key]['subcategories'] = $subcatetegories;
            }
        }
        return $data;
    }

    public static function parseAllUsersForOrder($orderId){
        $workerOrders = WorkerOrder::where('order_id', $orderId)->get();
        if ($workerOrders->isEmpty()){
            return false;
        }

        foreach ($workerOrders as $workerOrder) {
            $users[] = User::find($workerOrder->user_id);
        }

        $userData = SupportControllerCosImLazy::parseUsers($users);

        foreach ($workerOrders as $key => $workerOrder) {
            $data[$key]['orderId']  = $workerOrder->order_id;
            $data[$key]['user']     = $userData[$key];
        }

        return $data;
    }

    public static function parseUserFiles($files){
        foreach ($files as $key => $file) {
            $data[$key]['id']           = $file->id;
            $data[$key]['userId']       = $file->user_id;
            $data[$key]['file']         = $file->file;
            $data[$key]['created_at']   = $file->created_at->timestamp;
            $data[$key]['updated_at']   = $file->updated_at->timestamp;
        }

        return $data;
    }

    public static function parseOrder($orders){
        if ($orders instanceof Collection){
            foreach ($orders as $key => $order) {
                $data[$key]['id']             = $order->id;
                $data[$key]['projectId']      = $order->project_id;
                $data[$key]['categoryId']     = $order->category_id;
                $data[$key]['subcategoryId']  = $order->subcategoryId;
                $data[$key]['name']           = $order->name;
                $data[$key]['work_area']      = $order->work_area;
                $data[$key]['description']    = $order->description;
                $data[$key]['created_at']     = $order->created_at->timestamp;
                $data[$key]['updated_at']     = $order->updated_at->timestamp;
            }
        }else{
            $data['id']             = $orders->id;
            $data['project_id']     = $orders->project_id;
            $data['category_id']    = $orders->category_id;
            $data['subcategory_id'] = $orders->subcategory_id;
            $data['name']           = $orders->name;
            $data['work_area']      = $orders->work_area;
            $data['description']    = $orders->description;
            $data['created_at']     = $orders->created_at->timestamp;
            $data['updated_at']     = $orders->updated_at->timestamp;
        }
        return $data;
    }

    public static function parseUserRatings($ratings){
        foreach ($ratings as $key => $rating) {
            $data[$key]['id']           = $rating->id;
            $data[$key]['user_id']      = $rating->uszr_id;
            $data[$key]['title']        = $rating->title;
            $data[$key]['content']      = $rating->content;
            $data[$key]['created_at']   = $rating->created_at->timestamp;
            $data[$key]['updated_at']   = $rating->updated_at->timestamp;
        }
        return $data;
    }

}
