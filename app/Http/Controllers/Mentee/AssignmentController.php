<?php

namespace App\Http\Controllers\Mentee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    protected $user_id;

    public function __construct()
    {
        $this->middleware('auth:mentee');

    }

    public function index(Request $request)
    {
        $affiliate_data = get_single_data_id('admins', Auth::user()->assigned_by);
        $timezone = $affiliate_data->timezone;
        $type = !empty($request->type) ? $request->type : 'pending';

        if (!empty($timezone)) {
            date_default_timezone_set($timezone);
            $cur_date = date('Y-m-d H:i:s');
        } else {
            $cur_date = date('Y-m-d H:i:s');
        }

        if ($type == 'pending') {
            $data = DB::table('goaltask')
                ->leftjoin('assign_task', 'assign_task.goaltask_id', '=', 'goaltask.id')
                ->select('goaltask.*', 'assign_task.id as assign_id', 'assign_task.status as datastatus', 'assign_task.begin_time', 'assign_task.complated_time', 'assign_task.note')
                ->where('assign_task.victim_id', Auth::user()->id)
                ->where('goaltask.status', 1)
                ->where('goaltask.type', 'task')
                ->where('assign_task.status', '!=', 2)
                ->orderBy('goaltask.updated_date', 'desc')
                ->paginate(10);
        } else if ($type == 'completed') {
            $data = DB::table('goaltask')
                ->leftjoin('assign_task', 'assign_task.goaltask_id', '=', 'goaltask.id')
                ->select('goaltask.*', 'assign_task.id as assign_id', 'assign_task.status as datastatus', 'assign_task.begin_time', 'assign_task.complated_time', 'assign_task.note')
                ->where('assign_task.victim_id', Auth::user()->id)
                ->where('goaltask.status', 1)
                ->where('goaltask.type', 'task')
                ->where('assign_task.status', 2)
                ->orderBy('goaltask.updated_date', 'desc')
                ->paginate(10);
        }
        $data->appends(array('type' => $type))->links();

        return view('mentee.my-assignments')->with('type', $type)->with('data', $data);

    }

    public function show(Request $request)
    {
        $id = !empty($request->id) ? $request->id : '';
        $goal_id = Crypt::decrypt($id);

        $goal_detail = DB::table('goaltask')->select('*')->where('id', $goal_id)->first();

        $notes = DB::table('goaltask_note')->select('*')->where('goaltask_id', $goal_id)->get();
        $assign_task = DB::table('assign_task')->select('*')->where('goaltask_id', $goal_id)->first();
        $goal_status = $assign_task->status;
//        dd($goal_status);
        $goal_files = DB::table('goaltaskuserfiles')->select('*')->where('goaltask_id', $goal_id)->get();

        $base_path = Storage::disk('s3')->url('goaltask');

        return view('mentee.my-assignments-detail')->with('detail', $goal_detail)->with('notes', $notes)->with('goal_status', $goal_status)->with('files', $goal_files)->with('base_path', $base_path);

    }

    public function saveNote(Request $request, $id)
    {

        $goalTask = DB::table('goaltask')->select('*')->where('id', $id)->first();

        $type = $goalTask->type;

        $title = Input::get('title');
        $description = Input::get('description');

        // echo $schedule_time; die;

        $affiliate_data = get_single_data_id('admins', Auth::user()->assigned_by);
        $timezone = $affiliate_data->timezone;

        if (!empty($timezone)) {
            date_default_timezone_set($timezone);
            $created_date = date('Y-m-d H:i:s');
        } else {
            $created_date = date('Y-m-d H:i:s');
        }

        if ($type == "task") {
            DB::table('goaltask_note')->insert(['victim_id' => Auth::user()->id, 'type' => $type, 'goaltask_id' => $id, 'title' => $title, 'note' => $description, 'created_date' => $created_date]);
            session(['success_message' => 'Assignment Notes saved successfully']);

            return redirect()->back();

        } else {
            session(['error_message' => 'Assignment Notes are not saved.']);
        }
        return redirect()->back();

    }

    public function saveFiles(Request $request, $id)
    {

        $goalTask = DB::table('goaltask')->select('*')->where('id', $id)->first();

        $type = $goalTask->type;

        $affiliate_data = get_single_data_id('admins', Auth::user()->assigned_by);
        $timezone = $affiliate_data->timezone;

        if (!empty($timezone)) {
            date_default_timezone_set($timezone);
            $created_date = date('Y-m-d H:i:s');
        } else {
            $created_date = date('Y-m-d H:i:s');
        }

//        dd($request->file());

        if ($type == "task") {
            if (!empty($request->files)) {
                $i = 0;
                foreach($request->files as $all_files) {
                    for ($i = 0; $i < count($all_files); $i++) {
                        $img = $all_files[$i];
                        if ($img->getClientOriginalExtension() != 'png' && $img->getClientOriginalExtension() != 'jpg' && $img->getClientOriginalExtension() != 'jpeg') {
                            session(['success_message' => 'Please upload Image file.']);
                        }
                        $image_name = time() . uniqid(rand());
                        $image = $image_name . '.' . $img->getClientOriginalExtension();
                        $filePath = 'goaltask/' . $image;
                        Storage::disk('s3')->put($filePath, file_get_contents($img), 'public');

                        DB::table('goaltaskuserfiles')->insertGetId(['goaltask_id' => $id, 'file_name' => $image, 'added_by' => Auth::user()->id, 'created_date' => date('Y-m-d H:i:s')]);
                        session(['success_message' => 'Assignment Files saved successfully']);

                    }
                }} else {
                session(['error_message' => 'Assignment Files not saved']);
            }
        }
        return redirect()->back();

    }

    public function actiongoaltask(Request $request, $id)
    {
        $goalTask = DB::table('goaltask')->select('*')->where('id', $id)->first();

        $type = $goalTask->type;

        $affiliate_data = get_single_data_id('admins', Auth::user()->assigned_by);
        $timezone = $affiliate_data->timezone;

        $assign_goal = DB::table('assign_task')->select('*')->where('goaltask_id', $id)->first();

        if (!empty($timezone)) {
            date_default_timezone_set($timezone);
            $complated_date = date('Y-m-d H:i:s');
        } else {
            $complated_date = date('Y-m-d H:i:s');
        }

        if ($type == "task") {
            if ($assign_goal->status === 0) {
                DB::table('assign_task')
                    ->where('assign_task.victim_id', Auth::user()->id)
                    ->where('assign_task.goaltask_id', $id)
                    ->update(['status' => 1, 'begin_time' => $complated_date]);

            }else {
                DB::table('assign_task')
                    ->where('assign_task.victim_id', Auth::user()->id)
                    ->where('assign_task.goaltask_id', $id)
                    ->update(['status' => 2, 'complated_time' => $complated_date]);

            }
        }

        return redirect()->back();

    }

    public function filedeletegoaltask(Request $request,$goaltaskuserfiles_id)
    {
        $user_id = Auth::user()->id;

        $delelteFIle = DB::table('goaltaskuserfiles')
                        ->select('*')
                        ->where('goaltaskuserfiles.added_by',$user_id)
                        ->where('goaltaskuserfiles.id',$goaltaskuserfiles_id)
                        ->first();
        if($delelteFIle){
        DB::table('goaltaskuserfiles')->where('goaltaskuserfiles.id',$goaltaskuserfiles_id)->delete();

        session(['success_message' => 'Assignment File deleted successfully']);

        return redirect()->back();
        } else {
            session(['error_message' => 'Assignment File cannot be deleted.']);

            return redirect()->back();
        }
    }
}
