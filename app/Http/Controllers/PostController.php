<?php

namespace App\Http\Controllers;

use App\Post;
use App\PostContent;
use App\PostImage;
use App\PostImageAndText;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PostController extends Controller
{
    protected  $post;

    public function addFullPost(Request $request){
        $sections = $request->get('sections');
        $files = $request->allFiles();

        foreach ($sections as $key => $section) {
            if ($section['type'] == 'metaTitle'){
                $title      = $section['title'];

                $date       = $section['date'];
                $this->post = $this->addFullPostAddMeta($title, $date);
            }
            elseif ($section['type'] == 'content'){
                $content = $section['value'];
                $this->addFullPostAddContent($content,$key);
            }
            elseif ($section['type'] == 'image'){
                if (isset($files['sections'][$key]['image'])){
                    $status = json_decode(FileController::uploadPicture('post', $files['sections'][$key]['image']));
                    if ($status['success'] == true){
                        if (isset($section['description'])){
                            $this->addFullPostAddImage($status['image'], $section['description'], $key);
                        }else{
                            $this->addFullPostAddImage($status['image'], ' ', $key);
                        }
                    }
                }
            }
            elseif ($section['type'] == 'imageAndText'){
                if (isset($files['sections'][$key]['image'])){
                    $status = json_decode(FileController::uploadPicture('post', $files['sections'][$key]['image']));
                    if ($status['success'] == true){
                        $content = $section['value'];
                        $position = $section['imagePosition'];
                        $this->addFullPostAddImageAndText($status['image'],$content, $position,$key);
                    }
                }
            }

        }
        return json_encode(['success' => true]);
    }

    public function addFullPostAddMeta($title, $date){
        $date = $date/1000;
        $date = Carbon::createFromTimestamp($date)->toDateTimeString();

        $post = Post::create([
            'author' =>  User::find(\Auth::id())->name,
            'title' => $title,
            'created_at' => $date,
        ]);

        return $post;
    }

    public function addFullPostAddContent($content, $order){
        PostContent::create([
            'postId' => $this->post->id,
            'content' => $content,
            'order' => $order
        ]);
    }

    public function addFullPostAddImage($image, $description, $order){
        PostImage::create([
            'postId' => $this->post->id,
            'image' => $image,
            'order' => $order,
            'description' => $description
        ]);
    }

    public function addFullPostAddImageAndText($image,$content, $position, $order){
        PostImageAndText::create([
            'postId' => $this->post->id,
            'image' => $image,
            'content' => $content,
            'imagePosition' => $position,
            'order' => $order
        ]);
    }

}
