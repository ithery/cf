<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @license Ittron Global Teknologi
 *
 * @since Nov 29, 2020
 */
trait CApp_Trait_App_Bootstrap {
    protected static $registerComponentBooted = false;
    protected static $registerControlBooted = false;
    protected static $registerBladeBooted = false;

    public static function registerComponent() {
        if (!static::$registerComponentBooted) {
            CComponent_RenameMe_SupportEvents::init();
            CComponent_RenameMe_SupportLocales::init();
            CComponent_RenameMe_SupportChildren::init();
            CComponent_RenameMe_SupportRedirects::init();
            CComponent_RenameMe_SupportValidation::init();
            CComponent_RenameMe_SupportFileUploads::init();
            CComponent_RenameMe_OptimizeRenderedDom::init();
            CComponent_RenameMe_SupportFileDownloads::init();
            CComponent_RenameMe_SupportActionReturns::init();
            CComponent_RenameMe_SupportBrowserHistory::init();
            CComponent_RenameMe_SupportComponentTraits::init();

            CView::blade()->precompiler(function ($string) {
                return (new CComponent_ComponentTagCompiler())->compile($string);
            });

            CView::blade()->directive('CAppComponent', [CComponent_BladeDirective::class, 'component']);
            CView::blade()->directive('this', [CComponent_BladeDirective::class, 'this']);
            CView::blade()->directive('entangle', [CComponent_BladeDirective::class, 'entangle']);

            CView::engineResolver()->register('blade', function () {
                return new CComponent_ComponentCompilerEngine();
            });
            CComponent_LifecycleManager::registerHydrationMiddleware([
                /* This is the core middleware stack of Livewire. It's important */
                /* to understand that the request goes through each class by the */
                /* order it is listed in this array, and is reversed on response */
                /*                                                               */
                /* ↓    Incoming Request                  Outgoing Response    ↑ */
                /* ↓                                                           ↑ */
                /* ↓    Secure Stuff                                           ↑ */
                /* ↓ */ CComponent_HydrationMiddleware_SecureHydrationWithChecksum::class, /* --------------- ↑ */
                /* ↓ */ CComponent_HydrationMiddleware_NormalizeServerMemoSansDataForJavaScript::class, /* -- ↑ */
                /* ↓ */ CComponent_HydrationMiddleware_HashDataPropertiesForDirtyDetection::class, /* ------- ↑ */
                /* ↓                                                           ↑ */
                /* ↓    Hydrate Stuff                                          ↑ */
                /* ↓ */ CComponent_HydrationMiddleware_HydratePublicProperties::class, /* ------------------- ↑ */
                /* ↓ */ CComponent_HydrationMiddleware_CallPropertyHydrationHooks::class, /* ---------------- ↑ */
                /* ↓ */ CComponent_HydrationMiddleware_CallHydrationHooks::class, /* ------------------------ ↑ */
                /* ↓                                                           ↑ */
                /* ↓    Update Stuff                                           ↑ */
                /* ↓ */ CComponent_HydrationMiddleware_PerformDataBindingUpdates::class, /* ----------------- ↑ */
                /* ↓ */ CComponent_HydrationMiddleware_PerformActionCalls::class, /* ------------------------ ↑ */
                /* ↓ */ CComponent_HydrationMiddleware_PerformEventEmissions::class, /* --------------------- ↑ */
                /* ↓                                                           ↑ */
                /* ↓    Output Stuff                                           ↑ */
                /* ↓ */ CComponent_HydrationMiddleware_RenderView::class, /* -------------------------------- ↑ */
                /* ↓ */ CComponent_HydrationMiddleware_NormalizeComponentPropertiesForJavaScript::class, /* - ↑ */
            ]);

            CComponent_LifecycleManager::registerInitialDehydrationMiddleware([
                /* Initial Response */
                /* ↑ */ [CComponent_HydrationMiddleware_SecureHydrationWithChecksum::class, 'dehydrate'],
                /* ↑ */ [CComponent_HydrationMiddleware_NormalizeServerMemoSansDataForJavaScript::class, 'dehydrate'],
                /* ↑ */ [CComponent_HydrationMiddleware_HydratePublicProperties::class, 'dehydrate'],
                /* ↑ */ [CComponent_HydrationMiddleware_CallPropertyHydrationHooks::class, 'dehydrate'],
                /* ↑ */ [CComponent_HydrationMiddleware_CallHydrationHooks::class, 'initialDehydrate'],
                /* ↑ */ [CComponent_HydrationMiddleware_RenderView::class, 'dehydrate'],
                /* ↑ */ [CComponent_HydrationMiddleware_NormalizeComponentPropertiesForJavaScript::class, 'dehydrate'],
            ]);

            CComponent_LifecycleManager::registerInitialHydrationMiddleware([
                [CComponent_HydrationMiddleware_CallHydrationHooks::class, 'initialHydrate'],
            ]);

            if (method_exists(CView_ComponentAttributeBag::class, 'macro')) {
                CView_ComponentAttributeBag::macro('cf', function ($name) {
                    $entries = carr::head($this->whereStartsWith('cf:' . $name));

                    $directive = carr::head(array_keys($entries));
                    $value = carr::head(array_values($entries));

                    return new CComponent_CFDirective($name, $directive, $value);
                });
            }

            static::$registerComponentBooted = true;
        }
    }

    public static function registerBlade() {
        if (!static::$registerBladeBooted) {
            CView::blade()->directive('CApp', [CApp_Blade_Directive::class, 'directive']);
            CView::blade()->directive('CAppStyles', [CApp_Blade_Directive::class, 'styles']);
            CView::blade()->directive('CAppScripts', [CApp_Blade_Directive::class, 'scripts']);
            CView::blade()->directive('CAppPageTitle', [CApp_Blade_Directive::class, 'pageTitle']);
            CView::blade()->directive('CAppTitle', [CApp_Blade_Directive::class, 'title']);
            CView::blade()->directive('CAppNav', [CApp_Blade_Directive::class, 'nav']);
            CView::blade()->directive('CAppContent', [CApp_Blade_Directive::class, 'content']);
            CView::blade()->directive('CAppNav', [CApp_Blade_Directive::class, 'nav']);
            CView::blade()->directive('CAppPushScript', [CApp_Blade_Directive::class, 'pushScript']);
            CView::blade()->directive('CAppEndPushScript', [CApp_Blade_Directive::class, 'endPushScript']);
            CView::blade()->directive('CAppPrependScript', [CApp_Blade_Directive::class, 'prependScript']);
            CView::blade()->directive('CAppEndPrependScript', [CApp_Blade_Directive::class, 'endPrependScript']);
            CView::blade()->directive('CAppElement', [CApp_Blade_Directive::class, 'element']);

            CView::blade()->component('capp.view-component.modal', 'modal');
            static::$registerBladeBooted = true;
        }
    }

    public static function registerControl() {
        if (!static::$registerControlBooted) {
            CFBenchmark::start('CApp.RegisterControl');
            $manager = CManager::instance();
            $manager->registerControls([
                'text' => CElement_FormInput_Text::class,
                'number' => CElement_FormInput_Number::class,
                'email' => CElement_FormInput_Email::class,
                'datepicker' => CElement_FormInput_Date::class,
                'material-datetime' => CElement_FormInput_DateTime_MaterialDateTime::class,
                'daterange-picker' => CElement_FormInput_DateRange::class,
                'daterange-dropdown' => CElement_FormInput_DateRange_Dropdown::class,
                'daterange-button' => CElement_FormInput_DateRange_DropdownButton::class,
                'currency' => CElement_FormInput_Currency::class,
                'auto-numeric' => CElement_FormInput_AutoNumeric::class,
                'time' => CElement_FormInput_Time::class,
                'timepicker' => CElement_FormInput_Time::class,
                'clock' => CElement_FormInput_Clock::class,
                'clockpicker' => CElement_FormInput_Clock::class,
                'image' => CElement_FormInput_Image::class,
                'image-ajax' => CElement_FormInput_ImageAjax::class,
                'multi-image-ajax' => CElement_FormInput_MultipleImageAjax::class,
                'file-ajax' => CElement_FormInput_FileAjax::class,
                'password' => CElement_FormInput_Password::class,
                'select' => CElement_FormInput_Select::class,
                'minicolor' => CElement_FormInput_MiniColor::class,
                'map-picker' => CElement_FormInput_MapPicker::class,
                'hidden' => CElement_FormInput_Hidden::class,
                'select-tag' => CElement_FormInput_SelectTag::class,
                'selectsearch' => CFormInputSelectSearch::class,
                'checkbox' => CElement_FormInput_Checkbox::class,
                'checkbox-list' => CFormInputCheckboxList::class,
                'switcher' => CFormInputCheckboxList::class,
                'summernote' => CElement_FormInput_Textarea_Summernote::class,
            ]);

            $manager->registerControl('quill', 'CElement_FormInput_Textarea_Quill');
            $manager->registerControl('radio', 'CFormInputRadio');
            $manager->registerControl('label', 'CFormInputLabel');
            $manager->registerControl('file', 'CFormInputFile');
            $manager->registerControl('ckeditor', 'CFormInputCKEditor');
            $manager->registerControl('filedrop', 'CFormInputFileDrop');
            $manager->registerControl('slider', 'CFormInputSlider');
            $manager->registerControl('tooltip', 'CFormInputTooltip');
            $manager->registerControl('fileupload', 'CFormInputFileUpload');
            $manager->registerControl('wysiwyg', 'CFormInputWysiwyg');
            CFBenchmark::stop('CApp.RegisterControl');

            static::$registerControlBooted = true;
        }
    }
}
