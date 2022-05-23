<?php

interface CApi_Contract_Transformer_AdapterInterface {
    /**
     * Transform a response with a transformer.
     *
     * @param mixed                     $response
     * @param object                    $transformer
     * @param \CApi_Transformer_Binding $binding
     * @param \CApi_HTTP_Request        $request
     *
     * @return array
     */
    public function transform($response, $transformer, CApi_Transformer_Binding $binding, CApi_HTTP_Request $request);
}
