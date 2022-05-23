<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @license Ittron Global Teknologi
 *
 * @since Nov 29, 2020
 */
class CComponent_HydrationMiddleware_SecureHydrationWithChecksum implements CComponent_HydrationMiddlewareInterface {
    public static function hydrate($unHydratedInstance, $request) {
        // Make sure the data coming back to hydrate a component hasn't been tampered with.
        $checksumManager = new CComponent_ChecksumManager;

        $checksum = $request->memo['checksum'];

        unset($request->memo['checksum']);

        c::throwUnless(
            $checksumManager->check($checksum, $request->fingerprint, $request->memo),
            new CComponent_Exception_CorruptComponentPayloadException($unHydratedInstance::getName())
        );
    }

    public static function dehydrate($instance, $response) {
        $response->memo['checksum'] = (new CComponent_ChecksumManager)->generate($response->fingerprint, $response->memo);
    }
}
