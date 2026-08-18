<?php

declare(strict_types=1);

namespace CuyZ\Valinor\Mapper;

use RuntimeException;
use CuyZ\Valinor\Utility\ValueDumper;
use CuyZ\Valinor\Mapper\Tree\Message\Messages;
use CuyZ\Valinor\Mapper\Tree\Message\NodeMessage;

/** @internal */
final class ArgumentsMapperError extends RuntimeException implements MappingError {
    private Messages $messages;

    private string $type;

    /**
     * @var mixed
     */
    private $source;

    /**
     * @param non-empty-list<NodeMessage> $messages
     * @param mixed                       $source
     */
    public function __construct($source, string $type, string $function, array $messages) {
        $this->messages = new Messages(...$messages);
        $this->type = $type;
        $this->source = $source;

        $errorsCount = count($messages);

        if ($errorsCount === 1) {
            $body = $messages[0]
                ->withBody("Could not map arguments of `$function`. An error occurred at path {node_path}: {original_message}")
                ->toString();
        } else {
            $source = ValueDumper::dump($source);
            $body = "Could not map arguments of `$function` with value $source. A total of $errorsCount errors were encountered.";
        }

        parent::__construct($body, 1671115362);
    }

    public function messages(): Messages {
        return $this->messages;
    }

    public function type(): string {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function source() {
        return $this->source;
    }
}
