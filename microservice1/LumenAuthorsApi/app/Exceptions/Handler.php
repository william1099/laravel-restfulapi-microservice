<?php

namespace App\Exceptions;

use Throwable;
use App\Traits\ApiResponser;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    use ApiResponser;
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if($exception instanceof ValidationException) {
            return $this->changeValidationExceptionToJson($exception, $request);

        } else if ($exception instanceof ModelNotFoundException) {
            $model = strtolower(class_basename($exception->getModel()));
            return $this->errorResponse("data {$model} doesnt exist. If error persists, contact contact@william1099.me", 404);

        } else if($exception instanceof AuthenticationException) {
            return $this->errorResponse("unauthenticated", 401);

        } else if($exception instanceof AuthorizationException) {
            return $this->errorResponse("Forbidden", 403);

        } else if($exception instanceof NotFoundHttpException) {
           
            return $this->errorResponse("the specified url is not found", 404);

        } else if($exception instanceof MethodNotAllowedHttpException) {
            $method = $request->method;
            return $this->errorResponse("{$method} is not allowed", 405);

        } else if($exception instanceof HttpException) {
            return $this->errorResponse($exception->getMessage(), $exception->getStatusCode());

        } else if($exception instanceof QueryException) {
            
            $error_code = $exception->errorInfo[1];
            
            if($error_code == 1451) {

                return $this->errorResponse("data tidak dapat dihapus karena masih terdapat data yang berhubungan di tabel lain", 409);
            }
            

        } else if($exception instanceof TokenMismatchException) {
            return redirect()->back()->withInput($request->input());
        }
        
        if(!config("app.debug")) {
            return $this->errorResponse("Unexpected exception, try again later :)", 500);
        }

        return parent::render($request, $exception);
    }


    private function changeValidationExceptionToJson(ValidationException $e, $request)
    {   
        $errors = $e->validator->errors()->getMessages();

        return $this->errorResponse($errors, 422);

    }

}
