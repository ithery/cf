<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Formatter;

use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Color;
/**
 * Formatter style class for defining styles.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class OutputFormatterStyle implements OutputFormatterStyleInterface
{

    private Color $color;
    private $foreground;
    private $background;
    private $options = array();
    private ?string $href = null;
    private bool $handlesHrefGracefully;
    /**
     * Initializes output formatter style.
     *
     * @param string|null $foreground The style foreground color name
     * @param string|null $background The style background color name
     * @param array       $options    The style options
     */
    public function __construct($foreground = null, $background = null, array $options = array())
    {
        $this->color = new Color($this->foreground = $foreground ?: '', $this->background = $background ?: '', $this->options = $options);
    }

    /**
     * Sets style foreground color.
     *
     * @param string|null $color The color name
     *
     * @throws InvalidArgumentException When the color name isn't defined
     */
    public function setForeground($color = null)
    {
        $this->color = new Color($this->foreground = $color ?: '', $this->background, $this->options);
    }

    /**
     * Sets style background color.
     *
     * @param string|null $color The color name
     *
     * @throws InvalidArgumentException When the color name isn't defined
     */
    public function setBackground($color = null)
    {
        $this->color = new Color($this->foreground, $this->background = $color ?: '', $this->options);
    }

    /**
     * Sets some specific style option.
     *
     * @param string $option The option name
     *
     * @throws InvalidArgumentException When the option name isn't defined
     */
    public function setOption($option)
    {
        $this->options[] = $option;
        $this->color = new Color($this->foreground, $this->background, $this->options);
    }

    /**
     * Unsets some specific style option.
     *
     * @param string $option The option name
     *
     * @throws InvalidArgumentException When the option name isn't defined
     */
    public function unsetOption($option)
    {
        $pos = array_search($option, $this->options);
        if (false !== $pos) {
            unset($this->options[$pos]);
        }

        $this->color = new Color($this->foreground, $this->background, $this->options);
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions(array $options)
    {
        $this->color = new Color($this->foreground, $this->background, $this->options = $options);
    }

    /**
     * Applies the style to a given text.
     *
     * @param string $text The text to style
     *
     * @return string
     */
    public function apply($text)
    {
        $this->handlesHrefGracefully ??= 'JetBrains-JediTerm' !== getenv('TERMINAL_EMULATOR')
            && (!getenv('KONSOLE_VERSION') || (int) getenv('KONSOLE_VERSION') > 201100)
            && !isset($_SERVER['IDEA_INITIAL_DIRECTORY']);

        if (null !== $this->href && $this->handlesHrefGracefully) {
            $text = "\033]8;;$this->href\033\\$text\033]8;;\033\\";
        }

        return $this->color->apply($text);
    }
}
