<?php

trait CTrait_Controller_Application_Auth_Impersonate {
    public function listImpersonate($guard = null, $redirect = null) {
        $app = c::app();
        if ($guard == null) {
            $guard = c::app()->auth()->guardName();
        }
        $providerConfig = c::app()->auth($guard)->getProviderConfig();

        $model = carr::get($providerConfig, 'model');
        $exceptUserId = $guard == c::app()->auth()->guardName() ? c::app()->user()->getKey() : null;

        $modelObject = new $model();
        $table = $app->addTable();
        $table->addColumn($modelObject->getKeyName())->setLabel('ID');
        $table->addColumn(carr::get($providerConfig, 'username'))->setLabel('Username');
        $table->setDataFromModel($model, function (CModel_Query $q) use ($exceptUserId) {
            if ($exceptUserId) {
                $q->where($q->getModel()->getKeyName(), '<>', $exceptUserId);
            }
        });
        $table->setAjax(true);
        $queryString = '';
        if ($redirect != null) {
            $queryString .= '?r=' . urlencode($redirect);
        }
        $table->addRowAction()->setLabel('Impersonate')
            ->setLink(c::url($this->controllerUrl() . 'startImpersonate/{' . $modelObject->getKeyName() . '}/' . $guard . $queryString))
            ->setConfirm();
        $table->setRowActionStyle('btn-dropdown');

        return $app;
    }

    /**
     * @param int         $id
     * @param null|string $guardName
     *
     * @throws \Exception
     *
     * @return RedirectResponse
     */
    public function startImpersonate($id, $guardName = null) {
        $request = c::request();
        $manager = CAuth::impersonateManager();
        $guardName = $guardName ?: $manager->getDefaultSessionGuard();

        $errCode = 0;
        $errMessage = '';
        // Cannot impersonate to other guard if already login
        if ($errCode == 0) {
            if ($id == $request->user()->getAuthIdentifier() && ($manager->getCurrentAuthGuardName() == $guardName)) {
                $errCode++;
                $errMessage = 'You cannot impersonate yourself';
            }
        }
        // Cannot impersonate yourself
        if ($errCode == 0) {
            if ($id == $request->user()->getAuthIdentifier() && ($manager->getCurrentAuthGuardName() == $guardName)) {
                $errCode++;
                $errMessage = 'You cannot impersonate yourself';
            }
        }
        // Cannot impersonate again if you're already impersonate a user
        if ($errCode == 0) {
            if ($manager->isImpersonating()) {
                $errCode++;
                $errMessage = 'You cannot impersonate when you are impersonating';
            }
        }

        if ($errCode == 0) {
            if (!$request->user()->canImpersonate()) {
                $errCode++;
                $errMessage = 'Sorry you cant impersonate this user';
            }
        }

        if ($errCode == 0) {
            $userToImpersonate = $manager->findUserById($id, $guardName);

            if ($userToImpersonate->canBeImpersonated()) {
                if ($manager->start($request->user(), $userToImpersonate, $guardName)) {
                    $redirectUrl = carr::get(CApp_Base::getRequestGet(), 'r');
                    if ($redirectUrl) {
                        return c::redirect($redirectUrl);
                    }
                }
            } else {
                $errCode++;
                $errMessage = 'Sorry, this user cannot be impersonate by you';
            }
        }
        if ($errCode > 0) {
            c::msg('error', $errMessage);
        }

        return c::redirect()->back();
    }
}
