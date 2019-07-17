<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SupportControllerCosImLazy extends Controller
{
    public static function parseUsers( $users){
        foreach ($users as $key => $user) {
            $data[$key]['name'] = $user->name;
            $data[$key]['email'] = $user->email;
            $data[$key]['city'] = $user->city;
            $data[$key]['address'] = $user->address;
            $data[$key]['phone'] = $user->phone;
            $data[$key]['name_of_business'] = $user->name_of_business;
            $data[$key]['business_phone'] = $user->business_phone;
            $data[$key]['working_area'] = $user->working_area;
            $data[$key]['fax'] = $user->fax;
            $data[$key]['created_at'] = $user->created_at->timestamp;
            $data[$key]['updated_at'] = $user->updated_at->timestamp;
        }
        return $data;
    }

    public static function parseProjects( $projects){
        foreach ($projects as $key => $project) {
            $data[$key]['id'] = $project->id;
            $data[$key]['name'] = $project->name;
            $data[$key]['description'] = $project->description;
            $data[$key]['userId'] = $project->userId;
            $data[$key]['created_at'] = $project->created_at->timestamp;
        }
        return $data;
    }

    public static function parseCategories( $categories){
        foreach ($categories as $key => $category) {
            $subcatetegories = $category->getAllSubcategories;
            $data[$key]['id'] = $category->id;
            $data[$key]['name'] = $category->name;
            $data[$key]['image'] = $category->image;
            $data[$key]['created_at'] = $category->created_at->timestamp;
            if (!$subcatetegories->isEmpty()){
                $data[$key]['subcategories'] = $subcatetegories;
            }
        }
        return $data;
    }
}
