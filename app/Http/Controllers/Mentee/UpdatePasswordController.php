<?php

namespace App\Http\Controllers\Mentee;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;


class UpdatePasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mentee');
    }
    public function edit(Request $request)
    {
        return view('mentee.update-password');
    }

    public function update(Request $request)
    {
        if(Auth::guard('mentee')->check()) {
            $user = Auth::guard('mentee')->user();

            $password = $request->get('password');

            $validator = Validator::make($request->all(), [
                'password' => 'required',
                'password_confirmation' => 'required|same:password',
            ]);

            if ($validator->fails()) {
                session(['error_message' => $validator->errors()->first()]);
                return back();
            }

            DB::table('mentee')->where('id', $user->id)->update(['password' => Hash::make($password)]);

            session(['success_message' => "Password Changed Successfully"]);
            return redirect()->route('mentee.password.edit');
        } else {
            abort(403, 'Unauthorized action.');
        }
    }
}
