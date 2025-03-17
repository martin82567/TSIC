<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
use Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Auth;
use Hash;
use Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Http\Controllers\Controller;

class VersionController extends Controller
{
    public function get(Request $request)
    {
        $platform = $request->header('platform');
        if ($platform == 'ios') {
            $data = DB::table(APP_VERSION)->where('platform', '=', 'ios')->first();
            return Response::json(['status' => true, 'message' => "Here is your app version", 'data' => array('app_version' => $data)]);
        } else {
            $data = DB::table(APP_VERSION)->where('platform', '=', 'android')->first();
            return Response::json(['status' => true, 'message' => "Here is your app version", 'data' => array('app_version' => $data)]);
        }

    }
}