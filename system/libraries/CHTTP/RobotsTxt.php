<?php
/**
 * @see CHTTP
 */
final class CHTTP_RobotsTxt {
    /**
     * The lines of for the robots.txt.
     *
     * @var array
     */
    protected $lines = [];

    /**
     * @var callable|bool
     */
    protected static $shouldIndex = true;

    private static $instance;

    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Generate the robots.txt data.
     *
     * @return string
     */
    public function generate() {
        return implode(PHP_EOL, $this->lines);
    }

    /**
     * Add a Sitemap to the robots.txt.
     *
     * @param string $sitemap
     *
     * @return $this
     */
    public function addSitemap($sitemap) {
        return $this->addLine("Sitemap: ${sitemap}");
    }

    /**
     * Add a User-agent to the robots.txt.
     *
     * @param string $userAgent
     *
     * @return $this
     */
    public function addUserAgent($userAgent) {
        return $this->addLine("User-agent: ${userAgent}");
    }

    /**
     * Add a Host to the robots.txt.
     *
     * @param string $host
     *
     * @return $this
     */
    public function addHost($host) {
        return $this->addLine("Host: ${host}");
    }

    /**
     * Add a disallow rule to the robots.txt.
     *
     * @param string|array $directories
     *
     * @return $this
     */
    public function addDisallow($directories) {
        return $this->addRuleLine($directories, 'Disallow');
    }

    /**
     * Add a allow rule to the robots.txt.
     *
     * @param string|array $directories
     *
     * @return $this
     */
    public function addAllow($directories) {
        return $this->addRuleLine($directories, 'Allow');
    }

    /**
     * Add a rule to the robots.txt.
     *
     * @param string|array $directories
     * @param string       $rule
     *
     * @return $this
     */
    public function addRuleLine($directories, $rule) {
        foreach ((array) $directories as $directory) {
            $this->addLine("${rule}: ${directory}");
        }

        return $this;
    }

    /**
     * Add a comment to the robots.txt.
     *
     * @param string $comment
     *
     * @return $this
     */
    public function addComment($comment) {
        return $this->addLine("# ${comment}");
    }

    /**
     * Add a spacer to the robots.txt.
     *
     * @return $this
     */
    public function addSpacer() {
        return $this->addLine('');
    }

    /**
     * Add a line to the robots.txt.
     *
     * @param string $line
     *
     * @return $this
     */
    public function addLine($line) {
        $this->lines[] = $line;

        return $this;
    }

    /**
     * Add multiple lines to the robots.txt.
     *
     * @param string|array $lines
     *
     * @return $this
     */
    protected function addLines($lines) {
        foreach ((array) $lines as $line) {
            $this->addLine($line);
        }

        return $this;
    }

    /**
     * Reset the lines.
     *
     * @return $this
     */
    public function reset() {
        $this->lines = [];

        return $this;
    }

    /**
     * Set callback with should index condition.
     */
    public function setShouldIndexCallback(callable $callback) {
        self::$shouldIndex = $callback;
    }

    /**
     * Check is application should be indexed.
     */
    public function shouldIndex() {
        if (is_callable(self::$shouldIndex)) {
            return (bool) call_user_func(self::$shouldIndex);
        }

        return self::$shouldIndex;
    }

    /**
     * Render robots meta tag.
     */
    public function metaTag() {
        return '<meta name="robots" content="' . ($this->shouldIndex() ? 'index, follow' : 'noindex, nofollow') . '">';
    }

    public function toResponse() {
        return c::response($this->generate(), 200, ['Content-Type' => 'text/plain']);
    }
}
