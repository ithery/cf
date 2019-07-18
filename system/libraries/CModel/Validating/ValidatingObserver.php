<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 30, 2019, 3:28:16 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CModel_Validating_ValidatingObserver {

    /**
     * Register the validation event for saving the model. Saving validation
     * should only occur if creating and updating validation does not.
     *
     * @param  CModel $model
     * @return boolean
     */
    public function saving(CModel $model) {
        return $this->performValidation($model, 'saving');
    }

    /**
     * Register the validation event for restoring the model.
     *
     * @param  CModel $model
     * @return boolean
     */
    public function restoring(CModel $model) {
        return $this->performValidation($model, 'restoring');
    }

    /**
     * Perform validation with the specified ruleset.
     *
     * @param  CModel $model
     * @param  string $event
     * @return boolean
     */
    protected function performValidation(CModel $model, $event) {
        // If the model has validating enabled, perform it.
        if ($model->getValidating()) {
            // Fire the namespaced validating event and prevent validation
            // if it returns a value.
            if ($this->fireValidatingEvent($model, $event) !== null) {
                return;
            }
            if ($model->isValid() === false) {
                // Fire the validating failed event.
                $this->fireValidatedEvent($model, 'failed');
                if ($model->getThrowValidationExceptions()) {
                    $model->throwValidationException();
                }
                return false;
            }
            // Fire the validating.passed event.
            $this->fireValidatedEvent($model, 'passed');
        } else {
            $this->fireValidatedEvent($model, 'skipped');
        }
    }

    /**
     * Fire the namespaced validating event.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @param  string $event
     * @return mixed
     */
    protected function fireValidatingEvent(CModel $model, $event) {
        $dispatcher = $model->getEventDispatcher();
        if ($dispatcher == null) {
            return true;
        }
        return $dispatcher->until("eloquent.validating: " . get_class($model), [$model, $event]);
    }

    /**
     * Fire the namespaced post-validation event.
     *
     * @param  CModel $model
     * @param  string $status
     * @return void
     */
    protected function fireValidatedEvent(CModel $model, $status) {
        $dispatcher = $model->getEventDispatcher();
        if ($dispatcher == null) {
            return true;
        }

        $dispatcher->dispatch("eloquent.validated: " . get_class($model), [$model, $status]);
    }

}
