<?php
trait CTrait_Controller_Application_Manager_Translation {
    protected function getTitle() {
        return 'Translation Manager';
    }

    protected function canEdit($language, $group = '') {
        return $language != CF::config('app.locale');
    }

    protected function filterLanguage($language) {
        return true;
    }

    private function getLanguages() {
        $translation = CTranslation::manager()->resolve();
        $languages = $translation->allLanguages();
        $languages = c::collect($languages)->filter(function ($language) {
            return $this->filterLanguage($language);
        })->all();

        return $languages;
    }

    public function index() {
        $app = c::app();

        $app->title($this->getTitle());
        $languages = $this->getLanguages();
        $tabList = $app->addTabList()->setTabPosition('left');

        foreach ($languages as $language) {
            $tabList->addTab()->setLabel($language)
                ->setAjaxUrl($this->controllerUrl() . 'language/' . $language)
                ->setNoPadding();
        }
        $app->addView('cresenity.manager.translation.index');

        return $app;
    }

    public function language($language) {
        $app = c::app();
        $translation = CTranslation::manager()->resolve();
        $groups = $translation->getGroupsFor(CF::config('app.locale'));

        $widget = $app->addWidget()->setTitle($language)->setIcon('ti ti-flag')->setNoPadding();
        $tabList = $widget->addTabList()->setTabPosition('top');

        foreach ($groups as $group) {
            $tabList->addTab()
                ->setLabel($group)->setAjaxUrl($this->controllerUrl() . 'group/' . $language . '/' . $group)
                ->setNoPadding();
        }

        return $app;
    }

    public function group($language, $group) {
        $app = c::app();
        $translation = CTranslation::manager()->resolve();
        $request = c::request();
        $languages = $this->getLanguages();

        $groups = $translation->getGroupsFor(CF::config('app.locale'));
        $translations = $translation->filterTranslationsFor($language, $request->get('filter'));
        if ($group === 'single') {
            $translations = $translations->get('single');
            $translations = new CCollection(['single' => $translations]);
        } else {
            $translations = $translations->get('group')->filter(function ($values, $groupValue) use ($group) {
                return $groupValue === $group;
            });

            $translations = new CCollection(['group' => $translations]);
        }
        $app->addView('cresenity.manager.translation.translation-table', [
            'language' => $language,
            'canEdit' => $this->canEdit($language, $group),
            'languages' => $languages,
            'groups' => $groups,
            'translations' => $translations,
            'storeTranslationUrl' => $this->controllerUrl() . 'update',
        ]);

        return $app;
    }

    public function update() {
        $errCode = 0;
        $errMessage = '';
        $data = [];
        $request = c::request();
        $language = $request->get('language');
        $translationKey = $request->get('translationKey');
        $group = $request->get('group');
        $value = $request->get('value') ?: '';
        if ($errCode == 0) {
            if (!CApp::isAjax()) {
                $errCode++;
                $errMessage = 'This method must be called through ajax';
            }
        }
        if ($errCode == 0) {
            try {
                $translationDriver = CTranslation::manager()->resolve();
                if (!cstr::contains($group, 'single')) {
                    $translationDriver->addGroupTranslation($language, $group, $translationKey, $value);
                } else {
                    $translationDriver->addSingleTranslation($language, $group, $translationKey, $value);
                }
            } catch (Exception $ex) {
                $errCode++;
                $errMessage = $ex->getMessage();
            }
        }

        return CApp_Base::toJsonResponse($errCode, $errMessage, $data);
    }
}
