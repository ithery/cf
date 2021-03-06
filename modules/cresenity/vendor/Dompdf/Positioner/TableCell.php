<?php
/**
 * @link    http://dompdf.github.com/
 *
 * @author  Benj Carson <benjcarson@digitaljunkies.ca>
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */

namespace Dompdf\Positioner;

use Dompdf\FrameDecorator\Table;
use Dompdf\FrameDecorator\AbstractFrameDecorator;

/**
 * Positions table cells.
 */
class TableCell extends AbstractPositioner {
    /**
     * @param AbstractFrameDecorator $frame
     */
    public function position(AbstractFrameDecorator $frame) {
        $table = Table::find_parent_table($frame);
        $cellmap = $table->get_cellmap();
        $frame->set_position($cellmap->get_frame_position($frame));
    }
}
