<?php

class CApi_HTTP_Response_Format_JsonFormat extends CApi_HTTP_Response_FormatAbstract {
    /*
     * JSON format (as well as JSONP) uses JsonOptionalFormatting trait, which
     * provides extra functionality for the process of encoding data to
     * its JSON representation.
     */
    use CApi_HTTP_Response_Trait_JsonOptionalFormattingTrait;

    /**
     * Format an Eloquent model.
     *
     * @param \CModel $model
     *
     * @return string
     */
    public function formatModel($model) {
        $key = cstr::singular($model->getTable());

        if (!$model::$snakeAttributes) {
            $key = cstr::camel($key);
        }

        return $this->encode([$key => $model->toArray()]);
    }

    /**
     * Format an Eloquent collection.
     *
     * @param \CModel_Collection $collection
     *
     * @return string
     */
    public function formatModelCollection($collection) {
        if ($collection->isEmpty()) {
            return $this->encode([]);
        }

        $model = $collection->first();
        $key = cstr::plural($model->getTable());

        if (!$model::$snakeAttributes) {
            $key = cstr::camel($key);
        }

        return $this->encode([$key => $collection->toArray()]);
    }

    /**
     * Format an array or instance implementing Arrayable.
     *
     * @param array|\CInterface_Arrayable $content
     *
     * @return string
     */
    public function formatArray($content) {
        $content = $this->morphToArray($content);

        array_walk_recursive($content, function (&$value) {
            $value = $this->morphToArray($value);
        });

        return $this->encode($content);
    }

    /**
     * Get the response content type.
     *
     * @return string
     */
    public function getContentType() {
        return 'application/json';
    }

    /**
     * Morph a value to an array.
     *
     * @param array|\CInterface_Arrayable $value
     *
     * @return array
     */
    protected function morphToArray($value) {
        return $value instanceof CInterface_Arrayable ? $value->toArray() : $value;
    }

    /**
     * Encode the content to its JSON representation.
     *
     * @param mixed $content
     *
     * @return string
     */
    protected function encode($content) {
        $jsonEncodeOptions = [];

        // Here is a place, where any available JSON encoding options, that
        // deal with users' requirements to JSON response formatting and
        // structure, can be conveniently applied to tweak the output.

        if ($this->isJsonPrettyPrintEnabled()) {
            $jsonEncodeOptions[] = JSON_PRETTY_PRINT;
            $jsonEncodeOptions[] = JSON_UNESCAPED_UNICODE;
        }

        $encodedString = $this->performJsonEncoding($content, $jsonEncodeOptions);

        if ($this->isCustomIndentStyleRequired()) {
            $encodedString = $this->indentPrettyPrintedJson(
                $encodedString,
                $this->options['indent_style']
            );
        }

        return $encodedString;
    }
}
