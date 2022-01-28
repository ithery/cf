# Changelog

All notable changes to `CF` will be documented in this file

## 1.3 - 2022-01-24

### Important
- php 5.6 not supported anymore
### Added

- add CTranslation_Manager
### Fixes

- Fixes for cres.js:
  - object cresenity.sse

### Improved

- helpers c
  - c::arrayDiffAssocRecursive()

- Remove deprecated $.cresenity

## 1.2 - 2020-10-05

### Added

- add CMiddleware
- add CAuth
- CModel_Relation_BelongsToOne
- CView with blade
- CTemplate with blade
- Added CElement_View, can called with c::app()->addView()
- CComponent
- added CElement_Component, can called c::app()->addComponent()
- Component registration, c::manager()->registerComponent()
- Form client validation with $form->setValidation
- add support for public folder. Document Root for CF now can be served from folder public
- add Experimental WebSocket
- add Experimental CCron
### Fixes

- Fixes for cres.js function ajaxSubmit and handleResponse

### Improved

- helpers c
  - c::app()
  - c::manager()

- nav in folder config now deprecated, please move to folder nav and CApp will
  look file for nav.php as default

- key in nav now support key uri, key in nav will not using controller
  and method anymore, please use key uri.
  'controller'=>'home',
  'method'=>'index',
  now changed to
  'uri'=>'home/index'

- support blade view, give the views extension with blade.php and it will work

- support new feature with CComponent, you can create from views with
  blade syntax, or CApp with method addComponent. documentation is like
  laravel livewire

- CF::collect moved to c::collect and other helper in CF too will
  moved in c helpers

- c helpers is now more powerful
  - c::view(), will return CView_Factory and c::view('view.name') will return
    object of view
  - c::request() will return like CHTTP::request and c::request('key') will
    return CHTTP::request->get('key')

- CF exception handler now in CException::exceptionHandler() singleton
  use this to customizing reportable and renderable exception
  see modules/cresenity/bootstrap.php for example usage

- CF now have console app
  detail explanation in "php cf help"

- CF now have devsuite feature through cf cli
  install using php cf devsuite:install

- CF now support debugbar again
  using it with cookie capp-debugbar=1 to enable

- CF profiler is deprecated, all profiler will run on capp-debugbar for
  future version

- for model, future doesn't use {prefix}Model::make(),
  use {prefix}Model_Post::whatFunction.
  extends models is allowed for future version,
  but just use to using trait which suitable for application
  like CModel_SoftDelete_SoftDeleteTrait,...

- for CApp which using blade.php template, dont use capp.js in theme.
  object js cresenity always attached

- Improves abilities CApp Api for Git Deployment

## 1.1 - 2020-10-05

### Added

- CQueue
- CConsole (php cf)
- CStorage with s3 support
- CEvent
- CModel_Relation_BelongsToThrough

### Fixes

- capp.js modal bug

### Improved

- CF Core to HTTP Kernel
- CView
- CValidation
- CResource with CStorage
- CModel
- now return $app instead using echo $app->render(),
  echoing in controller is discouraged and make performance slower
