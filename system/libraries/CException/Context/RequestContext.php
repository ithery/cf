<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mime\Exception\InvalidArgumentException;

class CException_Context_RequestContext extends CException_ContextAbstract implements CException_Contract_ContextInterface {
    /**
     * @var null|\Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    public function __construct(Request $request = null) {
        $this->request = $request ?: Request::createFromGlobals();
    }

    public function getRequest() {
        return [
            'url' => $this->request->getUri(),
            'ip' => $this->request->getClientIp(),
            'method' => $this->request->getMethod(),
            'useragent' => $this->request->headers->get('User-Agent'),
        ];
    }

    private function getFiles() {
        if (is_null($this->request->files)) {
            return [];
        }

        return $this->mapFiles($this->request->files->all());
    }

    protected function mapFiles(array $files) {
        return array_map(function ($file) {
            if (is_array($file)) {
                return $this->mapFiles($file);
            }

            if (!$file instanceof UploadedFile) {
                return;
            }

            try {
                $fileSize = $file->getSize();
            } catch (\RuntimeException $e) {
                $fileSize = 0;
            }

            try {
                $mimeType = $file->getMimeType();
                if ($mimeType == null) {
                    $mimeType = 'undefined';
                }
            } catch (InvalidArgumentException $e) {
                $mimeType = 'undefined';
            }

            return [
                'pathname' => $file->getPathname(),
                'size' => $fileSize,
                'mimeType' => $mimeType,
            ];
        }, $files);
    }

    public function getSession() {
        try {
            //$session = $this->request->getSession();
            $session = CSession::instance();
        } catch (\Exception $exception) {
            $session = [];
        }

        return $session ? $this->getValidSessionData($session) : [];
    }

    /**
     * @param SessionInterface $session
     *
     * @return array
     */
    protected function getValidSessionData($session) {
        try {
            json_encode($session->all());
        } catch (Throwable $e) {
            return [];
        }

        return $session->all();
    }

    public function getCookies() {
        return $this->request->cookies->all();
    }

    public function getHeaders() {
        return $this->request->headers->all();
    }

    public function getRequestData() {
        return [
            'queryString' => $this->request->query->all(),
            'body' => $this->request->request->all(),
            'files' => $this->getFiles(),
        ];
    }

    public function toArray() {
        return [
            'request' => $this->getRequest(),
            'request_data' => $this->getRequestData(),
            'headers' => $this->getHeaders(),
            'cookies' => $this->getCookies(),
            'session' => $this->getSession(),
            'user' => $this->getUser(),
            'route' => $this->getRoute(),
            'git' => $this->getGit(),
        ];
    }

    public function getRoute() {
        return [
            'route' => CFRouter::$routed_uri,
            'routeParameters' => CFRouter::$arguments,
            'controllerAction' => CFRouter::$controller . '@' . CFRouter::$method,
            'middleware' => [],
        ];
    }

    public function getUser() {
        try {
            $user = c::app()->user();

            if (!$user) {
                return [];
            }
        } catch (Throwable $e) {
            return [];
        }

        try {
            if ($user instanceof CInterface_Arrayable) {
                return $user->toArray();
            }
            if ($user instanceof CModel) {
                return $user->getAttributes();
            }

            return (array) $user;
        } catch (Throwable $e) {
            return [];
        }

        return [];
    }
}
