<?php
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Auth;
use Route;
use DB;
use Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
class KeywordController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        $query = $request->search;
    	$data = array();
    	$data = DB::table('keyword')
                                ->select('*')
                                ->where(function ($q) use ($query) {
                                    $q->where('title', 'like', '%'.$query.'%');
                                })
                                ->orderBy('created_at','desc')
                                ->paginate(10);
        $data->appends(array('search' => $query))->links();
                   
    	return view('admin.keyword.list')->with('data',$data)->with('search',$query);
    }

    public function change_status(Request $request)
    {
    	$id = $request->id;
    	try {
            $dec_id = Crypt::decrypt($id);
            $data = DB::table('keyword')->where('id',$dec_id)->first();
	    	if($data->status == 0){
	    		DB::table('keyword')->where('id',$dec_id)->update(['status'=>1]);
	    		session(['success_message' => "Activated successfully"]);
	    	}else{
	    		DB::table('keyword')->where('id',$dec_id)->update(['status'=>0]);
	    		session(['success_message' => "Deactivated successfully"]);
	    	}
	    	return redirect('/admin/keyword/list');
        }catch (\Exception $e) {
            return redirect('/admin/keyword/list');
        }
    	
    }

    public function add(Request $request)
    {
        
        $data = array();
        return view('admin.keyword.add')->with('data',$data);
    }

    public function save(Request $request)
    {
        
        $title = $request->title;
        $chk_keyword = DB::table('keyword')->where('title',$title)->first();
        if(!empty($chk_keyword)){
            session(['err_msg'=>"Already exists"]);
            return redirect('/admin/keyword/add');
        }else{
            $id = DB::table('keyword')->insertGetId(['title'=>$title]);
            session(['success_message'=>"Created successfully"]);
            return redirect('/admin/keyword/list');
        }
        
    }
}