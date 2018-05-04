<?php

namespace App\Exceptions;

//use Exception;
//use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
//use Encore\Admin\Reporter\Reporter;
//use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
//use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;


class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
	    NotFoundHttpException::class,
	    AuthorizationException::class,
	    HttpException::class,
	    ModelNotFoundException::class,
	    ValidationException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];
	
	/**
	 * Report or log an exception.
	 * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
	 * @param  \Exception $exception
	 * @return mixed
	 * @throws Exception
	 */
	public function report(Exception $exception)
    {
        //parent::report($exception);
	    if($this->shouldReport($exception)) {
		    while(ob_get_level() > 0) {
			    ob_end_clean();
		    }
		    // if(class_exists(Reporter::class)) {
			 //    return Reporter::report($exception);
		    // }
		    return parent::report($exception);
	    }
    }

    /**
     * Render an exception into an HTTP response.
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response|SymfonyResponse
     */
    public function render($request, Exception $exception)
    {
	    if($this->shouldntReport($exception)) {
		    //return new SymfonyResponse('', 200);
		    return new \Illuminate\Http\Response('', 200);
		    //'';// Response::make( '', 200 )->setStatusCode( 200 );
	    }
	    
	    $_SERVER = array_diff_assoc($_SERVER, $_ENV);
	    $_ENV = [];
        return parent::render($request, $exception);
    }
}
