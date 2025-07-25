<?php
namespace App\Http\Controllers;
 
use App\Http\Controllers\Controller;
 
class ErrorController extends Controller
{
    public function four_not_four()
    {
        return view('errors.401');
    }
    
    public function index()
    {
        return response()->json(['error' => 'Custom error page']);
    }
}