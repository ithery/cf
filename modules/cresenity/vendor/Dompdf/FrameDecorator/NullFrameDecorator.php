<?php
/**
 * @link    http://dompdf.github.com/
 *
 * @author  Benj Carson <benjcarson@digitaljunkies.ca>
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */

namespace Dompdf\FrameDecorator;

use Dompdf\Frame;
use Dompdf\Dompdf;

/**
 * Dummy decorator.
 */
class NullFrameDecorator extends AbstractFrameDecorator {
    /**
     * NullFrameDecorator constructor.
     *
     * @param Frame  $frame
     * @param Dompdf $dompdf
     */
    public function __construct(Frame $frame, Dompdf $dompdf) {
        parent::__construct($frame, $dompdf);
        $style = $this->_frame->get_style();
        $style->width = 0;
        $style->height = 0;
        $style->margin = 0;
        $style->padding = 0;
    }
}
