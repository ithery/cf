<?php

final class CVendor_Firebase_Auth_DeleteUsersRequest {
    const MAX_BATCH_SIZE = 1000;

    /**
     * @var string
     */
    private $projectId;

    /**
     * @var string[]
     */
    private $uids;

    /**
     * @var bool
     */
    private $enabledUsersShouldBeForceDeleted;

    /**
     * @param string[] $uids
     * @param string   $projectId
     * @param bool     $enabledUsersShouldBeForceDeleted
     */
    private function __construct($projectId, array $uids, $enabledUsersShouldBeForceDeleted) {
        $this->projectId = $projectId;
        $this->uids = $uids;
        $this->enabledUsersShouldBeForceDeleted = $enabledUsersShouldBeForceDeleted;
    }

    /**
     * @param iterable<\Stringable|string> $uids
     * @param string                       $projectId
     * @param bool                         $forceDeleteEnabledUsers
     */
    public static function withUids($projectId, iterable $uids, $forceDeleteEnabledUsers = false): self {
        $validatedUids = [];
        $count = 0;

        foreach ($uids as $uid) {
            $validatedUids[] = (string) (new CVendor_Firebase_Value_Uid((string) $uid));
            ++$count;

            if ($count > self::MAX_BATCH_SIZE) {
                throw new CVendor_Firebase_Exception_InvalidArgumentException('Only ' . self::MAX_BATCH_SIZE . ' users can be deleted at a time');
            }
        }

        return new self($projectId, $validatedUids, $forceDeleteEnabledUsers);
    }

    /**
     * @return string
     */
    public function projectId() {
        return $this->projectId;
    }

    /**
     * @return string[]
     */
    public function uids() {
        return $this->uids;
    }

    /**
     * @return bool
     */
    public function enabledUsersShouldBeForceDeleted() {
        return $this->enabledUsersShouldBeForceDeleted;
    }
}
