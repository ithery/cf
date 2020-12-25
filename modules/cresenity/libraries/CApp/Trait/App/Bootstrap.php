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
            $manager = CManager::instance();

            $manager->registerControl('text', 'CElement_FormInput_Text');
            $manager->registerControl('number', 'CElement_FormInput_Number');
            $manager->registerControl('email', 'CElement_FormInput_Email');
            $manager->registerControl('datepicker', 'CElement_FormInput_Date');
            $manager->registerControl('date', 'CElement_FormInput_Date');
            $manager->registerControl('material-datetime', 'CElement_FormInput_DateTime_MaterialDateTime');
            $manager->registerControl('daterange-picker', 'CElement_FormInput_DateRange');
            $manager->registerControl('daterange-dropdown', 'CElement_FormInput_DateRange_Dropdown');
            $manager->registerControl('daterange-button', 'CElement_FormInput_DateRange_DropdownButton');
            $manager->registerControl('currency', 'CElement_FormInput_Currency');
            $manager->registerControl('auto-numeric', 'CElement_FormInput_AutoNumeric');
            $manager->registerControl('time', CElement_FormInput_Time::class);
            $manager->registerControl('timepicker', 'CElement_FormInput_Time');
            $manager->registerControl('clock', 'CElement_FormInput_Clock');
            $manager->registerControl('clockpicker', 'CElement_FormInput_Clock');
            $manager->registerControl('image', 'CElement_FormInput_Image');
            $manager->registerControl('image-ajax', 'CElement_FormInput_ImageAjax');
            $manager->registerControl('multi-image-ajax', 'CElement_FormInput_MultipleImageAjax');
            $manager->registerControl('file', 'CFormInputFile');
            $manager->registerControl('file-ajax', 'CElement_FormInput_FileAjax');
            $manager->registerControl('password', 'CElement_FormInput_Password');
            $manager->registerControl('textarea', 'CElement_FormInput_Textarea');
            $manager->registerControl('select', 'CElement_FormInput_Select');
            $manager->registerControl('minicolor', 'CElement_FormInput_MiniColor');
            $manager->registerControl('map-picker', CElement_FormInput_MapPicker::class);

            $manager->registerControl('select-tag', 'CElement_FormInput_SelectTag');

            $manager->registerControl('selectsearch', 'CFormInputSelectSearch');
            $manager->registerControl('label', 'CFormInputLabel');
            $manager->registerControl('checkbox', CElement_FormInput_Checkbox::class);
            $manager->registerControl('checkbox-list', 'CFormInputCheckboxList');
            $manager->registerControl('switcher', 'CElement_FormInput_Checkbox_Switcher');
            $manager->registerControl('summernote', 'CElement_FormInput_Textarea_Summernote');
            $manager->registerControl('quill', 'CElement_FormInput_Textarea_Quill');
            $manager->registerControl('wysiwyg', 'CFormInputWysiwyg');
            $manager->registerControl('ckeditor', 'CFormInputCKEditor');
            $manager->registerControl('hidden', 'CFormInputHidden');
            $manager->registerControl('radio', 'CFormInputRadio');
            $manager->registerControl('filedrop', 'CFormInputFileDrop');
            $manager->registerControl('slider', 'CFormInputSlider');
            $manager->registerControl('tooltip', 'CFormInputTooltip');
            $manager->registerControl('fileupload', 'CFormInputFileUpload');
            static::$registerControlBooted = true;
        }
    }
}
