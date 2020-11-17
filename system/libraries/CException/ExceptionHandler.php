<?php

defined('SYSPATH') OR die('No direct access allowed.');

use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\ErrorHandler\ErrorRenderer\HtmlErrorRenderer;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Whoops\Handler\HandlerInterface;
use Whoops\Run as Whoops;

/**
 * @author Hery Kurniawan
 * @since Sep 8, 2019, 5:16:49 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CException_ExceptionHandler implements CException_ExceptionHandlerInterface {

    use CTrait_ReflectsClosureTrait;

    /**
     * The container implementation.
     *
     * @var CContainer_Container
     */
    protected $container;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [];

    /**
     * The callbacks that should be used during reporting.
     *
     * @var array
     */
    protected $reportCallbacks = [];

    /**
     * The callbacks that should be used during rendering.
     *
     * @var array
     */
    protected $renderCallbacks = [];

    /**
     * A list of the internal exception types that should not be reported.
     *
     * @var array
     */
    protected $internalDontReport = [
        //AuthenticationException::class,
        //AuthorizationException::class,
        HttpException::class,
        CHTTP_Exception_ResponseException::class,
        CModel_Exception_ModelNotFound::class,
            //SuspiciousOperationException::class,
            //TokenMismatchException::class,
            //ValidationException::class,
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
     * Create a new exception handler instance.
     *
     * @param  CContainer_Container  $container
     * @return void
     */
    public function __construct() {
        $this->container = CContainer_Container::getInstance();
    }

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $e
     * @return mixed
     *
     * @throws \Exception
     */
    public function report($e) {
        if ($this->shouldntReport($e)) {
            return;
        }
        if (is_callable($reportCallable = [$e, 'report'])) {
            return $this->container->call($reportCallable);
        }


        foreach ($this->reportCallbacks as $reportCallback) {

            if ($reportCallback->handles($e)) {

                if ($reportCallback($e) === false) {
                    return;
                }
            }
        }



        CLogger::instance()->add(CLogger::ERROR, $e->getMessage(), null, $this->context(), $e);
//        try {
//            CLogger::instance()->add($reportCallable, $message)
//            $logger = $this->container->make(LoggerInterface::class);
//        } catch (Exception $ex) {
//            throw $e;
//        }
//        $logger->error(
//                $e->getMessage(), array_merge($this->context(), ['exception' => $e]
//        ));
    }

    /**
     * Determine if the exception should be reported.
     *
     * @param  \Exception  $e
     * @return bool
     */
    public function shouldReport($e) {
        return !$this->shouldntReport($e);
    }

    /**
     * Determine if the exception is in the "do not report" list.
     *
     * @param  \Exception  $e
     * @return bool
     */
    protected function shouldntReport($e) {
        $dontReport = array_merge($this->dontReport, $this->internalDontReport);
        return !is_null(carr::first($dontReport, function ($type) use ($e) {
                            return $e instanceof $type;
                        }));
    }

    /**
     * Get the default context variables for logging.
     *
     * @return array
     */
    protected function context() {
        try {
            return array_filter([
                'domain' => CF::domain(),
                'appCode' => CF::appCode(),
                'appId' => CF::appId(),
                'orgCode' => CF::orgCode(),
                'orgId' => CF::orgId(),
            ]);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Render an exception into a response.
     *
     * @param  \CHTTP_Request $request
     * @param  \Exception  $e
     * @return \CHTTP_Response|\Symfony\Component\HttpFoundation\Response
     */
    public function render($request, $e) {

        if ($this->isHttpException($e)) {
            if (CView::exists('errors/http/' . $e->getStatusCode())) {
                return CF::response()->view('errors/http/' . $e->getStatusCode(), [], $e->getStatusCode());
            } else {
                if ($e->getStatusCode() == 404) {
                    //backward compatibility old view
                    if (CView::exists('ccore/404')) {
                        return CF::response()->view('ccore/404', [], $e->getStatusCode());
                    }
                }
            }
        }

        if (method_exists($e, 'render') && $response = $e->render($request)) {
            return Router::toResponse($request, $response);
        } elseif ($e instanceof CInterface_Responsable) {
            return $e->toResponse($request);
        }

        $e = $this->prepareException($e);

        foreach ($this->renderCallbacks as $renderCallback) {
            if (is_a($e, $this->firstClosureParameterType($renderCallback))) {
                $response = $renderCallback($e, $request);

                if (!is_null($response)) {
                    return $response;
                }
            }
        }


        if ($e instanceof CHTTP_Exception_ResponseException) {
            return $e->getResponse();
        } elseif ($e instanceof AuthenticationException) {
            return $this->unauthenticated($request, $e);
        } elseif ($e instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($e, $request);
        }

        return $request->expectsJson() ? $this->prepareJsonResponse($request, $e) : $this->prepareResponse($request, $e);
    }

    /**
     * Prepare exception for rendering.
     *
     * @param  \Exception  $e
     * @return \Exception
     */
    protected function prepareException($e) {
        if ($e instanceof CModel_Exception_ModelNotFound) {
            $e = new NotFoundHttpException($e->getMessage(), $e);
        } elseif ($e instanceof AuthorizationException) {
            $e = new AccessDeniedHttpException($e->getMessage(), $e);
        } elseif ($e instanceof TokenMismatchException) {
            $e = new HttpException(419, $e->getMessage(), $e);
        } elseif ($e instanceof SuspiciousOperationException) {
            $e = new NotFoundHttpException('Bad hostname provided.', $e);
        }
        return $e;
    }

    /**
     * Convert an authentication exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception) {
        return $request->expectsJson() ? response()->json(['message' => $exception->getMessage()], 401) : redirect()->guest($exception->redirectTo() ? $exception->redirectTo() : route('login'));
    }

    /**
     * Create a response object from the given validation exception.
     *
     * @param  \Illuminate\Validation\ValidationException  $e
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function convertValidationExceptionToResponse(ValidationException $e, $request) {
        if ($e->response) {
            return $e->response;
        }
        return $request->expectsJson() ? $this->invalidJson($request, $e) : $this->invalid($request, $e);
    }

    /**
     * Convert a validation exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Validation\ValidationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function invalid($request, ValidationException $exception) {
        return CF::redirect(isset($exception->redirectTo) ? $exception->redirectTo : url()->previous())
                        ->withInput(crr::except($request->input(), $this->dontFlash))
                        ->withErrors($exception->errors(), $exception->errorBag);
    }

    /**
     * Convert a validation exception into a JSON response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Validation\ValidationException  $exception
     * @return \Illuminate\Http\JsonResponse
     */
    protected function invalidJson($request, ValidationException $exception) {
        return response()->json([
                    'message' => $exception->getMessage(),
                    'errors' => $exception->errors(),
                        ], $exception->status);
    }

    /**
     * Prepare a response for the given exception.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function prepareResponse($request, $e) {

        if (!$this->isHttpException($e) && $this->isDebug()) {
            return $this->toHttpResponse($this->convertExceptionToResponse($e), $e);
        }
        if (!$this->isHttpException($e)) {
            $e = new CHTTP_Exception_HttpException(500, $e->getMessage());
        }

        $response = $this->toHttpResponse(
                $this->renderHttpException($e), $e
        );

        return $response;
    }

    /**
     * Create a Symfony response for the given exception.
     *
     * @param  \Exception  $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function convertExceptionToResponse($e) {

        $response = SymfonyResponse::create(
                        $this->renderExceptionContent($e), $this->isHttpException($e) ? $e->getStatusCode() : 500, $this->isHttpException($e) ? $e->getHeaders() : []
        );

        return $response;
    }

    /**
     * Get the response content for the given exception.
     *
     * @param  \Exception  $e
     * @return string
     */
    protected function renderExceptionContent($e) {
        try {
            return CException_LegacyExceptionHandler::getContent($e);
            //return $this->isDebug() && class_exists(Whoops::class) ? $this->renderExceptionWithWhoops($e) : $this->renderExceptionWithSymfony($e, $this->isDebug());
            //return $this->renderExceptionWithSymfony($e, $this->isDebug());
        } catch (Exception $e) {
            return $this->renderExceptionWithSymfony($e, $this->isDebug());
        }
    }

    /**
     * Render an exception to a string using "Whoops".
     *
     * @param  \Exception  $e
     * @return string
     */
    protected function renderExceptionWithWhoops($e) {
        return c::tap(new Whoops, function ($whoops) {
                    $whoops->appendHandler($this->whoopsHandler());
                    $whoops->writeToOutput(false);
                    $whoops->allowQuit(false);
                })->handleException($e);
    }

    /**
     * Get the Whoops handler for the application.
     *
     * @return \Whoops\Handler\Handler
     */
    protected function whoopsHandler() {
        try {
            return new \Whoops\Handler\PrettyPageHandler();
        } catch (BindingResolutionException $e) {
            return (new WhoopsHandler)->forDebug();
        }
    }

    /**
     * Render an exception to a string using Symfony.
     *
     * @param  \Exception  $e
     * @param  bool  $debug
     * @return string
     */
    protected function renderExceptionWithSymfony($e, $debug) {
        $renderer = new HtmlErrorRenderer($debug);


        return $renderer->render($e)->getAsString();
    }

    /**
     * Render the given HttpException.
     *
     * @param  \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface  $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderHttpException(HttpExceptionInterface $e) {
        $this->registerErrorViewPaths();
        $viewName = 'errors/exception';
        if (CView::exists('errors/http/' . $e->getStatusCode())) {
            $viewName = 'errors/http/' . $e->getStatusCode();
        }
        /*
          if (view()->exists($view = "errors::{$e->getStatusCode()}")) {
          return response()->view($view, [
          'errors' => new ViewErrorBag,
          'exception' => $e,
          ], $e->getStatusCode(), $e->getHeaders());
          }
         * 
         */
        return $this->convertExceptionToResponse($e);
    }

    /**
     * Register the error template hint paths.
     *
     * @return void
     */
    protected function registerErrorViewPaths() {
        return c::collect(CF::paths())->map(function($path) {
                    return $path . 'views';
                });


        $paths = c::collect(CF::paths());
        View::replaceNamespace('errors', $paths->map(function ($path) {
                    return "{$path}/errors";
                })->push(__DIR__ . '/views')->all());
    }

    /**
     * Map the given exception into an Illuminate response.
     *
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @param  \Exception  $e
     * @return CHTTP_Response
     */
    protected function toHttpResponse($response, $e) {

        if ($response instanceof SymfonyRedirectResponse) {
            $response = new CHTTP_RedirectResponse(
                    $response->getTargetUrl(), $response->getStatusCode(), $response->headers->all()
            );
        } else {
            $response = new CHTTP_Response(
                    $response->getContent(), $response->getStatusCode(), $response->headers->all()
            );
        }

        return $response->withException($e);
    }

    /**
     * Prepare a JSON response for the given exception.
     *
     * @param  CHTTP_Request  $request
     * @param  \Exception $e
     * @return CHTTP_JsonResponse
     */
    protected function prepareJsonResponse($request, $e) {
        return new CHTTP_JsonResponse(
                $this->convertExceptionToArray($e), $this->isHttpException($e) ? $e->getStatusCode() : 500, $this->isHttpException($e) ? $e->getHeaders() : [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
    }

    /**
     * Convert the given exception to an array.
     *
     * @param  \Exception  $e
     * @return array
     */
    protected function convertExceptionToArray($e) {
        $result = [
            'message' => $this->isHttpException($e) ? $e->getMessage() : 'Server Error',
        ];
        if ($this->isDebug()) {
            $trace = c::collect($e->getTrace())->map(function ($trace) {
                        return carr::except($trace, ['args']);
                    })->all();
            $result = [
                'message' => $e->getMessage(),
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $trace,
            ];
        }
        return $result;
    }

    protected function isDebug() {
        $isDebug = CF::config('core.debug');
        if ($isDebug === null) {
            $isDebug = !CF::isProduction();
        }

        return $isDebug;
    }

    /**
     * Render an exception to the console.
     *
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @param  \Exception  $e
     * @return void
     */
    public function renderForConsole($output, $e) {
        (new ConsoleApplication)->renderException($e, $output);
    }

    /**
     * Determine if the given exception is an HTTP exception.
     *
     * @param  \Exception  $e
     * @return bool
     */
    protected function isHttpException($e) {
        return $e instanceof HttpExceptionInterface;
    }

    /**
     * Register a reportable callback.
     *
     * @param  callable  $reportUsing
     * @return CException_ReportableHandler
     */
    public function reportable(callable $reportUsing) {
        return c::tap(new CException_ReportableHandler($reportUsing), function ($callback) {

                    $this->reportCallbacks[] = $callback;
                });
    }

    /**
     * Register a renderable callback.
     *
     * @param  callable  $renderUsing
     * @return $this
     */
    public function renderable(callable $renderUsing) {
        $this->renderCallbacks[] = $renderUsing;

        return $this;
    }

}
