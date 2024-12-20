<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class CAuth_Guard_SessionGuard implements CAuth_Contract_StatefulGuardInterface, CAuth_SupportBasicAuthInterface {
    use CAuth_Guard_Concern_GuardHelper, CTrait_Macroable;

    /**
     * The name of the guard. Typically "web".
     *
     * Corresponds to guard name in authentication configuration.
     *
     * @var string
     */
    protected $name;

    /**
     * The user we last attempted to retrieve.
     *
     * @var CAuth_AuthenticatableInterface
     */
    protected $lastAttempted;

    /**
     * Indicates if the user was authenticated via a recaller cookie.
     *
     * @var bool
     */
    protected $viaRemember = false;

    /**
     * The number of minutes that the "remember me" cookie should be valid for.
     *
     * @var int
     */
    protected $rememberDuration = 576000;

    /**
     * The session used by the guard.
     *
     * @var CSession_Store
     */
    protected $session;

    /**
     * The cookie creator service.
     *
     * @var null|\CHTTP_Contract_CookieInterface
     */
    protected $cookie;

    /**
     * The request instance.
     *
     * @var null|\Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * The event dispatcher instance.
     *
     * @var null|CEvent_Dispatcher
     */
    protected $events;

    /**
     * The timebox instance.
     *
     * @var \CBase_Timebox
     */
    protected $timebox;

    /**
     * Indicates if the logout method has been called.
     *
     * @var bool
     */
    protected $loggedOut = false;

    /**
     * Indicates if a token user retrieval has been attempted.
     *
     * @var bool
     */
    protected $recallAttempted = false;

    /**
     * Create a new authentication guard.
     *
     * @param string                                         $name
     * @param CAuth_UserProviderInterface                    $provider
     * @param CSession_Store                                 $session
     * @param null|\Symfony\Component\HttpFoundation\Request $request
     *
     * @return void
     */
    public function __construct(
        $name,
        CAuth_UserProviderInterface $provider,
        CSession_Store $session,
        Request $request = null
    ) {
        $this->name = $name;
        $this->session = $session;
        $this->request = $request;
        $this->provider = $provider;
        $this->timebox = new CBase_Timebox();
    }

    /**
     * Get the currently authenticated user.
     *
     * @return null|CAuth_AuthenticatableInterface|CModel
     */
    public function user() {
        if ($this->loggedOut) {
            return null;
        }

        // If we've already retrieved the user for the current request we can just
        // return it back immediately. We do not want to fetch the user data on
        // every call to this method because that would be tremendously slow.
        if (!is_null($this->user)) {
            return $this->user;
        }

        $id = $this->session->get($this->getName());
        // First we will try to load the user using the identifier in the session if
        // one exists. Otherwise we will check for a "remember me" cookie in this
        // request, and if one exists, attempt to retrieve the user using that.
        if (!is_null($id)
            && $this->user = (is_object($id) ? $this->provider->retrieveByObject($id) : $this->provider->retrieveById($id))
        ) {
            $this->fireAuthenticatedEvent($this->user);
        }

        // If the user is null, but we decrypt a "recaller" cookie we can attempt to
        // pull the user data on that cookie which serves as a remember cookie on
        // the application. Once we have a user we can return it to the caller.
        if (is_null($this->user) && !is_null($recaller = $this->recaller())) {
            $this->user = $this->userFromRecaller($recaller);

            if ($this->user) {
                $this->updateSession($this->user->getAuthIdentifier());

                $this->fireLoginEvent($this->user, true);
            }
        }

        return $this->user;
    }

    /**
     * Pull a user from the repository by its "remember me" cookie token.
     *
     * @param CAuth_Recaller $recaller
     *
     * @return mixed
     */
    protected function userFromRecaller($recaller) {
        if (!$recaller->valid() || $this->recallAttempted) {
            return;
        }

        // If the user is null, but we decrypt a "recaller" cookie we can attempt to
        // pull the user data on that cookie which serves as a remember cookie on
        // the application. Once we have a user we can return it to the caller.
        $this->recallAttempted = true;

        $this->viaRemember = !is_null($user = $this->provider->retrieveByToken(
            $recaller->id(),
            $recaller->token()
        ));

        return $user;
    }

    /**
     * Get the decrypted recaller cookie for the request.
     *
     * @return null|CAuth_Recaller
     */
    protected function recaller() {
        if (is_null($this->request)) {
            return null;
        }

        if ($recaller = $this->request->cookies->get($this->getRecallerName())) {
            return new CAuth_Recaller($recaller);
        }

        return null;
    }

    /**
     * Get the ID for the currently authenticated user.
     *
     * @return null|int|string
     */
    public function id() {
        if ($this->loggedOut) {
            return null;
        }

        return $this->user()
                    ? $this->user()->getAuthIdentifier()
                    : $this->session->get($this->getName());
    }

    /**
     * Log a user into the application without sessions or cookies.
     *
     * @param array $credentials
     *
     * @return bool
     */
    public function once(array $credentials = []) {
        $this->fireAttemptEvent($credentials);

        if ($this->validate($credentials)) {
            $this->setUser($this->lastAttempted);

            return true;
        }

        return false;
    }

    /**
     * Log the given user ID into the application without sessions or cookies.
     *
     * @param mixed $id
     *
     * @return CAuth_AuthenticatableInterface|false
     */
    public function onceUsingId($id) {
        if (!is_null($user = $this->provider->retrieveById($id))) {
            $this->setUser($user);

            return $user;
        }

        return false;
    }

    /**
     * Validate a user's credentials.
     *
     * @param array $credentials
     *
     * @return bool
     */
    public function validate(array $credentials = []) {
        $this->lastAttempted = $user = $this->provider->retrieveByCredentials($credentials);

        return $this->hasValidCredentials($user, $credentials);
    }

    /**
     * Attempt to authenticate using HTTP Basic Auth.
     *
     * @param string $field
     * @param array  $extraConditions
     *
     * @return void|\Symfony\Component\HttpFoundation\Response
     */
    public function basic($field = 'email', $extraConditions = []) {
        if ($this->check()) {
            return;
        }

        // If a username is set on the HTTP basic request, we will return out without
        // interrupting the request lifecycle. Otherwise, we'll need to generate a
        // request indicating that the given credentials were invalid for login.
        if ($this->attemptBasic($this->getRequest(), $field, $extraConditions)) {
            return;
        }

        return $this->failedBasicResponse();
    }

    /**
     * Perform a stateless HTTP Basic login attempt.
     *
     * @param string $field
     * @param array  $extraConditions
     *
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     *
     * @return null|\Symfony\Component\HttpFoundation\Response
     *
     * @phpstan-return void|null
     */
    public function onceBasic($field = 'email', $extraConditions = []) {
        $credentials = $this->basicCredentials($this->getRequest(), $field);

        if (!$this->once(array_merge($credentials, $extraConditions))) {
            return $this->failedBasicResponse();
        }
    }

    /**
     * Attempt to authenticate using basic authentication.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string                                    $field
     * @param array                                     $extraConditions
     *
     * @return bool
     */
    protected function attemptBasic(Request $request, $field, $extraConditions = []) {
        if (!$request->getUser()) {
            return false;
        }

        return $this->attempt(array_merge(
            $this->basicCredentials($request, $field),
            $extraConditions
        ));
    }

    /**
     * Get the credential array for a HTTP Basic request.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string                                    $field
     *
     * @return array
     */
    protected function basicCredentials(Request $request, $field) {
        return [$field => $request->getUser(), 'password' => $request->getPassword()];
    }

    /**
     * Get the response for basic authentication.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     *
     * @return null|void
     */
    protected function failedBasicResponse() {
        throw new UnauthorizedHttpException('Basic', 'Invalid credentials.');
    }

    /**
     * Attempt to authenticate a user using the given credentials.
     *
     * @param array $credentials
     * @param bool  $remember
     *
     * @return bool
     */
    public function attempt(array $credentials = [], $remember = false) {
        $this->fireAttemptEvent($credentials, $remember);

        $this->lastAttempted = $user = $this->provider->retrieveByCredentials($credentials);

        // If an implementation of UserInterface was returned, we'll ask the provider
        // to validate the user against the given credentials, and if they are in
        // fact valid we'll log the users into the application and return true.
        if ($this->hasValidCredentials($user, $credentials)) {
            $this->login($user, $remember);

            return true;
        }

        // If the authentication attempt fails we will fire an event so that the user
        // may be notified of any suspicious attempts to access their account from
        // an unrecognized user. A developer may listen to this event as needed.
        $this->fireFailedEvent($user, $credentials);

        return false;
    }

    /**
     * Attempt to authenticate a user with credentials and additional callbacks.
     *
     * @param array               $credentials
     * @param null|array|callable $callbacks
     * @param bool                $remember
     *
     * @return bool
     */
    public function attemptWhen(array $credentials = [], $callbacks = null, $remember = false) {
        $this->fireAttemptEvent($credentials, $remember);

        $this->lastAttempted = $user = $this->provider->retrieveByCredentials($credentials);

        // This method does the exact same thing as attempt, but also executes callbacks after
        // the user is retrieved and validated. If one of the callbacks returns falsy we do
        // not login the user. Instead, we will fail the specific authentication attempt.
        if ($this->hasValidCredentials($user, $credentials) && $this->shouldLogin($callbacks, $user)) {
            $this->login($user, $remember);

            return true;
        }

        $this->fireFailedEvent($user, $credentials);

        return false;
    }

    /**
     * Determine if the user matches the credentials.
     *
     * @param mixed $user
     * @param array $credentials
     *
     * @return bool
     */
    protected function hasValidCredentials($user, $credentials) {
        return $this->timebox->call(function ($timebox) use ($user, $credentials) {
            $validated = !is_null($user) && $this->provider->validateCredentials($user, $credentials);

            if ($validated) {
                $timebox->returnEarly();
                $this->fireValidatedEvent($user);
            }

            return $validated;
        }, 200 * 1000);
    }

    /**
     * Log the given user ID into the application.
     *
     * @param mixed $id
     * @param bool  $remember
     *
     * @return \CAuth_AuthenticatableInterface|false
     */
    public function loginUsingId($id, $remember = false) {
        if (!is_null($user = $this->provider->retrieveById($id))) {
            $this->login($user, $remember);

            return $user;
        }

        return false;
    }

    /**
     * Log a user into the application.
     *
     * @param CAuth_AuthenticatableInterface $user
     * @param bool                           $remember
     *
     * @return void
     */
    public function login(CAuth_AuthenticatableInterface $user, $remember = false) {
        $authManager = CAuth::impersonateManager();
        if ($authManager->isImpersonating()) {
            $authManager->stop();
        }

        $this->updateSession($user->getAuthIdentifier());

        // If the user should be permanently "remembered" by the application we will
        // queue a permanent cookie that contains the encrypted copy of the user
        // identifier. We will then decrypt this later to retrieve the users.
        if ($remember) {
            $this->ensureRememberTokenIsSet($user);

            //$this->queueRecallerCookie($user);
            setcookie($this->getRecallerName(), $user->getAuthIdentifier() . '|' . $user->getRememberToken() . '|' . $user->getAuthPassword(), time() + 60 * 60 * 24 * 365 * 100, '/');
        }

        // If we have an event dispatcher instance set we will fire an event so that
        // any listeners will hook into the authentication events and run actions
        // based on the login and logout events fired from the guard instances.
        $this->fireLoginEvent($user, $remember);

        $this->setUser($user);
    }

    /**
     * Update the session with the given ID.
     *
     * @param int $id
     *
     * @return void
     */
    protected function updateSession($id) {
        $this->session->put($this->getName(), $id);
        //get the provider for this guart
        $providerSessionName = CF::config('auth.guards.' . $this->name . '.providerSessionName', 'user_provider');
        $provider = CF::config('auth.guards.' . $this->name . '.provider');

        if ($providerSessionName && $provider) {
            $this->session->put($providerSessionName, $provider);
        }

        //$this->session->put($this->provider->getName(), $id);
    }

    /**
     * Create a new "remember me" token for the user if one doesn't already exist.
     *
     * @param CAuth_AuthenticatableInterface $user
     *
     * @return void
     */
    protected function ensureRememberTokenIsSet(CAuth_AuthenticatableInterface $user) {
        if (empty($user->getRememberToken())) {
            $this->cycleRememberToken($user);
        }
    }

    /**
     * Queue the recaller cookie into the cookie jar.
     *
     * @param CAuth_AuthenticatableInterface $user
     *
     * @return void
     */
    protected function queueRecallerCookie(CAuth_AuthenticatableInterface $user) {
        $this->getCookieJar()->queue($this->createRecaller(
            $user->getAuthIdentifier() . '|' . $user->getRememberToken() . '|' . $user->getAuthPassword()
        ));
    }

    /**
     * Create a "remember me" cookie for a given ID.
     *
     * @param string $value
     *
     * @return \Symfony\Component\HttpFoundation\Cookie
     */
    protected function createRecaller($value) {
        return $this->getCookieJar()->make($this->getRecallerName(), $value, $this->getRememberDuration());
    }

    /**
     * Log the user out of the application.
     *
     * @return void
     */
    public function logout() {
        $user = $this->user();
        $this->clearUserDataFromStorage();

        if (!is_null($this->user) && !empty($user->getRememberToken())) {
            $this->cycleRememberToken($user);
        }

        // If we have an event dispatcher instance, we can fire off the logout event
        // so any further processing can be done. This allows the developer to be
        // listening for anytime a user signs out of this application manually.
        if (isset($this->events)) {
            $this->events->dispatch(new CAuth_Event_Logout($this->name, $user));
        }

        // Once we have fired the logout event we will clear the users out of memory
        // so they are no longer available as the user is no longer considered as
        // being signed into this application and should not be available here.
        $this->user = null;

        $this->loggedOut = true;
    }

    /**
     * Log the user out of the application on their current device only.
     *
     * This method does not cycle the "remember" token.
     *
     * @return void
     */
    public function logoutCurrentDevice() {
        $user = $this->user();

        $this->clearUserDataFromStorage();

        // If we have an event dispatcher instance, we can fire off the logout event
        // so any further processing can be done. This allows the developer to be
        // listening for anytime a user signs out of this application manually.
        if (isset($this->events)) {
            $this->events->dispatch(new CAuth_Event_CurrentDeviceLogout($this->name, $user));
        }

        // Once we have fired the logout event we will clear the users out of memory
        // so they are no longer available as the user is no longer considered as
        // being signed into this application and should not be available here.
        $this->user = null;

        $this->loggedOut = true;
    }

    /**
     * Remove the user data from the session and cookies.
     *
     * @return void
     */
    protected function clearUserDataFromStorage() {
        $this->session->remove($this->getName());
        if (!is_null($this->recaller())) {
            $this->getCookieJar()->queue(
                $this->getCookieJar()->forget($this->getRecallerName())
            );
        }
    }

    /**
     * Refresh the "remember me" token for the user.
     *
     * @param CAuth_AuthenticatableInterface $user
     *
     * @return void
     */
    protected function cycleRememberToken(CAuth_AuthenticatableInterface $user) {
        $user->setRememberToken($token = cstr::random(60));

        $this->provider->updateRememberToken($user, $token);
    }

    /**
     * Invalidate other sessions for the current user.
     *
     * The application must be using the AuthenticateSession middleware.
     *
     * @param string $password
     * @param string $attribute
     *
     * @return null|bool
     */
    public function logoutOtherDevices($password, $attribute = 'password') {
        if (!$this->user()) {
            return null;
        }

        $result = $this->rehashUserPassword($password, $attribute);

        if ($this->recaller()
            || $this->getCookieJar()->hasQueued($this->getRecallerName())
        ) {
            $this->queueRecallerCookie($this->user());
        }

        $this->fireOtherDeviceLogoutEvent($this->user());

        return $result;
    }

    /**
     * Rehash the current user's password.
     *
     * @param string $password
     * @param string $attribute
     *
     * @throws \InvalidArgumentException
     *
     * @return null|\CAuth_AuthenticatableInterface
     */
    protected function rehashUserPassword($password, $attribute) {
        if (!$this->hasher()->check($password, $this->user()->{$attribute})) {
            throw new InvalidArgumentException('The given password does not match the current password.');
        }

        return c::tap($this->user()->forceFill([
            $attribute => $this->hasher()->make($password),
        ]))->save();
    }

    /**
     * Register an authentication attempt event listener.
     *
     * @param mixed $callback
     *
     * @return void
     */
    public function attempting($callback) {
        if (isset($this->events)) {
            $this->events->listen(CAuth_Event_Attempting::class, $callback);
        }
    }

    /**
     * Fire the attempt event with the arguments.
     *
     * @param array $credentials
     * @param bool  $remember
     *
     * @return void
     */
    protected function fireAttemptEvent(array $credentials, $remember = false) {
        if (isset($this->events)) {
            $this->events->dispatch(new CAuth_Event_Attempting(
                $this->name,
                $credentials,
                $remember
            ));
        }
    }

    /**
     * Fires the validated event if the dispatcher is set.
     *
     * @param CAuth_AuthenticatableInterface $user
     *
     * @return void
     */
    protected function fireValidatedEvent($user) {
        if (isset($this->events)) {
            $this->events->dispatch(new CAuth_Event_Validated(
                $this->name,
                $user
            ));
        }
    }

    /**
     * Fire the login event if the dispatcher is set.
     *
     * @param CAuth_AuthenticatableInterface $user
     * @param bool                           $remember
     *
     * @return void
     */
    protected function fireLoginEvent($user, $remember = false) {
        if (isset($this->events)) {
            $this->events->dispatch(new CAuth_Event_Login(
                $this->name,
                $user,
                $remember
            ));
        }
    }

    /**
     * Fire the authenticated event if the dispatcher is set.
     *
     * @param CAuth_AuthenticatableInterface $user
     *
     * @return void
     */
    protected function fireAuthenticatedEvent($user) {
        if (isset($this->events)) {
            $this->events->dispatch(new CAuth_Event_Authenticated(
                $this->name,
                $user
            ));
        }
    }

    /**
     * Fire the other device logout event if the dispatcher is set.
     *
     * @param CAuth_AuthenticatableInterface $user
     *
     * @return void
     */
    protected function fireOtherDeviceLogoutEvent($user) {
        if (isset($this->events)) {
            $this->events->dispatch(new CAuth_Event_OtherDeviceLogout(
                $this->name,
                $user
            ));
        }
    }

    /**
     * Fire the failed authentication attempt event with the given arguments.
     *
     * @param null|CAuth_AuthenticatableInterface $user
     * @param array                               $credentials
     *
     * @return void
     */
    protected function fireFailedEvent($user, array $credentials) {
        if (isset($this->events)) {
            $this->events->dispatch(new CAuth_Event_Failed(
                $this->name,
                $user,
                $credentials
            ));
        }
    }

    /**
     * Get the last user we attempted to authenticate.
     *
     * @return CAuth_AuthenticatableInterface
     */
    public function getLastAttempted() {
        return $this->lastAttempted;
    }

    /**
     * Get a unique identifier for the auth session value.
     *
     * @return string
     */
    public function getName() {
        $sessionName = CF::config('auth.guards.' . $this->name . '.sessionName', 'login_' . $this->name . '_' . sha1(static::class));
        // cdbg::varDump($sessionName);
        // die;
        return $sessionName;
    }

    /**
     * Get the name of the cookie used to store the "recaller".
     *
     * @return string
     */
    public function getRecallerName() {
        return 'remember_' . $this->name . '_' . sha1(static::class);
    }

    /**
     * Determine if the user was authenticated via "remember me" cookie.
     *
     * @return bool
     */
    public function viaRemember() {
        return $this->viaRemember;
    }

    /**
     * Get the number of minutes the remember me cookie should be valid for.
     *
     * @return int
     */
    protected function getRememberDuration() {
        return $this->rememberDuration;
    }

    /**
     * Set the number of minutes the remember me cookie should be valid for.
     *
     * @param int $minutes
     *
     * @return $this
     */
    public function setRememberDuration($minutes) {
        $this->rememberDuration = $minutes;

        return $this;
    }

    /**
     * Get the cookie creator instance used by the guard.
     *
     * @throws \RuntimeException
     *
     * @return \CHTTP_Contract_CookieInterface
     */
    public function getCookieJar() {
        if (!isset($this->cookie)) {
            throw new RuntimeException('Cookie jar has not been set.');
        }

        return $this->cookie;
    }

    /**
     * Set the cookie creator instance used by the guard.
     *
     * @param \CHTTP_Contract_CookieInterface $cookie
     *
     * @return void
     */
    public function setCookieJar(CHTTP_Contract_CookieInterface $cookie) {
        $this->cookie = $cookie;
    }

    /**
     * Get the event dispatcher instance.
     *
     * @return \CEvent_DispatcherInterface
     */
    public function getDispatcher() {
        return $this->events;
    }

    /**
     * Set the event dispatcher instance.
     *
     * @param CEvent_Dispatcher $events
     *
     * @return void
     */
    public function setDispatcher(CEvent_Dispatcher $events) {
        $this->events = $events;
    }

    /**
     * Get the session store used by the guard.
     *
     * @return \CSession_Store
     */
    public function getSession() {
        return $this->session;
    }

    /**
     * Return the currently cached user.
     *
     * @return null|CAuth_AuthenticatableInterface
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Set the current user.
     *
     * @param CAuth_AuthenticatableInterface $user
     *
     * @return $this
     */
    public function setUser(CAuth_AuthenticatableInterface $user) {
        $this->user = $user;

        $this->loggedOut = false;

        $this->fireAuthenticatedEvent($user);

        return $this;
    }

    /**
     * Get the current request instance.
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest() {
        return $this->request ?: Request::createFromGlobals();
    }

    /**
     * Set the current request instance.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return $this
     */
    public function setRequest(Request $request) {
        $this->request = $request;

        return $this;
    }

    public function hasher() {
        return $this->provider->hasher();
    }

    /**
     * Log a user into the application without firing the Login event.
     *
     * @param \CAuth_AuthenticatableInterface $user
     *
     * @return void
     */
    public function quietLogin(CAuth_AuthenticatableInterface $user) {
        $this->updateSession($user->getAuthIdentifier());

        $this->setUser($user);
    }

    /**
     * Logout the user without updating remember_token
     * and without firing the Logout event.
     *
     * @return void
     */
    public function quietLogout() {
        $this->clearUserDataFromStorage();

        $this->user = null;

        $this->loggedOut = true;
    }

    /**
     * Get the timebox instance used by the guard.
     *
     * @return \CBase_Timebox
     */
    public function getTimebox() {
        return $this->timebox;
    }
}
