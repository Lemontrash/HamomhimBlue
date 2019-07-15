<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class FileController extends Controller
{
    public static function uploadPicture($type, $file){
        switch ($type){
            case 'post':
                $image = $file;
                if($image) {
                    $name = 'FILE'.rand(0,999999).time().'.'.$image->getClientOriginalExtension();
                    $destinationPath = public_path('/images/postImages');
                    $image->move($destinationPath, $name);
                }
                return json_encode(['success' => true, 'image' => '/images/postImages'.$name]);
                break;
            case '':
                break;

        }
        return json_encode(['success' => false, 'message' => 'No such type']);
    }


    public static function deleteFromLocalStorage($file){

    }
}


///storage/post/12/thumbnail/thumbnail@2x.jpeg
///storage/post/12/thumbnail/thumbnail@1x.jpeg
///storage/post/12/thumbnail/thumbnail.jpeg
///storage/post/12/video/qweqwe.jpeg
