<?php

class CException_Renderer_ExceptionRenderer implements CException_Contract_ExceptionRendererInterface {
    protected $shouldDisplayException = true;

    /**
     * Renders the given exception as HTML.
     *
     * @param \Throwable $throwable
     *
     * @return string
     */
    public function render($throwable) {
        if ($this->shouldDisplayException && CF::isProduction() !== true) {
            $this->renderException($throwable);
        }
    }

    public function renderException($throwable) {
        $viewModel = CException::manager()->createErrorModel($throwable);

        try {
            $template = 'errors/exception-ignition';
            $data = $viewModel->toArray();
            $viewFile = CF::findFile('views', $template);

            extract((array) $data, EXTR_OVERWRITE);

            include $viewFile;
        } catch (Throwable $e) {
            throw $e;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
