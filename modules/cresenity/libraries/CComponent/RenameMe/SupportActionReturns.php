<?php




use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CComponent_RenameMe_SupportActionReturns
{
    static function init() { return new static; }

    protected $returnsByIdAndAction = [];

    function __construct()
    {
        CComponent_Manager::instance()->listen('action.returned', function ($component, $action, $returned) {
            if (is_array($returned) || is_numeric($returned) || is_bool($returned) || is_string($returned)) {
                if (! isset($this->returnsByIdAndAction[$component->id])) $this->returnsByIdAndAction[$component->id] = [];

                $this->returnsByIdAndAction[$component->id][$action] = $returned;
            }
        });

        CComponent_Manager::instance()->listen('component.dehydrate.subsequent', function ($component, $response) {
            if (! isset($this->returnsByIdAndAction[$component->id])) return;

            $response->effects['returns'] = $this->returnsByIdAndAction[$component->id];
        });
    }

    function valueIsntAFileResponse($value)
    {
        return ! $value instanceof StreamedResponse
            && ! $value instanceof BinaryFileResponse;
    }

    function captureOutput($callback)
    {
        ob_start();

        $callback();

        return ob_get_clean();
    }
}
