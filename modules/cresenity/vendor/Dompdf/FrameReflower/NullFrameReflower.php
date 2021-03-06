<?php
/**
 * @link    http://dompdf.github.com/
 *
 * @author  Benj Carson <benjcarson@digitaljunkies.ca>
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */

namespace Dompdf\FrameReflower;

use Dompdf\Frame;
use Dompdf\FrameDecorator\Block as BlockFrameDecorator;

/**
 * Dummy reflower.
 */
class NullFrameReflower extends AbstractFrameReflower {
    /**
     * NullFrameReflower constructor.
     *
     * @param Frame $frame
     */
    public function __construct(Frame $frame) {
        parent::__construct($frame);
    }

    /**
     * @param null|BlockFrameDecorator $block
     */
    public function reflow(BlockFrameDecorator $block = null) {
        return;
    }
}
