<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Question;

use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Exception\InvalidArgumentException;

/**
 * Represents a Question.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Question {
    private $question;

    private $attempts;

    private $hidden = false;

    private $hiddenFallback = true;

    private $autocompleterCallback;

    private $validator;

    private $default;

    private $normalizer;

    private $trimmable = true;

    private $multiline = false;

    /**
     * @param string $question The question to ask to the user
     * @param mixed  $default  The default answer to return if the user enters nothing
     */
    public function __construct($question, $default = null) {
        $this->question = $question;
        $this->default = $default;
    }

    /**
     * Returns the question.
     *
     * @return string
     */
    public function getQuestion() {
        return $this->question;
    }

    /**
     * Returns the default answer.
     *
     * @return mixed
     */
    public function getDefault() {
        return $this->default;
    }

    /**
     * Returns whether the user response accepts newline characters.
     */
    public function isMultiline() {
        return $this->multiline;
    }

    /**
     * Sets whether the user response should accept newline characters.
     *
     * @param bool $multiline
     *
     * @return $this
     */
    public function setMultiline($multiline) {
        $this->multiline = $multiline;

        return $this;
    }

    /**
     * Returns whether the user response must be hidden.
     *
     * @return bool
     */
    public function isHidden() {
        return $this->hidden;
    }

    /**
     * Sets whether the user response must be hidden or not.
     *
     * @param bool $hidden
     *
     * @throws LogicException In case the autocompleter is also used
     *
     * @return $this
     */
    public function setHidden($hidden) {
        if ($this->autocompleterCallback) {
            throw new LogicException('A hidden question cannot use the autocompleter.');
        }

        $this->hidden = (bool) $hidden;

        return $this;
    }

    /**
     * In case the response can not be hidden, whether to fallback on non-hidden question or not.
     *
     * @return bool
     */
    public function isHiddenFallback() {
        return $this->hiddenFallback;
    }

    /**
     * Sets whether to fallback on non-hidden question if the response can not be hidden.
     *
     * @param bool $fallback
     *
     * @return $this
     */
    public function setHiddenFallback($fallback) {
        $this->hiddenFallback = (bool) $fallback;

        return $this;
    }

    /**
     * Gets values for the autocompleter.
     *
     * @return null|iterable
     */
    public function getAutocompleterValues() {
        $callback = $this->getAutocompleterCallback();

        return $callback ? $callback('') : null;
    }

    /**
     * Sets values for the autocompleter.
     *
     * @param mixed $values
     *
     * @throws LogicException
     *
     * @return $this
     */
    public function setAutocompleterValues($values) {
        if (\is_array($values)) {
            $values = $this->isAssoc($values) ? array_merge(array_keys($values), array_values($values)) : array_values($values);

            $callback = static function () use ($values) {
                return $values;
            };
        } elseif ($values instanceof \Traversable) {
            $valueCache = null;
            $callback = static function () use ($values, &$valueCache) {
                return $valueCache ?: $valueCache = iterator_to_array($values, false);
            };
        } else {
            $callback = null;
        }

        return $this->setAutocompleterCallback($callback);
    }

    /**
     * Gets the callback function used for the autocompleter.
     */
    public function getAutocompleterCallback() {
        return $this->autocompleterCallback;
    }

    /**
     * Sets the callback function used for the autocompleter.
     *
     * The callback is passed the user input as argument and should return an iterable of corresponding suggestions.
     *
     * @return $this
     */
    public function setAutocompleterCallback(callable $callback = null) {
        if ($this->hidden && null !== $callback) {
            throw new LogicException('A hidden question cannot use the autocompleter.');
        }

        $this->autocompleterCallback = $callback;

        return $this;
    }

    /**
     * Sets a validator for the question.
     *
     * @param null|mixed $validator
     *
     * @return $this
     */
    public function setValidator($validator = null) {
        $this->validator = $validator;

        return $this;
    }

    /**
     * Gets the validator for the question.
     *
     * @return null|callable
     */
    public function getValidator() {
        return $this->validator;
    }

    /**
     * Sets the maximum number of attempts.
     *
     * Null means an unlimited number of attempts.
     *
     * @param mixed $attempts
     *
     * @throws InvalidArgumentException in case the number of attempts is invalid
     *
     * @return $this
     */
    public function setMaxAttempts($attempts) {
        if (null !== $attempts) {
            $attempts = (int) $attempts;
            if ($attempts < 1) {
                throw new InvalidArgumentException('Maximum number of attempts must be a positive value.');
            }
        }

        $this->attempts = $attempts;

        return $this;
    }

    /**
     * Gets the maximum number of attempts.
     *
     * Null means an unlimited number of attempts.
     *
     * @return null|int
     */
    public function getMaxAttempts() {
        return $this->attempts;
    }

    /**
     * Sets a normalizer for the response.
     *
     * The normalizer can be a callable (a string), a closure or a class implementing __invoke.
     *
     * @param mixed $normalizer
     *
     * @return $this
     */
    public function setNormalizer($normalizer) {
        $this->normalizer = $normalizer;

        return $this;
    }

    /**
     * Gets the normalizer for the response.
     *
     * The normalizer can ba a callable (a string), a closure or a class implementing __invoke.
     *
     * @return null|callable
     */
    public function getNormalizer() {
        return $this->normalizer;
    }

    protected function isAssoc(array $array) {
        return (bool) \count(array_filter(array_keys($array), 'is_string'));
    }

    public function isTrimmable() {
        return $this->trimmable;
    }

    /**
     * @param mixed $trimmable
     *
     * @return $this
     */
    public function setTrimmable($trimmable) {
        $this->trimmable = $trimmable;

        return $this;
    }
}
