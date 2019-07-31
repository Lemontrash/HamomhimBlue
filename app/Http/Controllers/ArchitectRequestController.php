<?php

namespace App\Http\Controllers;

use App\ArchitectRequest;
use App\ArchitectRequestFile;
use App\Role;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ArchitectRequestController extends Controller
{
    public function architectSendRequest(Request $request){
        $workerId       = $request->get('workerId');
        $date           = $request->get('date');
        $address        = $request->get('address');
        $companyName    = $request->get('companyName');
        $name           = $request->get('name');
        $description    = $request->get('description');

        $files = $request->file('files');
        if (isset($files)){
            foreach ($files as $file) {
                if ($file->getClientOriginalExtension() == 'exe' || $file->getClientOriginalExtension() == 'php'){
                    return response()->json(['success' => false, 'message' => 'Illegal file extension']);
                }
            }
            foreach ($files as $file) {
                $success = FileController::uploadPicture('requestAttachment', $file);
                if (json_decode($success)->success == false){
                    return response()->json(json_decode($success));
                }

                $fileNames[] = json_decode($success)->file;
            }
        }

        if (!isset($date)){
            $date = Carbon::now()->timestamp;
        }

        $workers = Role::where('role', 'worker')->get();

        if ($workers->isEmpty()){
            return response()->json(['success' => false]);
        }
        $flag = false;
        foreach ($workers as $worker) {
            if ($worker->id = $workerId){
                $flag = true;
            }
        }
        if ($flag == false){
            return response()->json(['success' => false, 'message' => 'No suck worker id found']);
        }

        $req = ArchitectRequest::create([
            'address' => $address,
            'name' => $name,
            'company_name' => $companyName,
            'description' => $description,
            'architectId' => 1,
            'workerId' => $workerId,
            'created_at' => Carbon::createFromTimestamp($date)->toDateTimeString()
        ]);
        foreach ($fileNames as $fileName) {
            ArchitectRequestFile::create([
                'request_id' => $req->id,
                'file' => $fileName,
            ]);
        }

        return response()->json(['success' => true], 200);
    }

    public function answerRequest(Request $request){
        $status = $request->get('status');
        $requestId = $request->get('requestId');
        if($status == 'true'){
            $data = $this->acceptResponse($requestId);
            return response()->json($data);
        }else{
            $data = $this->declineResponse($requestId);
            return response()->json($data);
        }
    }

    private function  acceptResponse($requestId){
        $request = ArchitectRequest::find($requestId);
        if (empty($request)){
            return ['success' => false, 'message' => 'No such request'];
        }
        $request->status = 'accepted';
        $request->save();
        return ['success' => true];
    }

    private function declineResponse($requestId){
        $request = ArchitectRequest::find($requestId);
        if (empty($request)){
            return ['success' => false, 'message' => 'No such request'];
        }
        $request->status = 'rejected';
        $request->save();
        return ['success' => true];
    }
}
