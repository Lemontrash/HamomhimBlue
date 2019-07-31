<?php

namespace App\Http\Controllers;

use App\User;
use App\WorkerOrder;
use Illuminate\Http\Request;

class SupportControllerCosImLazy extends Controller
{
    public static function parseUsers( $users){
        foreach ($users as $key => $user) {

            $data[$key]['name']             = $user->name;
            $data[$key]['email']            = $user->email;
            $data[$key]['city']             = $user->city;
            $data[$key]['address']          = $user->address;
            $data[$key]['phone']            = $user->phone;
            $data[$key]['name_of_business'] = $user->name_of_business;
            $data[$key]['business_phone']   = $user->business_phone;
            $data[$key]['working_area']     = $user->working_area;
            $data[$key]['fax']              = $user->fax;
            $data[$key]['created_at']       = $user->created_at->timestamp;
            $data[$key]['updated_at']       = $user->updated_at->timestamp;
        }
        return $data;
    }

    public static function parseProjects( $projects){
        foreach ($projects as $key => $project) {
            $data[$key]['id']           = $project->id;
            $data[$key]['name']         = $project->name;
            $data[$key]['description']  = $project->description;
            $data[$key]['userId']       = $project->userId;
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
        $workerOrders = WorkerOrder::where('orderId', $orderId)->get();
        if ($workerOrders->isEmpty()){
            return false;
        }

        foreach ($workerOrders as $workerOrder) {
            $users[] = User::find($workerOrder->userId);
        }

        $userData = SupportControllerCosImLazy::parseUsers($users);

        foreach ($workerOrders as $key => $workerOrder) {
            $data[$key]['orderId']  = $workerOrder->orderId;
            $data[$key]['user']     = $userData[$key];
        }

        return $data;
    }

    public static function parseUserFiles($files){
        foreach ($files as $key => $file) {
            $data[$key]['userId']       = $file->userId;
            $data[$key]['file']         = $file->file;
            $data[$key]['created_at']   = $file->created_at->timestamp;
            $data[$key]['updated_at']   = $file->updated_at->timestamp;
        }

        return $data;
    }

    public static function parseOrder($orders){
        if (is_array($orders)){
            foreach ($orders as $key => $order) {
                $data[$key]['projectId']      = $order->projectId;
                $data[$key]['categoryId']     = $order->categoryId;
                $data[$key]['subcategoryId']  = $order->subcategoryId;
                $data[$key]['name']           = $order->name;
                $data[$key]['work_area']      = $order->work_area;
                $data[$key]['description']    = $order->description;
                $data[$key]['created_at']     = $order->created_at;
                $data[$key]['updated_at']     = $order->updated_at;
            }
        }else{
            $data['projectId']      = $orders->projectId;
            $data['categoryId']     = $orders->categoryId;
            $data['subcategoryId']  = $orders->subcategoryId;
            $data['name']           = $orders->name;
            $data['work_area']      = $orders->work_area;
            $data['description']    = $orders->description;
            $data['created_at']     = $orders->created_at;
            $data['updated_at']     = $orders->updated_at;
        }
        return $data;
    }
}
