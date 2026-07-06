import ElementHistoryState from '../../../history/ElementHistoryState';
import { cropMethod } from './method/crop';
import { downloadMethod } from './method/download';
import { moveMethod } from './method/move';
import { openMethod } from './method/open';
import { previewMethod } from './method/preview';
import { renameMethod } from './method/rename';
import { resizeMethod } from './method/resize';
import { trashMethod } from './method/trash';
import { useMethod } from './method/use';

export default class FileManager {
    constructor(className, config = {}) {
        // all html elements
        this.elements =
            className instanceof Element
                ? [className]
                : [].slice.call(document.querySelectorAll(className));
        if (this.elements.length < 1) {
            return;
        }
        this.element = this.elements[0];

        // Guards against being constructed twice on the same root element (e.g. if
        // upstream init logic re-fires), which would otherwise double-bind every
        // event handler below and leave window.cfm pointed at whichever instance
        // was constructed last.
        if (this.element.cfmInstance) {
            return this.element.cfmInstance;
        }
        this.$root = $(this.element);
        this.element.cfmInstance = this;

        window.cfm = this;

        // Bound unconditionally, before anything below that could throw
        // (ajax calls, etc.), so the modal close buttons keep working even if a
        // later step in this constructor fails.
        this.initModalDismissHandlers();

        const cresConfig = JSON.parse(this.element.getAttribute('cres-config'));
        this.settings = $.extend({
            connectorUrl: '/cresenity/connector/fm',
            sortType: 'name_asc',
            locale: undefined,
            lang: {
                'nav-upload': 'Upload'
            }
        }, cresConfig, config);
        this.uploadInitilized = false;
        this.uploadQueue = [];
        this.activeUploads = 0;
        // Names currently queued/in-flight for the running upload batch(es) --
        // see queueUpload()/enqueueUpload().
        this.pendingUploadNames = new Set();
        this.duplicateQueue = [];
        this.duplicatePromptActive = false;

        this.selected = [];
        this.items = [];
        // Indexes (into this.items) of whatever's mid-drag for the Drive-style
        // drag-to-move feature -- see bindItemDragAndDrop().
        this.draggingIndexes = null;

        this.showList = 'grid';
        this.sortType = this.settings.sortType;
        this.setActiveDisplay();
        this.callback = {};

        this.controllerMethod = {};
        this.controllerMethod.move = moveMethod.bind(this);
        this.controllerMethod.open = openMethod.bind(this);
        this.controllerMethod.preview = previewMethod.bind(this);
        this.controllerMethod.rename = renameMethod.bind(this);
        this.controllerMethod.trash = trashMethod.bind(this);
        this.controllerMethod.crop = cropMethod.bind(this);
        this.controllerMethod.resize = resizeMethod.bind(this);
        this.controllerMethod.download = downloadMethod.bind(this);
        this.controllerMethod.use = useMethod.bind(this);

        // Bound once here so passing them as bare callback references
        // (jQuery click handlers, ajax .done()) still resolves `this` to
        // this FileManager instance instead of the DOM element/jqXHR.
        this.refreshFoldersAndItems = this.refreshFoldersAndItems.bind(this);
        this.toggleSelected = this.toggleSelected.bind(this);
        this.createFolder = this.createFolder.bind(this);
        // ======================
        // ==  Navbar actions  ==
        // ======================


        this.find('.fm-selection-clear').click(() => {
            this.clearSelected();
        });
        this.find('.fm-to-previous').click(() => {
            let previous_dir = this.getPreviousDir();
            if (previous_dir == '') {
                return;
            }
            this.goTo(previous_dir);
        });
        // eslint-disable-next-line no-unused-vars
        this.find('.fm-show-tree').click((e) => {
            this.toggleMobileTree();
        });
        // eslint-disable-next-line no-unused-vars
        this.find('.fm-main').click((e) => {
            if (this.find('.fm-tree').hasClass('in')) {
                this.toggleMobileTree(false);
            }
        });
        this.$root.on('click', '[data-display]', (e) => {
            // Grid <-> list is a pure client-side re-render of the already
            // loaded this.items -- no need to hit the server again just to
            // change how the same data is laid out. See renderItems().
            let target = e.currentTarget;
            this.showList = $(target).data('display');
            this.setActiveDisplay();
            this.renderItems();
        });
        this.$root.on('click', '[data-action]', (e) => {
            let target = e.currentTarget;
            this.controllerMethod[$(target).data('action')]($(target).data('multiple') ? this.getSelectedItems() : this.getOneSelectedElement());
        });

        // Clicking anywhere in the content area that isn't an item (row/card)
        // clears the selection -- empty grid gutters, table header, the
        // whitespace below the last row, etc. Clicks on the item itself or its
        // "..." dropdown never reach here: those either handle selection
        // themselves (toggleSelected()) or stopPropagation() (dropdown).
        this.$root.on('click', '.fm-content', (e) => {
            if (!$(e.target).closest('.fm-item').length) {
                this.clearSelected();
            }
        });

        // Per-item "..." dropdown (grid card overlay / list row's last column):
        // acts on exactly that one item, regardless of any prior multi-selection,
        // by reusing the same controllerMethod dispatch as the bottom action bar
        // -- see toggleActions()/[data-action] above for the multi-select version.
        // No stopPropagation() on the toggle itself: Bootstrap's own dropdown
        // click handling is delegated at the document level, so stopping the
        // event here (at this.$root, a closer ancestor) would prevent it from
        // ever reaching document and the dropdown would never open. Letting it
        // bubble also fires the item's own click->toggleSelected, which just
        // selects that single item -- a reasonable side effect, not a conflict.
        this.$root.on('click', '[data-item-action]', (e) => {
            e.stopPropagation();
            let actionName = $(e.currentTarget).data('item-action');
            let index = $(e.currentTarget).closest('[data-id]').data('id');
            let actionDef = this.settings.actions.find((a) => a.name === actionName);
            this.selected = [index];
            this.updateSelectedStyle();
            this.controllerMethod[actionName](actionDef && actionDef.multiple ? this.getSelectedItems() : this.getOneSelectedElement());
        });

        // List view's sortable column headers (Google Drive-style): clicking
        // the currently-active column flips its direction, clicking a
        // different one starts it ascending. Pure client-side re-sort of the
        // already-loaded this.items -- see sortItems() -- no ajax needed,
        // same reasoning as the grid<->list [data-display] toggle above.
        this.$root.on('click', '.fm-list-th[data-sort-field]', (e) => {
            let field = $(e.currentTarget).data('sort-field');
            let currentField = this.sortType.replace(/_(asc|desc)$/, '');
            let currentDirection = this.sortType.endsWith('_desc') ? 'desc' : 'asc';
            this.sortType = field + '_' + (field === currentField && currentDirection === 'asc' ? 'desc' : 'asc');
            this.selected = [];
            this.sortItems();
            this.renderItems();
        });

        this.$root.on('click', '.fm-tree-toggle', (e) => {
            e.stopPropagation();
            this.toggleTreeNode($(e.currentTarget).closest('li'));
        });
        this.$root.on('click', '.fm-tree-node', (e) => {
            this.goTo($(e.currentTarget).attr('data-path'));
            this.toggleMobileTree(false);
        });

        this.$root.on('click', '[data-action-button=add-folder]', () => {
            this.dialog(
                this.settings.lang['message-name'],
                this.settings.lang['default-folder-name'] || 'Untitled folder',
                this.createFolder,
                true
            );
        });
        this.$root.on('click', '[data-action-button=upload]', () => {
            this.find('.fm-file-input')[0].click();
        });
        // Icon-only, Google Drive-style, built into the selection toolbar
        // (shown whenever anything is selected -- see toggleActions()), not
        // the old collapsible nav-buttons menu. .slice() first -- this.settings.actions
        // is reused as-is by populateItemActionsMenu() for the per-item
        // dropdown, which should see the original (PHP-defined) order, not
        // reversed by prepend()ing here.
        this.settings.actions.slice().reverse().forEach((action) => {
            this.find('.fm-selection-actions').prepend(
                $('<a>').addClass('fm-selection-action d-none')
                    .attr('data-action', action.name)
                    .attr('data-multiple', action.multiple)
                    .attr('title', action.label)
                    .append($('<i>').addClass('fas fa-fw fa-' + action.icon))
            );
        });
        this.settings.sortings.forEach((sort) => {
            this.find('.fm-nav-buttons .dropdown-menu').append(
                $('<a>').addClass('dropdown-item').attr('data-sortby', sort.by)
                    .append($('<i>').addClass('fas fa-fw fa-' + sort.icon))
                    .append($('<span>').text(sort.label))
                    .click(() => {
                        this.sortType = sort.by;
                        this.selected = [];
                        this.sortItems();
                        this.renderItems();
                    })
            );
        });
        this.initHistoryState();
        this.loadFolders();
        this.performFmRequest('error')
            .done((response) => {
                JSON.parse(response).data.messages.forEach((message) => {
                    this.find('.fm-alerts').append(
                        $('<div>').addClass('alert alert-warning')
                            .append($('<i>').addClass('fas fa-exclamation-circle'))
                            .append(' ' + message)
                    );
                });
            });
        $(window).on('dragenter', () => {
            this.showUploadPanel();
        });

        this.initializeUpload();
    }

    /**
     * Wires this instance up to the browser's back/forward buttons via
     * ElementHistoryState (see media/js/cres/src/history/ElementHistoryState.js),
     * namespaced per-instance so multiple FileManagers on the same page don't
     * clobber each other's history state. goTo() (see below) is what actually
     * pushes an entry whenever the open folder changes; this only reacts to
     * the user navigating back/forward to one of those entries.
     *
     * Also seeds the initial folder from the `path` query string param, if
     * present, since a hard page load/refresh never carries history.state
     * with it.
     */
    initHistoryState() {
        if (!this.element.id) {
            this.element.id = 'cfm-' + Math.random().toString(36).slice(2, 10);
        }

        this.initialWorkingDir = this.getUrlParam('path') || '';
        if (this.initialWorkingDir) {
            this.find('.fm-working-dir').val(this.initialWorkingDir);
        }

        this.historyState = new ElementHistoryState('filemanager:' + this.element.id);
        this.historyState.onChange((state) => {
            this.goTo(state ? state.path : this.initialWorkingDir, false);
        });
    }

    /**
     * jQuery lookup scoped to this instance's own root element, so multiple
     * FileManager instances on the same page don't interfere with each other.
     */
    find(selector) {
        return this.$root.find(selector);
    }

    haveCallback(name) {
        return typeof this.callback[name] == 'function';
    }

    doCallback(name, ...args) {
        if (this.haveCallback(name)) {
            this.callback[name](...args);
        }
    }

    setCallback(name, cb) {
        this.callback[name] = cb;
    }

    // ==================================
    // ==     Base Function            ==
    // ==================================

    getUrlParam(paramName) {
        let reParam = new RegExp('(?:[\?&]|&)' + paramName + '=([^&]+)', 'i');
        let match = window.location.search.match(reParam);
        return (match && match.length > 1) ? match[1] : null;
    }


    // ==================================
    // ==     WYSIWYG Editors Check    ==
    // ==================================

    usingTinymce3() {
        return !!window.tinyMCEPopup;
    }

    usingTinymce4AndColorbox() {
        return !!this.getUrlParam('field_name');
    }

    usingCkeditor3() {
        return !!this.getUrlParam('CKEditor') || !!this.getUrlParam('CKEditorCleanUpFuncNum');
    }

    usingFckeditor2() {
        return window.opener && typeof data != 'undefined' && window.data.Properties.Width != '';
    }

    usingWysiwygEditor() {
        return this.usingTinymce3() || this.usingTinymce4AndColorbox() || this.usingCkeditor3() || this.usingFckeditor2();
    }
    // ====================
    // ==  Ajax actions  ==
    // ====================

    performFmRequest(url, parameter, type) {
        let data = this.defaultParameters();
        if (parameter != null) {
            $.each(parameter, function (key, value) {
                data[key] = value;
            });
        }

        return $.ajax({
            type: 'GET',
            beforeSend: (request) => {
                let token = this.getUrlParam('token');
                if (token !== null) {
                    request.setRequestHeader('Authorization', 'Bearer ' + token);
                }
            },
            dataType: type || 'text',
            url: this.settings.connectorUrl + '/' + url,
            data: data,
            cache: false
        }).fail((jqXHR, textStatus, errorThrown) => {
            this.displayErrorResponse(jqXHR, textStatus, errorThrown);
        });
    }

    displayErrorResponse(jqXHR) {
        //console.log('Display Error Response');
        //try to get json from this response
        let data = null;
        let message = jqXHR.responseText;
        try {
            data = JSON.parse(message);
        } catch(e) {
            //do nothing
        }
        if(typeof data == 'object' && data.message) {
            message = data.message;
        }


        this.notify('<div style="max-height:50vh;overflow: scroll;">' + message + '</div>');
    }

    notify(body, callback) {
        this.find('.fm-notify').find('.btn-primary').toggle(callback !== undefined);
        this.find('.fm-notify').find('.btn-primary').unbind().click(()=>{
            this.find('.fm-notify').modal('hide');
            callback();
        });

        if (window.cresenity.isJson(body)) {
            let json = JSON.parse(body);
            let message = json.html;
            if(json.exception && json.message) {
                message = json.message;
            }
            this.find('.fm-notify').find('.modal-body').html(message);
            if(json.js) {
                eval(window.cresenity.base64.decode(json.js));
            }
            this.find('.fm-notify').modal('show');
        } else {
            this.find('.fm-notify').modal('show').find('.modal-body').html(body);
        }
    }

    notImp() {
        this.notify('error', 'Not yet implemented!');
    }

    /**
     * Dedicated FileManager toast (bottom-left), separate from the app-wide
     * cresenity.message() notifier -- used for every action's short success/
     * error feedback (upload, delete, rename, move, new folder). Confirmation
     * prompts, pickers and previews keep using notify()'s modal instead.
     *
     * @param {string} type 'success' or 'error'
     * @param {string} message
     */
    toast(type, message) {
        let $item = this.find('.fm-toast-template').clone().removeClass('fm-toast-template d-none');
        $item.addClass('fm-toast-' + type);
        $item.find('.fm-toast-message').text(message);
        this.find('.fm-toast-container').append($item);

        let dismiss = () => {
            clearTimeout(timer);
            $item.removeClass('fm-toast-show');
            setTimeout(() => $item.remove(), 200);
        };
        // rAF so the class is added on the next frame -- otherwise the
        // transition never runs since the element starts and ends the same tick.
        requestAnimationFrame(() => $item.addClass('fm-toast-show'));
        let timer = setTimeout(dismiss, 4000);
        $item.on('click', dismiss);
    }

    defaultParameters() {
        return {
            working_dir: this.find('.fm-working-dir').val(),
            type: this.find('.fm-type').val()
        };
    }

    /**
     * @param {string} title
     * @param {string} value
     * @param {function} callback
     * @param {bool} [selectLastWord] Pre-selects the last word of `value` on show
     *                                (Drive's "Untitled folder" -> only "folder"
     *                                highlighted, so typing replaces just that part).
     */
    dialog(title, value, callback, selectLastWord) {
        let $dialog = this.find('.fm-dialog');
        let $input = $dialog.find('input');
        $input.val(value);
        // .off() first: dialog() can be called many times over the component's
        // lifetime (add-folder, rename, ...) and each call was otherwise stacking
        // another 'shown.bs.modal' handler on top of every previous one.
        $dialog.off('shown.bs.modal').on('shown.bs.modal', () => {
            $input.trigger('focus');
            if (selectLastWord) {
                let lastSpace = value.lastIndexOf(' ');
                $input[0].setSelectionRange(lastSpace + 1, value.length);
            }
        });
        $dialog.find('.btn-primary').unbind().click(() => {
            $dialog.modal('hide');
            callback($input.val());
        });
        $dialog.find('.modal-title').text(title);
        $dialog.modal();
    }

    /**
     * @param {string} response raw JSON response body of an action (delete,
     *                          rename, move, new folder, ...)
     * @param {string} [successMessage] shown as a toast when errCode is 0;
     *                                  errors are always toasted regardless.
     */
    refreshFoldersAndItems(response, successMessage) {
        this.loadFolders();
        let json = JSON.parse(response);
        if (json.errCode != 0) {
            this.toast('error', json.errMessage);
        } else if (successMessage) {
            this.toast('success', successMessage);
        }
    }

    /**
     * Moves the given items into targetFolder via the 'doMove' connector
     * action, then refreshes the tree/listing -- shared by the folder-picker
     * dialog (see showMovePicker()) and drag-and-drop (bindItemDragAndDrop()).
     *
     * @param {string[]} itemNames
     * @param {string} targetFolder
     */
    performMove(itemNames, targetFolder) {
        this.performFmRequest('doMove', {
            items: itemNames,
            goToFolder: targetFolder
        }).done((response) => {
            this.refreshFoldersAndItems(response, this.settings.lang['message-move-success']);
        });
    }

    /**
     * (Re)loads the sidebar tree's top-level folders, then loadItems() (which
     * also re-syncs which nodes are expanded/active -- see ensureTreePathOpen()).
     */
    loadFolders() {
        this.performFmRequest('folder', {}, 'html').done((data) => {
            let folders = JSON.parse(data).data.folders;
            let $treeRoot = this.find('.fm-tree-root');
            $treeRoot.html('');
            folders.forEach((folder) => {
                $treeRoot.append(this.buildTreeNode(folder));
            });
            // Top-level folders start expanded, matching the old always-visible
            // first level.
            $treeRoot.children('li').each((index, li) => {
                this.expandTreeNode($(li));
            });
            this.loadItems();
        });
    }

    /**
     * Clones the hidden .fm-tree-item-template into a real, not-yet-expanded
     * tree node for the given folder ({name, path, has_children}, see
     * FolderController).
     *
     * @param {object} folder
     * @return {jQuery}
     */
    buildTreeNode(folder) {
        let li = this.find('.fm-tree-item-template').clone().removeClass('fm-tree-item-template');
        li.children('.fm-tree-node').attr('data-path', folder.path);
        li.find('.fm-tree-label').text(folder.name);
        // invisible (not d-none) so leaf folders still reserve the caret's
        // horizontal space -- keeps every node's label aligned at each depth.
        li.find('.fm-tree-toggle').removeClass('d-none').toggleClass('invisible', !folder.has_children);
        li.data('hasChildren', !!folder.has_children);
        li.data('loaded', false);
        return li;
    }

    /**
     * Renders the folder-picker dialog (see moveMethod() in method/move.js)
     * into the shared .fm-notify modal: a flat nav-pills list of the allowed
     * root folder(s) and their direct children, matching MoveController's
     * `folders` shape ({name, path, children: [{name, path}]}). Clicking any
     * entry performs the move immediately and closes the dialog -- there's no
     * separate confirm step, so the modal's own Confirm button stays hidden.
     *
     * @param {object[]} folders
     * @param {string[]} itemNames
     */
    showMovePicker(folders, itemNames) {
        let $list = $('<ul>').addClass('nav nav-pills flex-column');
        folders.forEach((folder) => {
            $list.append(this.buildMovePickerItem(folder.name, folder.path, itemNames));
            (folder.children || []).forEach((child) => {
                $list.append(this.buildMovePickerItem(child.name, child.path, itemNames, true));
            });
        });

        let $modal = this.find('.fm-notify');
        $modal.find('.modal-body').html('').append($list);
        $modal.find('.btn-primary').hide();
        $modal.modal('show');
    }

    /**
     * @param {string} label
     * @param {string} path
     * @param {string[]} itemNames
     * @param {boolean} [isChild]
     * @return {jQuery}
     */
    buildMovePickerItem(label, path, itemNames, isChild) {
        let li = $('<li>').addClass('nav-item' + (isChild ? ' sub-item' : ''));
        li.append(
            $('<a>').addClass('nav-link').attr('href', '#')
                .append($('<i>').addClass('fa fa-folder fa-fw'))
                .append(' ' + label)
                .click((e) => {
                    e.preventDefault();
                    this.find('.fm-notify').modal('hide');
                    this.performMove(itemNames, path);
                })
        );
        return li;
    }

    /**
     * Toggles a tree node's children open/closed; called from the caret click
     * handler. Does nothing for leaf folders (no caret is rendered for those).
     *
     * @param {jQuery} li
     */
    toggleTreeNode(li) {
        if (!li.data('hasChildren')) {
            return;
        }
        let $children = li.children('.fm-tree-children');
        if (!$children.hasClass('d-none')) {
            $children.addClass('d-none');
            li.children('.fm-tree-node').find('.fm-tree-toggle').removeClass('fm-tree-toggle-expanded');
            return;
        }
        this.expandTreeNode(li);
    }

    /**
     * Opens a tree node's children container, lazily fetching its direct
     * subfolders the first time (cached afterwards via the 'loaded' data flag).
     * Returns a jQuery promise so callers (ensureTreePathOpen()) can chain off
     * of when the fetch (if any) actually completes.
     *
     * @param {jQuery} li
     * @return {JQuery.Promise}
     */
    expandTreeNode(li) {
        let $children = li.children('.fm-tree-children');
        $children.removeClass('d-none');
        li.children('.fm-tree-node').find('.fm-tree-toggle').addClass('fm-tree-toggle-expanded');
        if (!li.data('hasChildren') || li.data('loaded')) {
            return $.Deferred().resolve().promise();
        }
        let path = li.children('.fm-tree-node').attr('data-path');
        return this.performFmRequest('folder', {path: path}, 'html').done((data) => {
            let folders = JSON.parse(data).data.folders;
            $children.html('');
            folders.forEach((folder) => {
                $children.append(this.buildTreeNode(folder));
            });
            li.data('loaded', true);
        });
    }

    /**
     * Walks the tree from its top level down to the folder currently open in
     * .fm-main, lazily expanding each ancestor along the way so the active
     * folder is always visible/highlighted, however deep it is.
     *
     * @param {string} workingDir
     */
    ensureTreePathOpen(workingDir) {
        this.find('.fm-tree-node').removeClass('active');
        this.find('.fm-tree-icon').removeClass('fa-folder-open').addClass('fa-folder');

        let segments = (workingDir || '').split('/').filter(function (e) {
            return e;
        });

        let walk = (li, index) => {
            if (!li || !li.length) {
                return;
            }
            li.children('.fm-tree-node').addClass('active')
                .find('.fm-tree-icon').removeClass('fa-folder').addClass('fa-folder-open');
            if (index >= segments.length) {
                return;
            }
            this.expandTreeNode(li).then(() => {
                let path = '/' + segments.slice(0, index + 1).join('/');
                let next = li.children('.fm-tree-children').children('li').filter((i, el) => {
                    return $(el).children('.fm-tree-node').attr('data-path') === path;
                }).first();
                walk(next, index + 1);
            });
        };
        walk(this.find('.fm-tree-root').children('li').first(), 0);
    }


    // ======================
    // ==  Folder actions  ==
    // ======================

    /**
     * @param {string} new_dir
     * @param {boolean} [pushHistory] false when called from a popstate
     *                                (ElementHistoryState#onChange above) --
     *                                otherwise every back navigation would
     *                                immediately push a new forward entry.
     */
    goTo(new_dir, pushHistory = true) {
        this.find('.fm-working-dir').val(new_dir);
        this.loadItems();

        if (pushHistory) {
            let url = window.cresenity.url.addQueryString(window.location.href, 'path', new_dir);

            this.historyState.push({path: new_dir}, url);
        }
    }

    getPreviousDir() {
        let working_dir = this.find('.fm-working-dir').val();
        if (working_dir) {
            return working_dir.substring(0, working_dir.lastIndexOf('/'));
        }
        return null;
    }


    // ==========================
    // ==  Multiple Selection  ==
    // ==========================

    /**
     * A plain click selects only that item (replacing any previous
     * selection); Ctrl/Cmd+click toggles it in/out of the current selection
     * instead, Google Drive/Explorer/Finder-style -- there's no separate
     * "multi-select mode" to turn on first.
     */
    toggleSelected(e) {
        let sequence = $(e.currentTarget).data('id');
        if (e.ctrlKey || e.metaKey) {
            let elementIndex = this.selected.indexOf(sequence);
            if (elementIndex === -1) {
                this.selected.push(sequence);
            } else {
                this.selected.splice(elementIndex, 1);
            }
        } else {
            this.selected = [sequence];
        }

        this.updateSelectedStyle();
    }

    clearSelected() {
        this.selected = [];
        this.updateSelectedStyle();
    }

    updateSelectedStyle() {
        this.items.forEach((item, index) => {
            let isSelected = this.selected.indexOf(index) > -1;
            let $el = this.find('[data-id=' + index + ']');
            $el.toggleClass('fm-item-selected', isSelected);
            $el.find('.square').toggleClass('selected', isSelected);
        });
        this.toggleActions();
    }

    getOneSelectedElement(orderOfItem) {
        let index = orderOfItem !== undefined ? orderOfItem : this.selected[0];
        return this.items[index];
    }

    getSelectedItems() {
        return this.selected.reduce((arrObjects, id) => {
            arrObjects.push(this.getOneSelectedElement(id));
            return arrObjects;
        }, []);
    }

    toggleActions() {
        let oneSelected = this.selected.length === 1;
        let manySelected = this.selected.length >= 1;
        let onlyImage = this.getSelectedItems()
            .filter(function (item) {
                return !item.is_image;
            })
            .length === 0;
        let onlyFile = this.getSelectedItems()
            .filter(function (item) {
                return !item.is_file;
            })
            .length === 0;
        this.find('[data-action=use]').toggleClass('d-none', !(manySelected && onlyFile));
        this.find('[data-action=rename]').toggleClass('d-none', !oneSelected);
        this.find('[data-action=preview]').toggleClass('d-none', !(manySelected && onlyFile));
        this.find('[data-action=move]').toggleClass('d-none', !manySelected);
        this.find('[data-action=download]').toggleClass('d-none', !(manySelected && onlyFile));
        this.find('[data-action=resize]').toggleClass('d-none', !(oneSelected && onlyImage));
        this.find('[data-action=crop]').toggleClass('d-none', !(oneSelected && onlyImage));
        this.find('[data-action=trash]').toggleClass('d-none', !manySelected);
        this.find('[data-action=open]').toggleClass('d-none', !oneSelected || onlyFile);
        this.find('.fm-actions').toggleClass('d-none', this.selected.length === 0);

        // Google Drive-style: normal header (breadcrumb/back/hamburger) swaps
        // for a "N selected" toolbar (icon-only actions, reusing the very same
        // [data-action] elements above) whenever anything is selected.
        this.find('.fm-nav-normal').toggleClass('d-none', manySelected);
        this.find('.fm-selection-toolbar').toggleClass('d-none', !manySelected);
        if (manySelected) {
            this.find('.fm-selection-count').text(
                this.selected.length + ' ' + (this.settings.lang['message-selected'] || 'selected')
            );
        }
    }


    /**
     * Fetches the current folder's items from the server (needed whenever the
     * folder or sortType actually changes) and stores them on this.items/
     * this.workingDir, then hands off to renderItems() for the actual DOM
     * build. Switching grid <-> list alone doesn't need this -- see the
     * [data-display] handler in the constructor, which calls renderItems()
     * directly against the already-loaded data.
     */
    loadItems() {
        this.loading(true);
        this.performFmRequest('item', {showList: this.showList, sortType: this.sortType}, 'html')
            .done((data) => {
                this.selected = [];
                let response = JSON.parse(data).data;
                this.workingDir = response.working_dir;
                this.items = response.items;
                this.find('.fm-nav-buttons > ul').removeClass('d-none');
                this.find('.fm-working-dir').val(this.workingDir);
                this.renderBreadcrumbs(this.workingDir);
                let atRootFolder = this.getPreviousDir() == '';
                this.find('.fm-to-previous').toggleClass('d-none invisible-lg', atRootFolder);
                this.find('.fm-show-tree').toggleClass('d-none', !atRootFolder).toggleClass('d-block', atRootFolder);
                this.ensureTreePathOpen(this.workingDir);
                this.renderItems();
                this.loading(false);
            });
    }

    /**
     * Builds the breadcrumb trail (root > cat > ...), whose active (last)
     * segment doubles as the New Folder/Upload trigger, Drive-style.
     *
     * @param {string} workingDir
     */
    renderBreadcrumbs(workingDir) {
        let breadcrumbs = [];
        let validSegments = workingDir.split('/').filter(function (e) {
            return e;
        });
        // The root entry always leads the trail (root > cat > ...) -- its
        // label comes from the tree sidebar's own name for '/' (e.g. a
        // "files"/"photos" category folder shown there as "root").
        breadcrumbs.push({label: this.find('[data-path=\'/\']').text(), path: '/'});
        validSegments.forEach((segment, index) => {
            let path = '/' + validSegments.slice(0, index + 1).join('/');
            breadcrumbs.push({label: segment, path});
        });
        this.find('.fm-breadcrumbs > ol').html('');
        breadcrumbs.forEach((crumb, index) => {
            let li = $('<li>').addClass('breadcrumb-item');
            if (index === breadcrumbs.length - 1) {
                // [data-action-button] clicks are handled by the delegated
                // handlers already bound on this.$root in the constructor.
                li.addClass('active dropdown').attr('aria-current', 'page');
                li.append(
                    $('<a>').addClass('dropdown-toggle').attr({
                        'data-toggle': 'dropdown',
                        role: 'button',
                        'aria-haspopup': 'true',
                        'aria-expanded': 'false'
                    }).text(crumb.label)
                );
                li.append(
                    $('<div>').addClass('dropdown-menu').append(
                        $('<a>').addClass('dropdown-item').attr('data-action-button', 'add-folder')
                            .append($('<i>').addClass('fas fa-folder fa-fw'))
                            .append(' ' + this.settings.lang['nav-new'])
                    ).append(
                        $('<a>').addClass('dropdown-item').attr('data-action-button', 'upload')
                            .append($('<i>').addClass('fas fa-upload fa-fw'))
                            .append(' ' + this.settings.lang['nav-upload'])
                    )
                );
            } else {
                li.text(crumb.label).click(() => {
                    this.goTo(crumb.path);
                });
            }

            this.find('.fm-breadcrumbs > ol').append(li);
        });
    }

    /**
     * Pure client-side (re-)render of this.items into either the grid or the
     * list table, based on this.showList -- no ajax. Called after loadItems()
     * fetches fresh data, and directly from the [data-display] toggle.
     */
    renderItems() {
        let hasItems = this.items.length !== 0;
        this.find('.fm-empty').toggleClass('d-none', hasItems);
        this.find('.fm-content').html('').removeAttr('class').addClass('fm-content');
        this.toggleSortDropdown();
        if (hasItems) {
            this.find('.fm-content').addClass(this.showList).addClass('preserve_actions_space');
            if (this.showList === 'list') {
                this.renderListTable();
            } else {
                this.renderGrid();
            }
        }
        this.toggleActions();
    }

    /**
     * Client-side re-sort of this.items by the current sortType, mirroring
     * Path::sortByColumn()'s server-side logic -- including folders always
     * sorting separately from (and staying ahead of) files, matching how the
     * server merges folders() then files(). Lets the sort dropdown/column
     * headers re-render instantly instead of re-fetching, same reasoning as
     * the grid<->list toggle.
     */
    sortItems() {
        let direction = this.sortType.endsWith('_desc') ? 'desc' : 'asc';
        let field = this.sortType.replace(/_(asc|desc)$/, '');
        if (['name', 'time', 'size'].indexOf(field) === -1) {
            field = 'name';
        }
        let folders = this.items.filter((item) => !item.is_file);
        let files = this.items.filter((item) => item.is_file);
        [folders, files].forEach((group) => {
            group.sort((a, b) => {
                let cmp = this.compareItems(a, b, field);
                return direction === 'desc' ? -cmp : cmp;
            });
        });
        this.items = folders.concat(files);
    }

    /**
     * @param {object} a
     * @param {object} b
     * @param {string} field 'name', 'time' or 'size'
     * @return {number}
     */
    compareItems(a, b, field) {
        let key = field === 'size' ? 'size_bytes' : field;
        let aVal = a[key];
        let bVal = b[key];
        if (typeof aVal === 'number' && typeof bVal === 'number') {
            return aVal - bVal;
        }
        return String(aVal).localeCompare(String(bVal));
    }

    /**
     * The top nav's Sort dropdown only makes sense in grid mode -- list mode
     * sorts via its own clickable column headers instead.
     */
    toggleSortDropdown() {
        this.find('.fm-sort-dropdown').toggleClass('d-none', this.showList === 'list');
    }

    /**
     * @param {number} timestamp unix timestamp (seconds)
     * @return {string}
     */
    formatDate(timestamp) {
        return (new Date(timestamp * 1000)).toLocaleString(this.settings.locale);
    }

    /**
     * Binds the shared per-item behavior (select/open, dropdown menu) common
     * to both the grid card and the list row -- everything else about their
     * markup differs (buildGridItem()/buildListRow() build the rest).
     *
     * @param {jQuery} el
     * @param {object} item
     * @param {number} index
     * @return {jQuery}
     */
    bindItemElement(el, item, index) {
        el.attr('data-id', index)
            .click(this.toggleSelected)
            // eslint-disable-next-line no-unused-vars
            .dblclick((e) => {
                if (item.is_file) {
                    this.controllerMethod.use(this.getSelectedItems());
                } else {
                    this.goTo(item.url);
                }
            });
        this.populateItemActionsMenu(el.find('.fm-item-actions .dropdown-menu'), item);
        this.bindItemDragAndDrop(el, item, index);
        return el;
    }

    /**
     * Google Drive-style drag-to-move: any card/row is draggable, and folders
     * accept drops of whatever is currently dragged, moving it there via
     * performMove() -- the same helper the folder-picker dialog uses (see
     * showMovePicker()) -- just without the picker, since the target folder
     * is exactly the one dropped onto.
     *
     * @param {jQuery} el
     * @param {object} item
     * @param {number} index
     */
    bindItemDragAndDrop(el, item, index) {
        el.attr('draggable', 'true');
        el.on('dragstart', (e) => {
            // Dragging an item outside the current selection starts a fresh
            // single-item drag; dragging one that's already selected drags
            // the whole selection along with it, Drive-style.
            if (this.selected.indexOf(index) === -1) {
                this.selected = [index];
                this.updateSelectedStyle();
            }
            this.draggingIndexes = this.selected.slice();
            e.originalEvent.dataTransfer.effectAllowed = 'move';
            // Firefox refuses to start a drag without data actually being set.
            e.originalEvent.dataTransfer.setData('text/plain', item.name);
        });
        el.on('dragend', () => {
            this.find('.fm-drop-hover').removeClass('fm-drop-hover');
            this.draggingIndexes = null;
        });

        if (item.is_file) {
            return;
        }
        el.on('dragover', (e) => {
            if (!this.draggingIndexes || this.draggingIndexes.indexOf(index) !== -1) {
                return;
            }
            e.preventDefault();
            e.originalEvent.dataTransfer.dropEffect = 'move';
            el.addClass('fm-drop-hover');
        });
        el.on('dragleave', () => {
            el.removeClass('fm-drop-hover');
        });
        el.on('drop', (e) => {
            e.preventDefault();
            el.removeClass('fm-drop-hover');
            if (!this.draggingIndexes || this.draggingIndexes.indexOf(index) !== -1) {
                return;
            }
            let draggedItems = this.draggingIndexes.map((i) => this.items[i]);
            this.draggingIndexes = null;
            this.performMove(draggedItems.map((draggedItem) => draggedItem.name), item.url);
        });
    }

    /**
     * @param {object} item
     * @return {jQuery}
     */
    buildItemImage(item) {
        if (item.thumb_url) {
            return $('<div>').css('background-image', 'url("' + item.thumb_url + '?timestamp=' + item.time + '")');
        }

        return $('<div>').addClass('mime-icon ico-' + item.icon).append($('<div>').addClass('ico'));
    }

    /**
     * Per-item "..." dropdown contents -- the same actions/visibility rules
     * as the bottom multi-select bar (toggleActions()), evaluated for this
     * one item instead of the current selection, and only ever including
     * actions actually enabled via the action config (this.settings.actions).
     *
     * @param {jQuery} $menu
     * @param {object} item
     */
    populateItemActionsMenu($menu, item) {
        $menu.html('');
        let visible = {
            use: item.is_file,
            rename: true,
            preview: item.is_file,
            move: true,
            download: item.is_file,
            resize: item.is_image,
            crop: item.is_image,
            trash: true
        };
        this.settings.actions.forEach((action) => {
            if (!visible[action.name]) {
                return;
            }
            $menu.append(
                $('<a>').addClass('dropdown-item').attr('data-item-action', action.name)
                    .append($('<i>').addClass('fas fa-fw fa-' + action.icon))
                    .append(' ' + action.label)
            );
        });
    }

    renderGrid() {
        this.items.forEach((item, index) => {
            this.find('.fm-content').append(this.buildGridItem(item, index));
        });
    }

    /**
     * @param {object} item
     * @param {number} index
     * @return {jQuery}
     */
    buildGridItem(item, index) {
        let template = this.bindItemElement(
            this.find('.fm-item-template').clone().removeClass('fm-item-template d-none'),
            item,
            index
        );
        template.find('.square').append(this.buildItemImage(item));
        template.find('.item_name').text(item.name);
        template.find('time').text(item.is_file ? this.formatDate(item.time) : '').toggleClass('d-none', !item.is_file);

        return template;
    }

    /**
     * Google Drive-style sortable table: Name / Date Modified / File Size (no
     * Owner column) -- the header is rebuilt every render since it needs to
     * reflect the current sortType (see buildSortableHeader()).
     */
    renderListTable() {
        let $table = $('<table>').addClass('fm-list-table');
        let $headRow = $('<tr>').appendTo($('<thead>').appendTo($table));
        $headRow.append(this.buildSortableHeader('name', this.settings.lang['title-name'] || 'Name'));
        $headRow.append(this.buildSortableHeader('time', this.settings.lang['title-modified'] || 'Date modified'));
        $headRow.append(this.buildSortableHeader('size', this.settings.lang['title-size'] || 'File size'));
        $headRow.append($('<th>').addClass('fm-list-th-actions'));

        let $tbody = $('<tbody>').appendTo($table);
        this.items.forEach((item, index) => {
            $tbody.append(this.buildListRow(item, index));
        });
        this.find('.fm-content').append($table);
    }

    /**
     * @param {string} field 'name', 'time' or 'size'
     * @param {string} label
     * @return {jQuery}
     */
    buildSortableHeader(field, label) {
        let currentField = this.sortType.replace(/_(asc|desc)$/, '');
        let currentDirection = this.sortType.endsWith('_desc') ? 'desc' : 'asc';
        let $th = $('<th>').addClass('fm-list-th').attr('data-sort-field', field)
            .append($('<span>').text(label));
        if (field === currentField) {
            $th.addClass('active').append(
                $('<i>').addClass('fas fa-fw fm-list-sort-icon fa-arrow-' + (currentDirection === 'asc' ? 'up' : 'down'))
            );
        }

        return $th;
    }

    /**
     * @param {object} item
     * @param {number} index
     * @return {jQuery}
     */
    buildListRow(item, index) {
        let row = this.bindItemElement(
            this.find('.fm-list-row-template').clone().removeClass('fm-list-row-template d-none'),
            item,
            index
        );
        row.find('.square').append(this.buildItemImage(item));
        row.find('.item_name').text(item.name);
        row.find('.fm-list-cell-modified').text(item.is_file ? this.formatDate(item.time) : '');
        row.find('.fm-list-cell-size').text(item.size || '');

        return row;
    }

    loading(showLoading) {
        this.find('.fm-loading').toggleClass('d-none', !showLoading);
        this.find('.fm-breadcrumb-icon').toggleClass('d-none', showLoading);
    }

    createFolder(folderName) {
        this.performFmRequest('newFolder', {name: folderName})
            .done((response) => this.refreshFoldersAndItems(response, this.settings.lang['message-create-success']));
    }

    /**
     * Wires drag-and-drop (anywhere over .fm-main, matching Drive) and the
     * hidden file input's change event to queueUpload(); no third-party library,
     * uploads run as plain XHR + FormData (see uploadFile()).
     */
    initializeUpload() {
        if (this.uploadInitilized) {
            return;
        }
        this.uploadInitilized = true;

        this.find('.fm-file-input').on('change', (e) => {
            this.queueUpload(e.target.files);
            e.target.value = '';
        });

        let dragCounter = 0;
        const mainEl = this.find('.fm-main');
        // A plain dragover/dragleave pair fires repeatedly as the pointer moves
        // over child elements inside .fm-main; a counter (rather than a single
        // boolean) avoids the hover overlay flickering off while still dragging
        // over a nested item.
        //
        // isFileDrag() gates all four of these on the drag actually carrying OS
        // files (dataTransfer.types includes 'Files') -- otherwise an internal
        // item-to-folder drag (see bindItemDragAndDrop()) would also bubble up
        // here and both show the "drop files to upload" overlay and, since
        // dataTransfer.files is empty for an internal drag, silently no-op an
        // upload attempt on drop.
        mainEl.on('dragenter', (e) => {
            if (!this.isFileDrag(e)) {
                return;
            }
            e.preventDefault();
            dragCounter++;
            mainEl.addClass('fm-drag-hover');
        });
        mainEl.on('dragover', (e) => {
            if (!this.isFileDrag(e)) {
                return;
            }
            e.preventDefault();
        });
        mainEl.on('dragleave', (e) => {
            e.preventDefault();
            dragCounter = Math.max(0, dragCounter - 1);
            if (dragCounter === 0) {
                mainEl.removeClass('fm-drag-hover');
            }
        });
        mainEl.on('drop', (e) => {
            if (!this.isFileDrag(e)) {
                return;
            }
            e.preventDefault();
            dragCounter = 0;
            mainEl.removeClass('fm-drag-hover');
            this.queueUpload(e.originalEvent.dataTransfer.files);
        });
    }

    /**
     * @param {jQuery.Event} e
     * @return {boolean} whether this drag carries OS files, as opposed to an
     *                    internal item drag (see bindItemDragAndDrop())
     */
    isFileDrag(e) {
        let types = e.originalEvent.dataTransfer && e.originalEvent.dataTransfer.types;
        return !!types && Array.prototype.indexOf.call(types, 'Files') !== -1;
    }

    /**
     * @param {string} mimeType
     * @return {bool}
     */
    isFileTypeAccepted(mimeType) {
        let accepted = (this.settings.acceptedFiles || '').split(',').map((s) => s.trim()).filter(Boolean);
        if (accepted.length === 0) {
            return true;
        }
        return accepted.some((type) => {
            if (type.slice(-2) === '/*') {
                return mimeType.indexOf(type.slice(0, -1)) === 0;
            }
            return mimeType === type;
        });
    }

    /**
     * Validates each file against acceptedFiles/maxFilesize, then hands the rest
     * to the upload queue.
     *
     * Name conflicts are checked up front -- Google Drive-style, instantly, no
     * ajax round-trip just to discover a duplicate -- against two things:
     * - this.items, the already-loaded folder listing (existing server-side
     *   file) -> prompts via promptDuplicate().
     * - this.pendingUploadNames, files already queued/uploading from *this*
     *   same batch -> silently replaces instead, no prompt: picking two
     *   same-named files together in one go is deliberate, not a surprise
     *   collision, so there's nothing worth asking about.
     * uploadFile() still re-checks server-side as a race-guard regardless
     * (e.g. a file uploaded from another tab since this list was last loaded).
     *
     * @param {FileList} fileList
     */
    queueUpload(fileList) {
        this.showUploadPanel();
        Array.prototype.forEach.call(fileList, (file) => {
            let item = this.createUploadPreviewItem(file);
            if (!this.isFileTypeAccepted(file.type)) {
                this.markUploadError(item, (this.settings.lang['error-mime'] || 'Unexpected MimeType: ') + file.type);
                return;
            }
            if (file.size > this.settings.maxFilesize * 1000) {
                this.markUploadError(item, this.settings.lang['error-size'] || 'Over limit size');
                return;
            }
            if (this.pendingUploadNames.has(file.name)) {
                this.enqueueUpload(file, item, 'replace');
                return;
            }
            if (this.findExistingFile(file.name)) {
                this.promptDuplicate(file, item);
                return;
            }
            this.enqueueUpload(file, item);
        });
        this.processUploadQueue();
    }

    /**
     * @param {string} name
     * @return {boolean} whether a file (not folder) by this name is already
     *                    listed in the current folder
     */
    findExistingFile(name) {
        return this.items.some((item) => item.is_file && item.name === name);
    }

    /**
     * @param {File} file
     * @param {jQuery} item
     * @param {string} [onDuplicate]
     */
    enqueueUpload(file, item, onDuplicate) {
        this.pendingUploadNames.add(file.name);
        this.uploadQueue.push({file, item, onDuplicate});
    }

    processUploadQueue() {
        while (this.activeUploads < 5 && this.uploadQueue.length > 0) {
            let queued = this.uploadQueue.shift();
            this.activeUploads++;
            this.uploadFile(queued.file, queued.item, queued.onDuplicate);
        }
    }

    createUploadPreviewItem(file) {
        let item = this.find('.fm-upload-item-template').clone().removeClass('fm-upload-item-template d-none');
        item.find('.fm-upload-item-name').text(file.name);
        this.find('.fm-upload-list').append(item);
        return item;
    }

    markUploadProgress(item, percent) {
        item.find('.fm-upload-item-progress-bar').css('width', percent + '%');
    }

    markUploadSuccess(item) {
        item.find('.fm-upload-item-progress').addClass('d-none');
        item.find('.fm-upload-item-success').removeClass('d-none');
    }

    markUploadError(item, message) {
        item.find('.fm-upload-item-progress').addClass('d-none');
        item.find('.fm-upload-item-error').removeClass('d-none').attr('title', message);
        this.toast('error', message);
    }

    /**
     * Google Drive-style "Upload options" prompt for a name conflict: lets the
     * user choose to replace the existing file or keep both, then retries the
     * same upload with that choice as `on_duplicate` (see uploadFile()).
     * Cancelling counts as a failed upload for this item.
     *
     * Queued rather than shown immediately -- the dialog is a single shared
     * modal, so dropping several already-existing-named files at once would
     * otherwise have each call stomp the previous one's pending prompt.
     *
     * @param {File} file
     * @param {jQuery} item
     */
    promptDuplicate(file, item) {
        // Marked pending synchronously (not just once the dialog resolves) so
        // a third same-named file later in the same queueUpload() forEach
        // loop is also treated as a same-batch replace, not another prompt.
        this.pendingUploadNames.add(file.name);
        this.duplicateQueue.push({file, item});
        this.processDuplicateQueue();
    }

    processDuplicateQueue() {
        if (this.duplicatePromptActive || this.duplicateQueue.length === 0) {
            return;
        }
        this.duplicatePromptActive = true;
        let {file, item} = this.duplicateQueue.shift();

        let $dialog = this.find('.fm-duplicate-dialog');
        let message = (this.settings.lang['message-duplicate'] || '":name" already exists in this location.')
            .replace(':name', file.name);
        $dialog.find('.fm-duplicate-message').text(message);
        $dialog.find('input[value=replace]').prop('checked', true);

        let resolve = () => {
            this.duplicatePromptActive = false;
            this.processDuplicateQueue();
        };
        $dialog.find('.fm-duplicate-cancel').off('click').on('click', () => {
            $dialog.modal('hide');
            this.pendingUploadNames.delete(file.name);
            this.markUploadError(item, this.settings.lang['message-duplicate-cancelled'] || 'Upload cancelled.');
            resolve();
        });
        $dialog.find('.fm-duplicate-confirm').off('click').on('click', () => {
            let onDuplicate = $dialog.find('input[type=radio]:checked').val();
            $dialog.modal('hide');
            // uploadFile() decrements activeUploads itself on completion, so it
            // needs to be re-incremented here to stay balanced with
            // processUploadQueue()'s own bookkeeping.
            this.activeUploads++;
            this.uploadFile(file, item, onDuplicate);
            resolve();
        });
        $dialog.modal('show');
    }

    /**
     * @param {File} file
     * @param {jQuery} item
     * @param {string} [onDuplicate] 'replace' or 'keep_both', set when retrying
     *                                after promptDuplicate()
     */
    uploadFile(file, item, onDuplicate) {
        let formData = new FormData();
        formData.append('upload[]', file);
        formData.append('working_dir', this.find('.fm-working-dir').val());
        formData.append('type', this.find('.fm-type').val());
        formData.append('_token', this.find('.fm-csrf-token').val());
        if (onDuplicate) {
            formData.append('on_duplicate', onDuplicate);
        }

        let xhr = new XMLHttpRequest();
        xhr.open('POST', this.settings.connectorUrl + '/upload', true);
        let token = this.getUrlParam('token');
        if (token !== null) {
            xhr.setRequestHeader('Authorization', 'Bearer ' + token);
        }
        xhr.upload.onprogress = (e) => {
            if (e.lengthComputable) {
                this.markUploadProgress(item, Math.round((e.loaded / e.total) * 100));
            }
        };
        xhr.onload = () => {
            this.activeUploads--;
            let response = xhr.responseText;
            if (window.cresenity.isJson(response)) {
                let json = JSON.parse(response);
                if (json.errCode == 0) {
                    this.pendingUploadNames.delete(file.name);
                    this.markUploadSuccess(item);
                    this.toast('success', (this.settings.lang['message-upload-success'] || 'Uploaded successfully.') + ' (' + file.name + ')');
                    this.loadFolders();
                } else if (!onDuplicate && json.errMessage === this.settings.lang['error-file-exist']) {
                    // Stale this.items (e.g. another tab created it since this
                    // folder was last loaded) -- pendingUploadNames stays as-is,
                    // promptDuplicate() re-adds it (harmless no-op) and clears
                    // it itself on cancel.
                    this.promptDuplicate(file, item);
                } else {
                    this.pendingUploadNames.delete(file.name);
                    this.markUploadError(item, json.errMessage || response);
                }
            } else {
                this.pendingUploadNames.delete(file.name);
                this.markUploadError(item, response);
            }
            this.processUploadQueue();
        };
        xhr.onerror = () => {
            this.activeUploads--;
            this.pendingUploadNames.delete(file.name);
            this.markUploadError(item, this.settings.lang['error-other'] || 'Upload failed');
            this.processUploadQueue();
        };
        xhr.send(formData);
    }


    setActiveDisplay() {
        this.find('[data-display]').removeClass('active');
        this.find('[data-display="' + this.showList + '"]').addClass('active');
    }

    toggleMobileTree(should_display) {
        if (should_display === undefined) {
            should_display = !this.find('.fm-tree').hasClass('in');
        }
        this.find('.fm-tree').toggleClass('in', should_display);
    }

    initModalDismissHandlers() {
        const checkModal = () => {
            let modalExists = $('.modal:visible').length > 0;
            if (!modalExists) {
                $('body').removeClass('modal-open');
                $('.modal-backdrop.show').remove();
            } else if (!$('body').hasClass('modal-open')) {
                $('body').addClass('modal-open');
            }
        };
        // Cleaning up the shared body/backdrop state genuinely is global,
        // regardless of which instance's modal just closed.
        $(document).on('hidden.bs.modal', checkModal);
        this.$root.on('click', '[data-dismiss-modal=dialog]', () => {
            this.find('.fm-dialog').modal('hide');
        });
        this.$root.on('click', '[data-dismiss-modal=notify]', () => {
            this.find('.fm-notify').modal('hide');
        });
        this.$root.on('click', '[data-dismiss-modal=uploadModal]', () => {
            this.find('.fm-upload-panel').addClass('d-none');
        });
    }

    /**
     * Shows the non-blocking upload panel. Unlike the old Bootstrap modal this
     * replaced, it has no backdrop and doesn't get hidden automatically -- the
     * user closes it explicitly (or it just stays out of the way) while
     * uploads keep running in the background regardless.
     */
    showUploadPanel() {
        this.find('.fm-upload-panel').removeClass('d-none');
    }
}
