<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class COAuth_Repository_ClientRepository {

    /**
     * Get a client by the given ID.
     *
     * @param  int  $id
     * @return COAuth_Model_Client|null
     */
    public function find($id) {
        $client = COAuth::client();
        return $client->where($client->getKeyName(), $id)->first();
    }

    /**
     * Get an active client by the given ID.
     *
     * @param  int  $id
     * @return \Laravel\Passport\Client|null
     */
    public function findActive($id) {
        $client = $this->find($id);
        return $client && !$client->revoked ? $client : null;
    }

    /**
     * Get a client instance for the given ID and user ID.
     *
     * @param  int  $clientId
     * @param  mixed  $userId
     * @return COAuth_Model_Client|null
     */
    public function findForUser($clientId, $userId) {
        $client = COAuth::client();
        return $client
                        ->where($client->getKeyName(), $clientId)
                        ->where('user_id', $userId)
                        ->first();
    }

    /**
     * Get the client instances for the given user ID.
     *
     * @param  mixed  $userId
     * @return CModel_Collection
     */
    public function forUser($userId) {
        return COAuth::client()
                        ->where('user_id', $userId)
                        ->orderBy('name', 'asc')->get();
    }

    /**
     * Get the active client instances for the given user ID.
     *
     * @param  mixed  $userId
     * @return CModel_Collection
     */
    public function activeForUser($userId) {
        return $this->forUser($userId)->reject(function ($client) {
                    return $client->revoked;
                })->values();
    }

    /**
     * Get the personal access token client for the application.
     *
     * @return COAuth_Model_Client
     *
     * @throws \RuntimeException
     */
    public function personalAccessClient() {
        if (Passport::$personalAccessClientId) {
            return $this->find(Passport::$personalAccessClientId);
        }
        $client = COAuth::personalAccessClient();
        if (!$client->exists()) {
            throw new RuntimeException('Personal access client not found. Please create one.');
        }
        return $client->orderBy($client->getKeyName(), 'desc')->first()->client;
    }

    /**
     * Store a new client.
     *
     * @param  int  $userId
     * @param  string  $name
     * @param  string  $redirect
     * @param  bool  $personalAccess
     * @param  bool  $password
     * @param  bool  $confidential
     * @return COAuth_Model_Client
     */
    public function create($userId, $name, $redirect, $personalAccess = false, $password = false, $confidential = true) {
        $client = COAuth::client()->forceFill([
            'user_id' => $userId,
            'name' => $name,
            'secret' => ($confidential || $personalAccess) ? Str::random(40) : null,
            'redirect' => $redirect,
            'personal_access_client' => $personalAccess,
            'password_client' => $password,
            'revoked' => false,
        ]);
        $client->save();
        return $client;
    }

    /**
     * Store a new personal access token client.
     *
     * @param  int  $userId
     * @param  string  $name
     * @param  string  $redirect
     * @return COAuth_Model_Client
     */
    public function createPersonalAccessClient($userId, $name, $redirect) {
        return c::tap($this->create($userId, $name, $redirect, true), function ($client) {
            $accessClient = COAuth::personalAccessClient();
            $accessClient->client_id = $client->id;
            $accessClient->save();
        });
    }

    /**
     * Store a new password grant client.
     *
     * @param  int  $userId
     * @param  string  $name
     * @param  string  $redirect
     * @return COAuth_Model_Client
     */
    public function createPasswordGrantClient($userId, $name, $redirect) {
        return $this->create($userId, $name, $redirect, false, true);
    }

    /**
     * Update the given client.
     *
     * @param  COAuth_Model_Client  $client
     * @param  string  $name
     * @param  string  $redirect
     * @return COAuth_Model_Client
     */
    public function update(COAuth_Model_Client $client, $name, $redirect) {
        $client->forceFill([
            'name' => $name, 'redirect' => $redirect,
        ])->save();
        return $client;
    }

    /**
     * Regenerate the client secret.
     *
     * @param  COAuth_Model_Client  $client
     * @return COAuth_Model_Client
     */
    public function regenerateSecret(COAuth_Model_Client $client) {
        $client->forceFill([
            'secret' => cstr::random(40),
        ])->save();
        return $client;
    }

    /**
     * Determine if the given client is revoked.
     *
     * @param  int  $id
     * @return bool
     */
    public function revoked($id) {
        $client = $this->find($id);
        return is_null($client) || $client->revoked;
    }

    /**
     * Delete the given client.
     *
     * @param  COAuth_Model_Client  $client
     * @return void
     */
    public function delete(COAuth_Model_Client $client) {
        $client->tokens()->update(['revoked' => true]);
        $client->forceFill(['revoked' => true])->save();
    }

}
