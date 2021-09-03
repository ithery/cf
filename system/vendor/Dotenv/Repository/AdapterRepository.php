<?php



namespace Dotenv\Repository;

use Dotenv\Repository\Adapter\ReaderInterface;
use Dotenv\Repository\Adapter\WriterInterface;

final class AdapterRepository implements RepositoryInterface
{
    /**
     * The reader to use.
     *
     * @var \Dotenv\Repository\Adapter\ReaderInterface
     */
    private $reader;

    /**
     * The writer to use.
     *
     * @var \Dotenv\Repository\Adapter\WriterInterface
     */
    private $writer;

    /**
     * Create a new adapter repository instance.
     *
     * @param \Dotenv\Repository\Adapter\ReaderInterface $reader
     * @param \Dotenv\Repository\Adapter\WriterInterface $writer
     *
     * @return void
     */
    public function __construct(ReaderInterface $reader, WriterInterface $writer)
    {
        $this->reader = $reader;
        $this->writer = $writer;
    }

    /**
     * Determine if the given environment variable is defined.
     *
     * @param string $name
     *
     * @return bool
     */
    public function has( $name)
    {
        return $this->reader->read($name)->isDefined();
    }

    /**
     * Get an environment variable.
     *
     * @param string $name
     *
     * @return string|null
     */
    public function get( $name)
    {
        return $this->reader->read($name)->getOrElse(null);
    }

    /**
     * Set an environment variable.
     *
     * @param string $name
     * @param string $value
     *
     * @return bool
     */
    public function set( $name,  $value)
    {
        return $this->writer->write($name, $value);
    }

    /**
     * Clear an environment variable.
     *
     * @param string $name
     *
     * @return bool
     */
    public function clear( $name)
    {
        return $this->writer->delete($name);
    }
}
