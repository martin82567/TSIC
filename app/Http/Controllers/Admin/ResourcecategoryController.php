<?php
namespace App\Http\Controllers\Admin;
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
class ResourcecategoryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    /**
     * show dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = $request->search;
        $resource_category_arr = DB::table('resource_category')
                            ->where(function ($q) use ($query) {
                                $q->where('name', 'like', '%'.$query.'%');
                            })
                            ->orderBy('id', 'desc')
                            ->paginate(15);
        return view('admin.resource_category.list',['resource_category_arr' => $resource_category_arr]);
    }

    public function add($id)
    {  
        try {
        $id = Crypt::decrypt($id);
        $user_details = DB::table('resource_category')->where('id',$id)->first();
        $admin_files_details = DB::table('admin_files')->where('admin_id',$id)->get();
        return view('admin.resource_category.add',['user_details' => $user_details, 'admin_files_details' => $admin_files_details]);
        }
        catch (\Exception $e) {
            return redirect()->route('admin.resource_category');
        }
    }

    public function save(Request $request)
    {

    $id = Input::get('id');
    $validator = Validator::make($request->all(), [
        'name' => 'required'
    ]);
    if ($validator->fails()) {
        return redirect('admin/resource_category/add/'.$id)
                    ->withErrors($validator)
                    ->withInput();
    }

    $name = Input::get('name');
    $is_active = Input::get('is_active');

    if(empty($id)){
        
        $id = DB::table('resource_category')->insertGetId(['name' => $name,'is_active' => $is_active]); 
        session(['success_message' => 'Data added successfully']);
    }else{
        DB::table('resource_category')
        ->where('id', $id)
        ->update(['name' => $name,'is_active' => $is_active]);
        session(['success_message' => 'Data updated successfully']);
    }
    return redirect()->action('Admin\ResourcecategoryController@index');
    }
}