<?php

trait CModel_AccessToken_AccessTokenModelTrait {
    /**
     * Get the tokenable model that the access token belongs to.
     *
     * @return \CModel_Relationship_MorphTo
     */
    public function tokenable() {
        return $this->morphTo('tokenable');
    }

    /**
     * Find the token instance matching the given token.
     *
     * @param string $token
     *
     * @return null|static
     */
    public static function findToken($token) {
        if (strpos($token, '|') === false) {
            return static::where('token', c::hash('sha256', $token))->first();
        }

        list($id, $token) = explode('|', $token, 2);

        if ($instance = static::find($id)) {
            return hash_equals($instance->token, c::hash('sha256', $token)) ? $instance : null;
        }
    }

    /**
     * Determine if the token has a given ability.
     *
     * @param string $ability
     *
     * @return bool
     */
    public function can($ability) {
        return in_array('*', $this->abilities)
               || array_key_exists($ability, array_flip($this->abilities));
    }

    /**
     * Determine if the token is missing a given ability.
     *
     * @param string $ability
     *
     * @return bool
     */
    public function cant($ability) {
        return !$this->can($ability);
    }
}
