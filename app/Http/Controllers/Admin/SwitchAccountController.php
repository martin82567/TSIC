<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File; 
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SwitchAccountController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index($type='')
    {
    	# code...
    	// if(Auth::user()->type != 1){
    	// 	return redirect('/404');
    	// }
        // die('Hi');
    	
    	$data = DB::table('admins');
    	if($type == 'affiliate'){
    		$data = $data->where('type', 2);
    	}else if($type == 'staff'){
    		$data = $data->where('type', 3);
    	}
    	$data = $data->orderBy('name','asc')->get()->toarray();
    	return view('admin.account.index', compact('data','type'));
    }
    
    public function save_post(Request $request)
    {
        $id = !empty($request->id)?$request->id:'';
        $type = !empty($request->type)?$request->type:'';
        
        if(!empty($id)){
            Session::put( 'orig_user', Auth::id() );        
            Auth::loginUsingId($id);
            return redirect('/admin');
        }else{
            session(['msg'=>"Please choose one",'msg_class'=>'danger']);
            return redirect('/admin/switch-acc/index'.'/'.$type);
        }
        

    }

    // // Inside User Controller
    // public function user_switch_start( $new_user )
    // {
    //     $new_user = User::find( $new_user );
    //     Session::put( 'orig_user', Auth::id() );
    //     Auth::login( $new_user );
    //     return Redirect::back();
    // }

    public function back_to_admin()
    {
        $id = Session::pull( 'orig_user' );
        // echo $id; die;
        // $orig_user = User::find( $id );
        Auth::loginUsingId( $id );
        Session::forget('orig_user');
        return redirect('/admin/switch-acc/index/affiliate');
    }
}
