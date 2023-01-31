import { makeFab } from './make-fab';
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
        window.Dropzone.autoDiscover = false;

        window.cfm = this;

        // all html elements
        this.elements =
            className instanceof Element
                ? [className]
                : [].slice.call(document.querySelectorAll(className));
        if (this.elements.length < 1) {
            return;
        }
        this.element = this.elements[0];
        const cresConfig = JSON.parse(this.element.getAttribute('cres-config'));
        this.settings = $.extend({
            selector: '.capp-fm',
            connectorUrl: '/cresenity/connector/fm',
            sortType: 'alphabetic',
            lang: {
                'nav-upload': 'Upload'
            }
        }, cresConfig, config);
        this.dropzoneInitilized = false;
        this.multiSelectionEnabled = false;

        this.selected = [];
        this.items = [];

        this.showList = 'grid';
        this.callback = {};

        this.controllerMethod = {};
        this.controllerMethod.move = moveMethod;

        this.controllerMethod.open = openMethod;

        this.controllerMethod.preview = previewMethod;
        this.controllerMethod.rename = renameMethod;
        this.controllerMethod.trash = trashMethod;
        this.controllerMethod.crop = cropMethod;
        this.controllerMethod.resize = resizeMethod;

        this.controllerMethod.download = downloadMethod;
        this.controllerMethod.use = useMethod;
        // ======================
        // ==  Navbar actions  ==
        // ======================


        $('#multi_selection_toggle').click(() => {
            this.multiSelectionEnabled = !this.multiSelectionEnabled;
            $('#multi_selection_toggle i')
                .toggleClass('fa-times', this.multiSelectionEnabled)
                .toggleClass('fa-check-double', !this.multiSelectionEnabled);
            if (!this.multiSelectionEnabled) {
                this.clearSelected();
            }
        });
        $('#to-previous').click(() => {
            let previous_dir = this.getPreviousDir();
            if (previous_dir == '') {
                return;
            }
            this.goTo(previous_dir);
        });
        // eslint-disable-next-line no-unused-vars
        $('#show_tree').click((e) => {
            this.toggleMobileTree();
        });
        // eslint-disable-next-line no-unused-vars
        $('#main').click((e) => {
            if ($('#tree').hasClass('in')) {
                this.toggleMobileTree(false);
            }
        });
        $(document).on('click', '#add-folder', () => {
            this.dialog(this.settings.lang['message-name'], '', this.createFolder);
        });
        $(document).on('click', '#upload', () => {
            $('#uploadModal').modal('show');
        });
        $(document).on('click', '[data-display]', (e) => {
            let target = e.currentTarget;
            this.showList = $(target).data('display');
            this.loadItems();
        });
        $(document).on('click', '[data-action]', (e) => {
            let target = e.currentTarget;
            this.controllerMethod[$(target).data('action')]($(target).data('multiple') ? this.getSelectedItems() : this.getOneSelectedElement());
        });

        $(document).on('click', '#tree a', (e) => {
            this.goTo($(e.target).closest('a').data('path'));
            this.toggleMobileTree(false);
        });

        makeFab($('#fab'), {
            buttons: [
                {
                    icon: 'fas fa-upload',
                    label: this.settings.lang['nav-upload'],
                    attrs: {id: 'upload'}
                },
                {
                    icon: 'fas fa-folder',
                    label: this.settings.lang['nav-new'],
                    attrs: {id: 'add-folder'}
                }
            ]
        });
        this.settings.actions.reverse().forEach(function (action) {
            $('#nav-buttons > ul').prepend(
                $('<li>').addClass('nav-item').append(
                    $('<a>').addClass('nav-link d-none')
                        .attr('data-action', action.name)
                        .attr('data-multiple', action.multiple)
                        .append($('<i>').addClass('fas fa-fw fa-' + action.icon))
                        .append($('<span>').text(action.label))
                )
            );
        });
        this.settings.sortings.forEach((sort) => {
            $('#nav-buttons .dropdown-menu').append(
                $('<a>').addClass('dropdown-item').attr('data-sortby', sort.by)
                    .append($('<i>').addClass('fas fa-fw fa-' + sort.icon))
                    .append($('<span>').text(sort.label))
                    .click(() => {
                        this.sortType = sort.by;
                        this.loadItems();
                    })
            );
        });
        this.loadFolders();
        this.performFmRequest('error')
            .done((response) => {
                JSON.parse(response).forEach(function (message) {
                    $('#alerts').append(
                        $('<div>').addClass('alert alert-warning')
                            .append($('<i>').addClass('fas fa-exclamation-circle'))
                            .append(' ' + message)
                    );
                });
            });
        $(window).on('dragenter', () => {
            $('#uploadModal').modal('show');
        });
        if (this.usingWysiwygEditor()) {
            $('#multi_selection_toggle').hide();
        }

        this.initializeUploadForm();
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
        $('#notify').find('.btn-primary').toggle(callback !== undefined);
        $('#notify').find('.btn-primary').unbind().click(()=>{
            $('#notify').modal('hide');
            callback();
        });

        if (window.cresenity.isJson(body)) {
            let json = JSON.parse(body);
            let message = json.html;
            if(json.exception && json.message) {
                message = json.message;
            }
            $('#notify').find('.modal-body').html(message);
            if(json.js) {
                eval(window.cresenity.base64.decode(json.js));
            }
            $('#notify').modal('show');
        } else {
            $('#notify').modal('show').find('.modal-body').html(body);
        }
    }

    notImp() {
        this.notify('error', 'Not yet implemented!');
    }

    defaultParameters() {
        return {
            working_dir: $('#working_dir').val(),
            type: $('#type').val()
        };
    }

    dialog(title, value, callback) {
        $('#dialog').find('input').val(value);
        $('#dialog').on('shown.bs.modal', function () {
            $('#dialog').find('input').focus();
        });
        // eslint-disable-next-line no-unused-vars
        $('#dialog').find('.btn-primary').unbind().click(function (e) {
            $('#dialog').modal('hide');
            callback($('#dialog').find('input').val());
        });
        $('#dialog').find('.modal-title').text(title);
        $('#dialog').modal();
    }

    refreshFoldersAndItems(data) {
        this.loadFolders();
        if (data != 'OK') {
            data = Array.isArray(data) ? data.join('<br/>') : data;
            this.notify(data);
        }
    }

    loadFolders() {
        let reloadOptions = {};
        reloadOptions.selector = '#tree';
        reloadOptions.url = this.settings.connectorUrl + '/folder';
        // eslint-disable-next-line no-unused-vars
        reloadOptions.onSuccess = (data) => {
            this.loadItems();
        };
        window.cresenity.reload(reloadOptions);
    }


    // ======================
    // ==  Folder actions  ==
    // ======================

    goTo(new_dir) {
        $('#working_dir').val(new_dir);
        this.loadItems();
    }

    getPreviousDir() {
        let working_dir = $('#working_dir').val();
        if (working_dir) {
            return working_dir.substring(0, working_dir.lastIndexOf('/'));
        }
        return null;
    }

    setOpenFolders() {
        $('#tree [data-path]').each(function (index, folder) {
            // close folders that are not parent
            let should_open = ($('#working_dir').val() + '/').startsWith($(folder).data('path') + '/');
            $(folder).children('i')
                .toggleClass('fa-folder-open', should_open)
                .toggleClass('fa-folder', !should_open);
        });
        $('#tree .nav-item').removeClass('active');
        $('#tree [data-path="' + $('#working_dir').val() + '"]').parent('.nav-item').addClass('active');
    }


    // ==========================
    // ==  Multiple Selection  ==
    // ==========================

    toggleSelected(e) {
        if (!this.multiSelectionEnabled) {
            this.selected = [];
        }


        let sequence = $(e.target).closest('a').data('id');
        let elementIndex = this.selected.indexOf(sequence);

        if (elementIndex === -1) {
            this.selected.push(sequence);
        } else {
            this.selected.splice(elementIndex, 1);
        }

        this.updateSelectedStyle();
    }

    clearSelected() {
        this.selected = [];
        this.multiSelectionEnabled = false;
        this.updateSelectedStyle();
    }

    updateSelectedStyle() {
        this.items.forEach((item, index) => {
            $('[data-id=' + index + ']')
                .find('.square')
                .toggleClass('selected', this.selected.indexOf(index) > -1);
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
        $('[data-action=use]').toggleClass('d-none', !(manySelected && onlyFile));
        $('[data-action=rename]').toggleClass('d-none', !oneSelected);
        $('[data-action=preview]').toggleClass('d-none', !(manySelected && onlyFile));
        $('[data-action=move]').toggleClass('d-none', !manySelected);
        $('[data-action=download]').toggleClass('d-none', !(manySelected && onlyFile));
        $('[data-action=resize]').toggleClass('d-none', !(oneSelected && onlyImage));
        $('[data-action=crop]').toggleClass('d-none', !(oneSelected && onlyImage));
        $('[data-action=trash]').toggleClass('d-none', !manySelected);
        $('[data-action=open]').toggleClass('d-none', !oneSelected || onlyFile);
        $('#multi_selection_toggle').toggleClass('d-none', this.usingWysiwygEditor() || !manySelected);
        $('#actions').toggleClass('d-none', this.selected.length === 0);
        $('#fab').toggleClass('d-none', this.selected.length !== 0);
    }


    loadItems() {
        this.loading(true);
        this.performFmRequest('item', {showList: this.showList, sortType: this.sortType}, 'html')
            .done((data) => {
                this.selected = [];
                let response = JSON.parse(data);
                let working_dir = response.working_dir;
                this.items = response.items;
                let hasItems = window.cfm.items.length !== 0;
                $('#empty').toggleClass('d-none', hasItems);
                $('#content').html('').removeAttr('class');
                if (hasItems) {
                    $('#content').addClass(response.display).addClass('preserve_actions_space');
                    this.items.forEach((item, index) => {
                        let template = $('#item-template').clone()
                            .removeAttr('id class')
                            .attr('data-id', index)
                            .click(window.cfm.toggleSelected)
                            // eslint-disable-next-line no-unused-vars
                            .dblclick(function (e) {
                                if (item.is_file) {
                                    window.cfm.controllerMethod.use(window.cfm.getSelectedItems());
                                } else {
                                    window.cfm.goTo(item.url);
                                }
                            });
                        let image;
                        if (item.thumb_url) {
                            image = $('<div>').css('background-image', 'url("' + item.thumb_url + '?timestamp=' + item.time + '")');
                        } else {
                            image = $('<div>').addClass('mime-icon ico-' + item.icon);
                        }


                        template.find('.square').append(image);
                        template.find('.item_name').text(item.name);
                        template.find('time').text((new Date(item.time * 1000)).toLocaleString());
                        if (!item.is_file) {
                            template.find('time').addClass('d-none');
                        } else {
                            template.find('time').removeClass('d-none');
                        }
                        $('#content').append(template);
                    });
                }

                $('#nav-buttons > ul').removeClass('d-none');
                $('#working_dir').val(working_dir);

                let breadcrumbs = [];
                let validSegments = working_dir.split('/').filter(function (e) {
                    return e;
                });
                validSegments.forEach((segment, index) => {
                    if (index === 0) {
                        // set root folder name as the first breadcrumb
                        breadcrumbs.push($('[data-path=\'/' + segment + '\']').text());
                    } else {
                        breadcrumbs.push(segment);
                    }
                });
                $('#current_folder').text(breadcrumbs[breadcrumbs.length - 1]);
                $('#breadcrumbs > ol').html('');
                breadcrumbs.forEach((breadcrumb, index) => {
                    let li = $('<li>').addClass('breadcrumb-item').text(breadcrumb);
                    if (index === breadcrumbs.length - 1) {
                        li.addClass('active').attr('aria-current', 'page');
                    } else {
                        li.click(() => {
                            // go to corresponding path
                            this.goTo('/' + validSegments.slice(0, 1 + index).join('/'));
                        });
                    }

                    $('#breadcrumbs > ol').append(li);
                });
                let atRootFolder = this.getPreviousDir() == '';
                $('#to-previous').toggleClass('d-none invisible-lg', atRootFolder);
                $('#show_tree').toggleClass('d-none', !atRootFolder).toggleClass('d-block', atRootFolder);
                this.setOpenFolders();
                this.loading(false);
                this.toggleActions();
            });
    }

    loading(showLoading) {
        $('#loading').toggleClass('d-none', !showLoading);
    }

    createFolder(folderName) {
        this.performFmRequest('newFolder', {name: folderName})
            .done(this.refreshFoldersAndItems);
    }

    initializeUploadForm() {
        if (!this.dropzoneInitilized) {
            this.dropzoneInitilized = true;

            // eslint-disable-next-line no-unused-vars
            let dropzone = new window.Dropzone('#uploadForm', {
                paramName: 'upload[]', // The name that will be used to transfer the file
                uploadMultiple: false,
                parallelUploads: 5,
                clickable: '#upload-button',
                dictDefaultMessage: this.settings.lang['message-drop'],
                init: function () {
                    this.on('success', function (file, response) {
                        if (response == 'OK') {
                            window.cfm.loadFolders();
                        } else if (window.cresenity.isJson(response)) {
                            let json = JSON.parse(response);
                            this.defaultOptions.error(file, json.join('\n'));
                        } else {
                            this.defaultOptions.error(file, response);
                        }
                    });
                },
                headers: {
                    Authorization: 'Bearer ' + this.getUrlParam('token')
                },
                acceptedFiles: this.settings.acceptedFiles,
                maxFilesize: (this.settings.maxFilesize / 1000)
            });
        }
    }


    toggleMobileTree(should_display) {
        if (should_display === undefined) {
            should_display = !$('.capp-fm-tree').hasClass('in');
        }
        $('.capp-fm-tree').toggleClass('in', should_display);
    }
}
