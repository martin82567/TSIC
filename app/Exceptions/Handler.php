<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Foundation\Http\Exceptions\MaintenanceModeException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Session\TokenMismatchException;

use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
        MaintenanceModeException::class,

        SuspiciousOperationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof NotFoundHttpException) {
            // return response()->json(['status'=>false, 'message'=>"Page not found"]);
            return redirect('/404');

        }else if($exception instanceof ModelNotFoundException){
            return response()->json(['status'=>false, 'message'=>"Model not found"]);

        }else if($exception instanceof MethodNotAllowedHttpException){
            return response()->json(['status'=>false, 'message'=>"Unknown method"]);

        }else if($exception instanceof TokenMismatchException){
            // return view('token-error');
            // return response()->json(['status'=>false, 'message'=>"Timeout"]);
            return redirect('/404');
        }else if($exception instanceof MaintenanceModeException){
            // return view('token-error');
            // return response()->json(['status'=>false, 'message'=>"Timeout"]);
            if (starts_with($request->getRequestUri(), "/api/")) {
                return response()->json(['status'=>false, 'message'=>"HOLIDAY MAINTENANCE SHUTDOWN NOTICE: The Take Stock App will be brought down for maintenance at 12:00 pm (EST) on Friday, June 14, 2024, and will go live at 8:00 am (EST) on Thursday, August 1, 2024."]);
            } else {
                return parent::render($request, $exception);
            }
        }else{

            return parent::render($request, $exception);
        }

    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }
        $guard = array_get($exception->guards(), 0);
        switch ($guard) {
       case 'admin':
            $login = 'admin.login';
            break;
        default:
            $login = 'login';
            break;
        }
        return redirect()->guest(route($login));
    }

    protected function prepareException(Exception $e)
    {
        if ($e instanceof SuspiciousOperationException) {
            $e = new NotFoundHttpException(null, $e);
        }

        return parent::prepareException($e);
    }


}
