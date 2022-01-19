<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @see CApp_Model_LogSSE
 * @since Mar 10, 2019, 6:11:27 AM
 *
 * @property string  $createdby
 * @property string  $updatedby
 * @property CCarbon $created
 * @property CCarbon $updated
 * @property int     $status
 */
trait CApp_Model_Trait_LogSSE {
    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        $this->primaryKey = 'log_sse_id';
        $this->table = 'log_sse';
        $this->guarded = ['log_sse_id'];
    }

    /**
     * Saves SSE event in database table.
     *
     * @param $message
     * @param $type
     * @param $event
     * @param mixed $ref
     *
     * @return bool
     */
    public function saveEvent(array $message, $type = 'info', $event = 'message', $ref = null) {
        $this->deleteProcessed();

        $message = json_encode($message);
        $data['message'] = $message;
        $data['event'] = $event;
        $data['type'] = $type;

        if ($ref != null) {
            if ($ref instanceof CModel) {
                $data['ref_id'] = $ref->getKey();
                $data['ref_type'] = get_class($ref);
            } else {
                $data['client'] = $ref;
            }
        } else {
            if (CF::config('sse.append_user_id') && c::auth()->check()) {
                $data['ref_id'] = c::auth()->user()->getAuthIdentifier();
                $data['ref_type'] = get_class(c::auth()->user());
            }
        }

        $this->fill($data);

        return $this->save();
    }

    /**
     * Deletes already processed events.
     */
    public function deleteProcessed() {
        if (!CF::config('sse.keep_events_logs', true)) {
            $this->where('delivered', '1')->delete();
        }
    }
}
