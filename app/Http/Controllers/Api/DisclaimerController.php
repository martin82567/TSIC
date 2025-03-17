<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File; 
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;

class DisclaimerController extends Controller
{
    //
    public function index(Request $request)
    {
    	# code...
    	$data = DB::table('waiver_statement')->select('id','statement','url')->where('status',1)->first();
    	return Response::json(['status'=>true,'message'=>"Disclaimer",'data'=>array('disclamer'=>$data)]);
    }
}
