<?php

namespace Cresenity\Demo\Api\Widget\Method\Resize;

use Cresenity\Demo\Api\Widget\MethodAbstract;

/**
 * @OA\Get(
 *     path="/api/widget/resize/image",
 *     tags={"Widget"},
 *     summary="Resize an image",
 *     security={{"oauth2": {}}},
 *     @OA\Parameter(
 *         name="url",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="string", format="uri")
 *     ),
 *     @OA\Parameter(
 *         name="width",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Resized image URL",
 *         @OA\JsonContent(
 *             @OA\Property(property="errCode", type="integer"),
 *             @OA\Property(property="errMessage", type="string"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="url", type="string")
 *             )
 *         )
 *     )
 * )
 *
 * See /docs/api/introduction (method class anatomy) and
 * /docs/api/docs-generation (the @OA annotations above).
 */
class Image extends MethodAbstract {
    public function execute() {
        $this->errCode = 0;
        $this->errMessage = '';

        $url = $this->getApiRequest()->url;
        $width = (int) $this->getApiRequest()->width;

        // Real implementation would call an image-processing engine here.
        $this->data = [
            'url' => $url . '?resized_to=' . $width,
        ];
    }
}
