<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $responseCode = null;
    public $responseMessage = null;
    public $responseData = null;

    public function __construct(){}

    public function getResponse($data = [], $message = '', $code = 200){
        if(isset($this->responseCode)){
            $code = $this->responseCode;
        }
        if(isset($this->responseMessage)){
            $message = $this->responseMessage;
        }
        if(isset($this->responseData)){
            $data = $this->responseData;
        }
        return response()->json([
            'data' => $data,
            'status' => [
                'message' => $message,
                'code' => $code
            ]
        ],$code);
    }

    public function getDataTable($query, $request){//tanpa search
        try{
            if(isset($request->sort_by)){
                $query = $query->orderBy($request->sort_by, $request->order_by);
            }

            if(isset($request->limit)){
                $query = $query->paginate($request->limit);
            }
            else{
                $query = $query->get();
            }


            return $query;
        }
        catch(\Exception $e){
            $this->getResponse([],$e->getMessage(),500);
        }
    }
}
