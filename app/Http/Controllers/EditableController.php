<?php

namespace App\Http\Controllers;

use App\AboutUs;
use App\HowItWorksArchitect;
use App\HowItWorksWorker;
use App\MainPage;
use App\PrivacyPolicy;
use App\TermsAndConditions;
use Illuminate\Http\Request;

class EditableController extends Controller
{
    public function getAlEditableContent(){
        $data['mainPage']            = $this->getMainPage();
        $data['termsAndConditions']  = $this->getTerms();
        $data['aboutUs']             = $this->getAboutUs();
        $data['privacyPolicy']       = $this->getPrivacyPolicy();
        $data['HowItWorksArchitect'] = $this->getHowItWorksArchitect();
        $data['HowItWorksWorker']    = $this->getHowItWorksWorker();

        return response()->json(['success' => true, 'value' => $data]);
    }

    private function getMainPage(){
        $currentPage = MainPage::find(1);
        return $currentPage->text;
    }

    private function getTerms(){
        $currentPage = TermsAndConditions::find(1);
        return $currentPage->text;
    }

    private function getAboutUs(){
        $currentPage = AboutUs::find(1);
        $data['title'] = $currentPage->title;
        $data['subtitle'] = $currentPage->subtitle;
        $data['text'] = $currentPage->text;
        $data['video'] = $currentPage->video;
        $data['titleSecond'] = $currentPage->title_second;
        $data['textSecond'] = $currentPage->text_second;
        $data['titleOnBlue'] = $currentPage->title_on_blue;
        $data['textOnBlue'] = $currentPage->text_on_blue;
        $data['coworkers'] = $currentPage->coworkers;
        $data['architects'] = $currentPage->architects;
        $data['workers'] = $currentPage->workers;
        return $data;
    }

    private function getPrivacyPolicy(){
        $currentPage = PrivacyPolicy::find(1);
        return $currentPage->text;
    }

    private function getHowItWorksArchitect(){
        $currentPage = HowItWorksArchitect::all();
        return $currentPage;
    }

    private function getHowItWorksWorker(){
        $currentPage = HowItWorksWorker::all();
        return $currentPage;
    }


    public function changeMainPage(Request $request){
        $text = $request->get('text');
        MainPage::truncate();
        MainPage::create([
            'text' => $text
        ]);
        return response()->json(['success' => true]);
    }

    public function changeTerms(Request $request){
        $text = $request->get('text');
        MainPage::truncate();
        MainPage::create([
            'text' => $text
        ]);
        return response()->json(['success' => true]);
    }

    public function changeAboutUs(Request $request){
        $title          = $request->get('title');
        $subtitle       = $request->get('subtitle');
        $text           = $request->get('text');
        $video          = $request->get('video');
        $titleSecond    = $request->get('title_second');
        $textSecond     = $request->get('text_second');
        $titleOnBlue    = $request->get('title_on_blue');
        $textOnBlue     = $request->get('text_on_blue');
        $imageOnBlue    = $request->get('image_on_blue');
        $coworkers      = $request->get('coworkers');
        $architects     = $request->get('architects');
        $workers        = $request->get('workers');
        AboutUs::truncate();
        AboutUs::create([
            'title'         => $title,
            'subtitle'      => $subtitle,
            'text'          => $text,
            'video'         => $video,
            'title_second'  => $titleSecond,
            'text_second'   => $textSecond,
            'title_on_blue' => $titleOnBlue,
            'text_on_blue'  => $textOnBlue,
            'image_on_blue' => $imageOnBlue,
            'coworkers'     => $coworkers,
            'architects'    => $architects,
            'workers'       => $workers,
        ]);
        return response()->json(['success' => true]);
    }

    public function changePrivacyPolicy(Request $request){
        $text = $request->get('text');
        PrivacyPolicy::truncate();
        PrivacyPolicy::create([
            'text' => $text
        ]);
        return response()->json(['success' => true]);
    }

    public function changeHowItWorksArchitect(Request $request){
        $rows = $request->get('rows');
        HowItWorksArchitect::truncate();
        foreach ($rows as $key => $row) {
            HowItWorksArchitect::create([
                'row' => $row,
                'order' => $key
            ]);
        }
        return response()->json(['success' => true]);
    }

    public function changeHowItWorksWorker(Request $request){
        $rows = $request->get('rows');
        HowItWorksWorker::truncate();
        foreach ($rows as $key => $row) {
            HowItWorksWorker::create([
                'row' => $row,
                'order' => $key
            ]);
        }
        return response()->json(['success' => true]);
    }
}
