<?php

namespace App\Exceptions;

use Throwable;
use App\Traits\ApiResponser;
use Illuminate\Database\QueryException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;


class Handler extends ExceptionHandler
{
    use ApiResponser;
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
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
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        // bypass parent render 
        if($exception instanceof ValidationException) {
            return $this->changeValidationExceptionToJson($exception, $request);

        } else if ($exception instanceof ModelNotFoundException) {
            $model = strtolower(class_basename($exception->getModel()));
            return $this->errorResponse(["error" => "data {$model} doesnt exist.
            if error persists, contact contact@william1099.me"], 404);

        } else if ($exception instanceof NotAdminException) {
            
            return $exception->render($request);
        } else if($exception instanceof AuthenticationException) {

            if($this->isFrontEnd($request)) {
                return redirect("login");
            }
            return $this->errorResponse(["error" => "not logged in"], 401);

        } else if($exception instanceof AuthorizationException) {
            return $this->errorResponse(["error" => "Forbidden"], 403);

        } else if($exception instanceof NotFoundHttpException) {
            return $this->errorResponse(["error" => "the specified url is not found"], 404);

        } else if($exception instanceof MethodNotAllowedHttpException) {
            $method = $request->method;
            return $this->errorResponse(["error" => "{$method} is not allowed"], 405);

        } else if($exception instanceof HttpException) {
            return $this->errorResponse(["error" => $exception->getMessage()], $exception->getStatusCode());

        } else if($exception instanceof QueryException) {
            
            $error_code = $exception->errorInfo[1];
            
            if($error_code == 1451) {

                return $this->errorResponse(["error" => "data tidak dapat dihapus karena masih terdapat data yang berhubungan di tabel lain"], 409);
            }
            

        } else if($exception instanceof TokenMismatchException) {
            return redirect()->back()->withInput($request->input());
        }
        
        if(!config("app.debug")) {
            return $this->errorResponse(["error" => "Unexpected exception, try again later :)"], 500);
        }
        
        return parent::render($request, $exception);
    }

    // mengubah validation exception response ke bentuk json
    private function changeValidationExceptionToJson(ValidationException $e, $request)
    {   
        $errors = $e->validator->errors()->getMessages();
        if($this->isFrontEnd($request)) {
            return $request->ajax() ? response()->json(["error" => $errors], 422) : 
                redirect()->back()->withInput($request->input())->withErrors($errors);
        }

        return $this->errorResponse(["error" => $errors], 422);

    }

    private function isFrontEnd($request) {
        return ($request->acceptsHtml() && collect($request->route()->middleware())->contains("web"));
    }
}
