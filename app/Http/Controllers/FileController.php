<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class FileController extends Controller
{
    public static function uploadPicture($type, $file){
        switch ($type){
            case 'post':
                //images
                if($file) {
                    $name = 'FILE'.rand(0,999999).time().'.'.$file->getClientOriginalExtension();
                    $destinationPath = public_path('/images/postImages');
                    $file->move($destinationPath, $name);
                }
                return json_encode(['success' => true, 'image' => '/images/postImages/'.$name]);
                break;
            case 'requestAttachment':
                //@TODO сделать загрузку файла
                // all files
                if($file) {
                    if ($file->getClientOriginalExtension() == 'php' || $file->getClientOriginalExtension() == 'exe'){
                        return json_encode(['success' => false, 'message' => 'Restricted file type']);
                    }
                    $name = 'FILE'.rand(0,999999).time().'.'.$file->getClientOriginalExtension();
                    $destinationPath = public_path('/files/requestFiles');
                    $file->move($destinationPath, $name);
                }
                return json_encode(['success' => true, 'file' => '/files/requestFiles/'.$name]);
                break;
            case 'avatar':
                if($file) {
                    $name = 'FILE'.rand(0,999999).time().'.'.$file->getClientOriginalExtension();
                    $destinationPath = public_path('/files/avatars');
                    $file->move($destinationPath, $name);
                }
                return json_encode(['success' => true, 'avatar' => '/files/avatars'.$name]);
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
