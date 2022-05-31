<?php

namespace League\Flysystem\AwsS3V3;

use League\Flysystem\Visibility;

class PortableVisibilityConverter implements VisibilityConverter {
    private const PUBLIC_GRANTEE_URI = 'http://acs.amazonaws.com/groups/global/AllUsers';

    private const PUBLIC_GRANTS_PERMISSION = 'READ';

    private const PUBLIC_ACL = 'public-read';

    private const PRIVATE_ACL = 'private';

    /**
     * @var string
     */
    private $defaultForDirectories;

    /**
     * @param [type] $defaultForDirectories
     */
    public function __construct($defaultForDirectories = Visibility::VISIBILITY_PUBLIC) {
        $this->defaultForDirectories = $defaultForDirectories;
    }

    /**
     * @param string $visibility
     *
     * @return string
     */
    public function visibilityToAcl($visibility) {
        if ($visibility === Visibility::VISIBILITY_PUBLIC) {
            return self::PUBLIC_ACL;
        }

        return self::PRIVATE_ACL;
    }

    /**
     * @param array $grants
     *
     * @return string
     */
    public function aclToVisibility(array $grants) {
        foreach ($grants as $grant) {
            $granteeUri = isset($grant['Grantee']['URI']) ? $grant['Grantee']['URI'] : null;
            $permission = isset($grant['Permission']) ? $grant['Permission'] : null;

            if ($granteeUri === self::PUBLIC_GRANTEE_URI && $permission === self::PUBLIC_GRANTS_PERMISSION) {
                return Visibility::VISIBILITY_PUBLIC;
            }
        }

        return Visibility::VISIBILITY_PRIVATE;
    }

    /**
     * @return string
     */
    public function defaultForDirectories() {
        return $this->defaultForDirectories;
    }
}
