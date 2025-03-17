<?php

namespace App\Http\Controllers\Mentee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
//use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ResourceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mentee');

    }

    public function index(Request $request)
    {
        $mentor = DB::table('mentee')->select('*')->where('id',Auth::user()->id)->first();

        if(empty($mentor)){
           return view('mentee.resource');
        }

        $search = !empty($request->search)?$request->search:'';
        $data = array();
        $data = DB::table(E_LEARNING.' AS e');
        $data = $data->select('e.id','e.name','e.description','e.type','e.file','e.url','a.affiliate_id')->leftJoin('e_learning_affiliates AS a','a.e_learning_id','e.id')->leftJoin('e_learning_users AS u','u.e_learning_id','e.id')->where('e.name','like', '%'.$search.'%')->where('e.is_active', 1)->where('u.user_type', 1)->where('a.affiliate_id', $mentor->assigned_by);

        if(!empty($search)){
            $data = $data->where('e.name','LIKE','%'.$search.'%');
        }

//        $data = $data->orderBy('e.id', 'desc')->paginate(10);
        $data = $data->orderBy('e.id', 'DESC')->paginate(15);
        $data->appends(array('search'=>$search))->links();
        $base_path = Storage::disk('s3')->url('e_learning');

        return view('mentee.resource', compact('data', 'search', 'base_path'));
    }
}
