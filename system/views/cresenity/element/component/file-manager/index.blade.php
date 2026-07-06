<?php
$labels = $fm->getTranslation();
$actionConfig = $fm->config('action');

$actions = [];
if (carr::get($actionConfig, 'use')) {
    $actions[] = ['name' => 'use', 'icon' => 'check', 'label' => 'Confirm', 'multiple' => true];
}
if (carr::get($actionConfig, 'rename')) {
    $actions[] = ['name' => 'rename', 'icon' => 'edit', 'label' => carr::get($labels, 'menu-rename'), 'multiple' => false];
}
if (carr::get($actionConfig, 'download')) {
    $actions[] = ['name' => 'download', 'icon' => 'download', 'label' => carr::get($labels, 'menu-download'), 'multiple' => true];
}
if (carr::get($actionConfig, 'preview')) {
    $actions[] = ['name' => 'preview', 'icon' => 'image', 'label' => carr::get($labels, 'menu-view'), 'multiple' => true];
}
if (carr::get($actionConfig, 'move')) {
    $actions[] = ['name' => 'move', 'icon' => 'paste', 'label' => carr::get($labels, 'menu-move'), 'multiple' => true];
}
if (carr::get($actionConfig, 'resize')) {
    $actions[] = ['name' => 'resize', 'icon' => 'arrows-alt', 'label' => carr::get($labels, 'menu-resize'), 'multiple' => false];
}
if (carr::get($actionConfig, 'crop')) {
    $actions[] = ['name' => 'crop', 'icon' => 'crop', 'label' => carr::get($labels, 'menu-crop'), 'multiple' => false];
}
if (carr::get($actionConfig, 'delete')) {
    $actions[] = ['name' => 'trash', 'icon' => 'trash', 'label' => carr::get($labels, 'menu-delete'), 'multiple' => true];
}

// 'by' values are '{field}_{asc|desc}', matching the list view's sortable
// column headers -- see Path::sortByColumn() and buildSortableHeader() in
// FileManager.js. Name only offers ascending here (unchanged from before);
// Date Modified/File Size offer both directions, as requested.
$sortings = [
    ['by' => 'name_asc', 'icon' => 'sort-alpha-down', 'label' => carr::get($labels, 'nav-sort-alphabetic')],
    ['by' => 'time_asc', 'icon' => 'sort-numeric-down', 'label' => carr::get($labels, 'nav-sort-time-asc')],
    ['by' => 'time_desc', 'icon' => 'sort-numeric-down-alt', 'label' => carr::get($labels, 'nav-sort-time-desc')],
    ['by' => 'size_asc', 'icon' => 'sort-amount-down', 'label' => carr::get($labels, 'nav-sort-size-asc')],
    ['by' => 'size_desc', 'icon' => 'sort-amount-down-alt', 'label' => carr::get($labels, 'nav-sort-size-desc')],
];

$cresConfig = [
    'config' => ['action' => $actionConfig],
    'lang' => $labels,
    'actions' => $actions,
    'sortings' => $sortings,
    'connectorUrl' => rtrim($fm->connectorUrl(), '/'),
    'acceptedFiles' => implode(',', $fm->availableMimeTypes()),
    'maxFilesize' => $fm->maxUploadSize(),
    'locale' => $fm->config('locale'),
];

// Bootstrap's collapse toggle matches by id, so this one pair needs a real,
// unique-per-instance id (everything else below is looked up scoped to this
// instance's own root element, via plain fm-* classes -- see FileManager.js).
$navButtonsId = uniqid('fm-nav-buttons-');
// Radio inputs group by `name` regardless of DOM location, so this also needs
// a real per-instance value (otherwise two FileManager instances on the same
// page would have their duplicate-file dialogs cross-interfere).
$duplicateRadioName = uniqid('fm-duplicate-action-');
?>
<div class="capp-fm cres:element:component:FileManager" cres-element="component:FileManager" cres-config="<?php echo c::jsonAttr($cresConfig); ?>">
    <nav class="navbar sticky-top navbar-expand-lg navbar-dark fm-nav">
        <!--
            Normal header content (breadcrumb + back/hamburger); swaps for
            .fm-selection-toolbar below, Google Drive-style, whenever anything
            is selected -- see toggleActions() in FileManager.js.
        -->
        <div class="fm-nav-normal">
            <!--
                Breadcrumb trail for the folder currently open, whose active
                (last) segment doubles as the "New Folder / Upload" trigger
                (Google Drive-style: "root > cat ▾"). The leading icon swaps
                to a spinner while loading() is true. Rebuilt on every
                loadItems() call. Placed first so it hugs the navbar's left
                edge -- .fm-to-previous reserves its layout space (visibility,
                not display) even while hidden, which would otherwise push
                this rightward.
            -->
            <nav aria-label="breadcrumb" class="navbar-brand fm-breadcrumbs">
                <i class="fas fa-folder-open fa-fw fm-breadcrumb-icon"></i>
                <i class="fas fa-spinner fa-spin fa-fw fm-loading d-none"></i>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item invisible">Home</li>
                </ol>
            </nav>

            <a class="navbar-brand invisible-lg d-none d-lg-inline fm-to-previous">
                <i class="fas fa-arrow-left fa-fw"></i>
                <span class="d-none d-lg-inline">{{ $fm->getLabel('nav-back') }}</span>
            </a>
            <a class="navbar-brand d-block d-lg-none fm-show-tree">
                <i class="fas fa-bars fa-fw"></i>
            </a>
        </div>

        <!--
            Google Drive-style selection toolbar: "N selected" + icon-only
            actions (reusing the very same [data-action] elements that used to
            live in the collapsible nav-buttons menu -- see the
            settings.actions.forEach() loop in FileManager.js). Select via a
            plain click (replaces selection) or Ctrl/Cmd+click (toggles),
            no separate multi-select mode to turn on first.
        -->
        <div class="fm-selection-toolbar d-none">
            <a class="fm-selection-clear" title="{{ $fm->getLabel('btn-close') }}"><i class="fas fa-times"></i></a>
            <span class="fm-selection-count"></span>
            <div class="fm-selection-actions"></div>
        </div>

        <a class="navbar-toggler collapsed border-0 px-1 py-2 m-0 ml-auto" data-toggle="collapse" data-target="#<?php echo $navButtonsId; ?>">
            <i class="fas fa-cog fa-fw"></i>
        </a>
        <div class="collapse navbar-collapse flex-grow-0 fm-nav-buttons" id="<?php echo $navButtonsId; ?>">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-display="grid">
                        <i class="fas fa-th-large fa-fw"></i>
                        <span>{{ $fm->getLabel('nav-thumbnails') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-display="list">
                        <i class="fas fa-list-ul fa-fw"></i>
                        <span>{{ $fm->getLabel('nav-list') }}</span>
                    </a>
                </li>
                <!-- Hidden in list mode -- the table's own column headers sort
                     instead, see toggleSortDropdown() in FileManager.js. -->
                <li class="nav-item dropdown fm-sort-dropdown">
                    <a class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-sort fa-fw"></i>
                        <span>{{ $fm->getLabel('nav-sort') }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right border-0"></div>
                </li>
            </ul>
        </div>
    </nav>

    <nav class="bg-light fixed-bottom border-top d-none fm-actions">
        <a data-action="open" data-multiple="false"><i class="fas fa-folder-open"></i>{{ $fm->getLabel('btn-open') }}</a>
        @if($fm->config('action.preview'))
            <a data-action="preview" data-multiple="true"><i class="fas fa-images"></i>{{ $fm->getLabel('menu-view') }}</a>
        @endif
        @if($fm->config('action.use'))
            <a data-action="use" data-multiple="true"><i class="fas fa-check"></i>{{ $fm->getLabel('btn-confirm') }}</a>
        @endif
    </nav>

    <div class="d-flex flex-row capp-fm-body">
        <div class="fm-tree">
            <div class="fm-tree-body">
                <ul class="fm-tree-root"></ul>
            </div>

            <!-- Recursive node template, cloned per-folder by buildTreeNode() in
                 FileManager.js. Children are lazily fetched (see
                 expandTreeNode()) the first time a node with has_children is
                 expanded, so only one level deeper than what's visible is ever
                 requested. Wrapped in its own <ul> to stay valid markup (an
                 <li> can't be a direct child of a <div>). -->
            <ul class="d-none">
                <li class="fm-tree-item-template">
                    <div class="fm-tree-node">
                        <i class="fas fa-chevron-right fm-tree-toggle d-none"></i>
                        <i class="fa fa-folder fa-fw fm-tree-icon"></i>
                        <span class="fm-tree-label"></span>
                    </div>
                    <ul class="fm-tree-children d-none"></ul>
                </li>
            </ul>
        </div>

        <div class="fm-main">
            <div class="fm-alerts"></div>

            <div class="fm-empty d-none">
                <i class="far fa-folder-open"></i>
                {{ $fm->getLabel('message-empty') }}
            </div>

            <div class="fm-content"></div>

            <!-- Grid card template, cloned per-item by buildGridItem() in
                 FileManager.js. A <div>, not an <a> -- a real per-item dropdown
                 toggle (.fm-item-actions-toggle, populated by
                 populateItemActionsMenu()) needs to nest inside it, and <a>
                 can't contain another <a>. -->
            <div class="fm-item fm-item-template d-none">
                <div class="square"></div>

                <div class="info">
                    <div class="item_name text-truncate"></div>
                    <time class="text-muted font-weight-light text-truncate"></time>
                </div>

                <div class="fm-item-actions dropdown">
                    <a class="fm-item-actions-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right"></div>
                </div>
            </div>

            <!-- Google Drive-style sortable table for list mode; the <thead>
                 is (re)built in JS (buildSortableHeader()) since it needs to
                 reflect the current sortType each render. Row template below,
                 cloned by buildListRow(); wrapped in its own <table>/<tbody>
                 to stay valid markup. -->
            <table class="d-none">
                <tbody>
                    <tr class="fm-item fm-list-row-template">
                        <td class="fm-list-cell-name">
                            <div class="square"></div>
                            <span class="item_name text-truncate"></span>
                        </td>
                        <td class="fm-list-cell-modified"></td>
                        <td class="fm-list-cell-size"></td>
                        <td class="fm-list-cell-actions">
                            <div class="fm-item-actions dropdown">
                                <a class="fm-item-actions-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right"></div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!--
        Non-blocking upload panel (Google Drive-style): unlike a modal, it never
        traps focus/interaction, so users can keep browsing folders or queue up
        more uploads while files are in flight. Drag-and-drop and the actual
        upload (plain XHR + FormData, no third-party library) are wired directly
        on .fm-main in FileManager.js so dropping works anywhere over the
        folder/file view, not just over this panel; this only holds the
        per-file progress list plus the working-dir/type/csrf fields upload
        requests need.
    -->
    <div class="fm-upload-panel d-none">
        <div class="fm-upload-panel-header">
            <span>{{ $fm->getLabel('title-upload') }}</span>
            <a data-dismiss-modal="uploadModal" aria-label="Close"><i class="fas fa-times"></i></a>
        </div>
        <div class="fm-upload-list" data-empty-label="{{ $fm->getLabel('message-drop') }}"></div>
        <div class="fm-upload-item fm-upload-item-template d-none">
            <div class="fm-upload-item-name text-truncate"></div>
            <div class="fm-upload-item-progress"><div class="fm-upload-item-progress-bar"></div></div>
            <i class="fas fa-check-circle text-success fm-upload-item-success d-none"></i>
            <i class="fas fa-exclamation-circle text-danger fm-upload-item-error d-none"></i>
        </div>
        <input type="file" multiple class="fm-file-input d-none" accept="{{ implode(',', $fm->availableMimeTypes()) }}">
        <input type="hidden" class="fm-working-dir">
        <input type="hidden" class="fm-type" value="{{ c::request()->input('type') }}">
        <input type="hidden" class="fm-csrf-token" value="{{ c::csrfToken() }}">
    </div>

    <div class="modal fade fm-notify" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss-modal="notify">{{ $fm->getLabel('btn-close') }}</button>
                    <button type="button" class="btn btn-primary" data-dismiss-modal="notify">{{ $fm->getLabel('btn-confirm') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Small, Drive-style prompt (used for both New Folder and Rename); sizing/
         chrome is scoped to .fm-dialog only in index.scss, not Bootstrap globally. -->
    <div class="modal fade fm-dialog" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control" placeholder="{{ $fm->getLabel('default-folder-name') }}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss-modal="dialog">{{ $fm->getLabel('btn-close') }}</button>
                    <button type="button" class="btn btn-primary" data-dismiss-modal="dialog">{{ $fm->getLabel('btn-confirm') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Google Drive-style "Upload options" prompt, shown when an upload
         collides with an existing file in the current folder -- see
         promptDuplicate()/uploadFile() in FileManager.js. -->
    <div class="modal fade fm-duplicate-dialog" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ $fm->getLabel('title-duplicate') }}</h4>
                </div>
                <div class="modal-body">
                    <p class="fm-duplicate-message"></p>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="<?php echo $duplicateRadioName; ?>" id="<?php echo $duplicateRadioName; ?>-replace" value="replace" checked>
                        <label class="form-check-label" for="<?php echo $duplicateRadioName; ?>-replace">{{ $fm->getLabel('duplicate-replace') }}</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="<?php echo $duplicateRadioName; ?>" id="<?php echo $duplicateRadioName; ?>-keep-both" value="keep_both">
                        <label class="form-check-label" for="<?php echo $duplicateRadioName; ?>-keep-both">{{ $fm->getLabel('duplicate-keep-both') }}</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link fm-duplicate-cancel">{{ $fm->getLabel('btn-cancel') }}</button>
                    <button type="button" class="btn btn-primary fm-duplicate-confirm">{{ $fm->getLabel('btn-upload') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Dedicated FileManager toast stack (bottom-left), separate from the
         app-wide cresenity.message() notifier -- used for every action's
         success/error feedback (upload, delete, rename, move, new folder).
         See toast() in FileManager.js. -->
    <div class="fm-toast-container"></div>
    <div class="fm-toast fm-toast-template d-none">
        <i class="fas fa-check-circle fm-toast-icon fm-toast-icon-success"></i>
        <i class="fas fa-exclamation-circle fm-toast-icon fm-toast-icon-error"></i>
        <div class="fm-toast-message"></div>
    </div>

    <div class="d-none carousel slide bg-light fm-carousel-template" data-ride="carousel">
        <ol class="carousel-indicators">
            <li data-target="#previewCarousel" data-slide-to="0" class="active"></li>
        </ol>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <a class="carousel-label"></a>
                <div class="carousel-image"></div>
            </div>
        </div>
        <a class="carousel-control-prev" href="#previewCarousel" role="button" data-slide="prev">
            <div class="carousel-control-background" aria-hidden="true">
                <i class="fas fa-chevron-left"></i>
            </div>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#previewCarousel" role="button" data-slide="next">
            <div class="carousel-control-background" aria-hidden="true">
                <i class="fas fa-chevron-right"></i>
            </div>
            <span class="sr-only">Next</span>
        </a>
    </div>
</div>
