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

class PostController extends Controller
{
    public function addPost(Request $request){
        $title = $request->get('title');
        $content = $request->get('content');
        $thumbnail = $request->file('thumbnail');
        $author = $request->author;

        $thumbnail = FileController::uploadPicture('post', $thumbnail);
        Post::create([
            'author' => $author,
            'content' => $content,
            'title' => $title,
            'thumbnail' => $thumbnail->file,
        ]);

        return response()->json(['success' => true]);
    }

    public function editPost(Request $request){
        $id = $request->get('postId');

        $post = Post::find($id);
        if (empty($post)){
            return response()->json(['success' => false,'message' => 'no such post']);
        }

        $post->author = $request->author;
        $post->content = $request->get('content');
        $post->title = $request->title;
        if (!empty($request->file('thumbnail'))){
            $thumbnail = FileController::uploadPicture('post', $request->file('thumbnail'));
            $post->thumbnail = $thumbnail->file;
        }
        $post->save();
        return response()->json(['success' => true]);
    }

    public function deletePost(Request $request){
        $id = $request->get('postId');
        $post = Post::find($id);

        if (empty($post)){
            return response()->json(['success' => false, 'message' => 'no such post']);
        }

        Post::where('id', $id)->delete();
        return response()->json(['success' => true]);
    }

    public function getSinglePost(Request $request){
        $id = $request->get('postId');
        $post = Post::find($id);

        if (empty($post)){
            return response()->json(['success' => false, 'message' => 'no such post']);
        }

        $post = SupportControllerCosImLazy::parsePosts($post);
        return response(['success' => true, 'value' => $post]);
    }

    public function getAllPosts(Request $request){
        $page    = $request->get('page');
        $sortBy  = $request->get('sortBy');
        $orderBy = $request->get('orderBy');
        $take    = $request->get('take');
        $search = $request->get('search');
        if ($orderBy != 'ASC' && $orderBy != 'DESC'){
            return response()->json(['success' => false, 'message' => 'wrong order by']);
        }

        if(isset($search)){
            $posts = Post::where('title', $search)->orWhere('title', 'LIKE', $search)->orderBy($sortBy, $orderBy)->get();
        }else{
            if($take != 0){
                $posts = Post::orderBy($sortBy,$orderBy)->get();
            }else{
                if ($page == 0){
                    $offset = 0;
                }else{
                    $offset = $take * $page;
                }
                $posts = Post::take($take)->offset($offset)->orderBy($sortBy, $orderBy)->get();
            }
        }
        if ($posts->isEmpty()){
            return response()->json(['success' => true, 'value' => []]);
        }

        $postData['posts'] = SupportControllerCosImLazy::parsePosts($posts);
        $postData['total'] = $this->getPostsCounter();

        return response()->json(['success' => true, 'value' => $postData]);
    }

    public function getPostsCounter(){
        return  Post::count();
    }

}
