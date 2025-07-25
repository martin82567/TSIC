<?php

namespace App\Http\Controllers\Mentee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UploadReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mentee');
    }

    public function index(Request $request)
    {
        $reports = DB::table('report')->select('*')->where('mentee_id',auth('mentee')->user()->id)->orderBy('id','desc')->get();
        $base_path = Storage::disk('s3')->url('report');

        return view('mentee.upload-report', compact('reports','base_path'));
    }

    public function create(Request $request)
    {
        return view('mentee.upload-report');
    }

    public function save(Request $request)
    {
        $name = !empty($request->name)?$request->name:'';
        $image = !empty($request->image)?$request->image:'';
        $id = !empty($request->id)?$request->id:'';

        if (empty($name)) {
            return response()->json(['status'=>false, 'message' => "Please give name", 'data' => array()]);
        }

        if (empty($image)) {
            return response()->json(['status'=>false, 'message' => "Please give image", 'data' => array()]);
        }

        $report = DB::table('report')
            ->where('id',$id)
            ->first();

        if(!empty($image)){
            $image_name = time().uniqid(rand());
            $image_name = $image_name.'.'.$image->getClientOriginalExtension();


            $filePath = 'report/' . $image_name;
            Storage::disk('s3')->put($filePath, file_get_contents($image), 'public');
        }else{
            $image_name = $report->image;
        }
        if(empty($id)){
            $id = DB::table('report')->insertGetId(['name' => $name,'image' => $image_name,'mentee_id' => auth('mentee')->user()->id]);
            $message =  'Data added successfully';
        }else{
            $id = DB::table('report')->where('id', $id)->update(['name'=>$name,'image'=>$image_name]);
            $message =  'Data updated successfully';
        }
        DB::table('report')->insert([
            'name'=> $name,
            'image'=> $image_name,
        ]);
        session(['success_message' => "Report Uploaded Successfully"]);
        return redirect(route('mentee.report.index'));
    }

}