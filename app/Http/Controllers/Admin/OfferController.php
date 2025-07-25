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
use DateTime;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Http\Controllers\Controller;

class OfferController extends Controller
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
    public function active_offer_type(Request $request)
    {
    	if(Auth::user()->type == 1){
    		$active_offer_type = DB::table('offer_type')
            ->leftjoin('admins','admins.id','=','offer_type.created_by')
            ->select('admins.name as creator','offer_type.*')
            ->paginate(10);
    	}else if(Auth::user()->type == 2){
            $active_offer_type = DB::table('offer_type')
            ->leftjoin('admins','admins.id','=','offer_type.created_by')
            ->select('admins.name as creator','offer_type.*')
            ->where('offer_type.created_by', Auth::user()->id)
            ->paginate(10);

    		//$active_offer_type = DB::table('offer_type')->where('created_by', Auth::user()->id)->paginate(10);
    	}
        

        return view('admin.offering.list_offer_type')->with('offer_types',$active_offer_type);

    }

    public function add($id)
    { 
     try {
       $id = Crypt::decrypt($id);
        $offer_type = DB::table('offer_type')->where('id',$id)->first();

        return view('admin.offering.add_offer_type',['offer_type' => $offer_type]);
        }
        catch (\Exception $e) {
            return redirect()->route('admin.active_offertypes');
        }
    } 

    
    public function save(Request $request)
    {
        $id = Input::get('id');
        $offer_type = Input::get('offer_type');

        $validator = Validator::make($request->all(), [
            'offer_type' => 'required'
        ]);
        if ($validator->fails()) {
            $id = Crypt::encrypt($id);
            return redirect('admin/offer_type/add/'.$id)
                        ->withErrors($validator)
                        ->withInput();
        }



        if(empty($id)){
                $id = DB::table('offer_type')->insertGetId(['name' => $offer_type,'created_by' => Auth::user()->id]); 
                session(['success_message' => 'Data added successfully']);
            }else{
                DB::table('offer_type')
                ->where('id', $id)
                ->update(['name' => $offer_type]);
                session(['success_message' => 'Data updated successfully']);
            }


       

        return redirect('admin/active_offertypes');
    }


}