<?php

/**
 * Description of PhpEngine
 *
 * @author Hery
 */
class CView_Engine_PhpEngine extends CView_EngineAbstract {
    /**
     * The filesystem instance.
     *
     * @var CStorage_Adapter
     */
    protected $files;

    /**
     * Create a new file engine instance.
     *
     * @return void
     */
    public function __construct() {
        $this->files = CStorage::instance()->disk('local');
    }

    /**
     * Get the evaluated contents of the view.
     *
     * @param string $path
     * @param array  $data
     *
     * @return string
     */
    public function get($path, array $data = []) {
        return $this->evaluatePath($path, $data);
    }

    /**
     * Get the evaluated contents of the view at the given path.
     *
     * @param string $path
     * @param array  $data
     *
     * @return string
     */
    protected function evaluatePath($path, $data) {
        $obLevel = ob_get_level();

        ob_start();

        // We'll evaluate the contents of the view inside a try/catch block so we can
        // flush out any stray output that might get out before an error occurs or
        // an exception is thrown. This prevents any partial views from leaking.
        try {
            CFile::getRequire($path, $data);
        } catch (Exception $e) {
            $this->handleViewException($e, $obLevel);
        } catch (Throwable $e) {
            $this->handleViewException($e, $obLevel);
        }

        return ltrim(ob_get_clean());
    }

    /**
     * Handle a view exception.
     *
     * @param \Throwable $e
     * @param int        $obLevel
     *
     * @return void
     *
     * @throws \Throwable
     */
    protected function handleViewException($e, $obLevel) {
        while (ob_get_level() > $obLevel) {
            ob_end_clean();
        }

        throw $e;
    }
}
