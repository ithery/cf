<?php
/**
 * @see CHTTP
 */
class CHTTP_RobotsTxt {
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
        if (static::$instance == null) {
            static::$instance = new static();
        }

        return static::$instance;
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
     */
    public function addSitemap($sitemap) {
        $this->addLine("Sitemap: ${sitemap}");
    }

    /**
     * Add a User-agent to the robots.txt.
     *
     * @param string $userAgent
     */
    public function addUserAgent($userAgent) {
        $this->addLine("User-agent: ${userAgent}");
    }

    /**
     * Add a Host to the robots.txt.
     *
     * @param string $host
     */
    public function addHost($host) {
        $this->addLine("Host: ${host}");
    }

    /**
     * Add a disallow rule to the robots.txt.
     *
     * @param string|array $directories
     */
    public function addDisallow($directories) {
        $this->addRuleLine($directories, 'Disallow');
    }

    /**
     * Add a allow rule to the robots.txt.
     *
     * @param string|array $directories
     */
    public function addAllow($directories) {
        $this->addRuleLine($directories, 'Allow');
    }

    /**
     * Add a rule to the robots.txt.
     *
     * @param string|array $directories
     * @param string       $rule
     */
    public function addRuleLine($directories, $rule) {
        foreach ((array) $directories as $directory) {
            $this->addLine("${rule}: ${directory}");
        }
    }

    /**
     * Add a comment to the robots.txt.
     *
     * @param string $comment
     */
    public function addComment($comment) {
        $this->addLine("# ${comment}");
    }

    /**
     * Add a spacer to the robots.txt.
     */
    public function addSpacer() {
        $this->addLine('');
    }

    /**
     * Add a line to the robots.txt.
     *
     * @param string $line
     */
    public function addLine($line) {
        $this->lines[] = $line;
    }

    /**
     * Add multiple lines to the robots.txt.
     *
     * @param string|array $lines
     */
    protected function addLines($lines) {
        foreach ((array) $lines as $line) {
            $this->addLine($line);
        }
    }

    /**
     * Reset the lines.
     *
     * @return void
     */
    public function reset() {
        $this->lines = [];
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
}
