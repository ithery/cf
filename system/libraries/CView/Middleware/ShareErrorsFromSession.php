<?php

class CView_Middleware_ShareErrorsFromSession {
    /**
     * The view factory implementation.
     *
     * @var \CView_Factory
     */
    protected $view;

    /**
     * Create a new error binder instance.
     */
    public function __construct() {
        $this->view = CView::factory();
    }

    /**
     * Handle an incoming request.
     *
     * @param \CHTTP_Request $request
     * @param \Closure       $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next) {
        // If the current session has an "errors" variable bound to it, we will share
        // its value with all view instances so the views can easily access errors
        // without having to bind. An empty bag is set when there aren't errors.
        $this->view->share(
            'errors',
            $request->session()->get('errors') ?: new CBase_ViewErrorBag()
        );

        // Putting the errors in the view for every view allows the developer to just
        // assume that some errors are always available, which is convenient since
        // they don't have to continually run checks for the presence of errors.

        return $next($request);
    }
}
