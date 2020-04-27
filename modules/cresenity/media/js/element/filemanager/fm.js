


var CFileManager = function (options) {
    window.cfm = this;
    this.settings = $.extend({
        selector: '.capp-fm',
        connectorUrl: '/cresenity/connector/fm',
        sortType: 'alphabetic',
        
    }, options);

    this.multiSelectionEnabled = false;
    var fab = function (menu, options) {

        menu.addClass('fab-wrapper');
        var toggler = $('<a>')
                .addClass('fab-button fab-toggle')
                .append($('<i>').addClass('fas fa-plus'))
                .click(function () {
                    menu.toggleClass('fab-expand');
                });
        menu.append(toggler);
        options.buttons.forEach(function (button) {
            toggler.before(
                    $('<a>').addClass('fab-button fab-action')
                    .attr('data-label', button.label)
                    .attr('id', button.attrs.id)
                    .append($('<i>').addClass(button.icon))
                    .click(function () {
                        menu.removeClass('fab-expand');
                    })
                    );
        });
    };

    this.selected = [];
    this.items = [];

    this.showList = 'grid';
    this.callback = {};

    this.controllerMethod = {};


    this.haveCallback = (name) => {
        return typeof this.callback[name] == 'function';
    };

    this.doCallback = (name, ...args) => {
        if (this.haveCallback(name)) {
            this.callback[name](...args);
        }
    };

    this.setCallback = (name, cb) => {
        this.callback[name] = cb;
    };

    // ==================================
    // ==     Base Function            ==
    // ==================================

    this.getUrlParam = (paramName) => {
        var reParam = new RegExp('(?:[\?&]|&)' + paramName + '=([^&]+)', 'i');
        var match = window.location.search.match(reParam);
        return (match && match.length > 1) ? match[1] : null;
    }



    // ==================================
    // ==     WYSIWYG Editors Check    ==
    // ==================================

    this.usingTinymce3 = () => {
        return !!window.tinyMCEPopup;
    }

    this.usingTinymce4AndColorbox = () => {
        return !!this.getUrlParam('field_name');
    }

    this.usingCkeditor3 = () => {
        return !!this.getUrlParam('CKEditor') || !!this.getUrlParam('CKEditorCleanUpFuncNum');
    }

    this.usingFckeditor2 = () => {
        return window.opener && typeof data != 'undefined' && data['Properties']['Width'] != '';
    }

    this.usingWysiwygEditor = () => {
        return this.usingTinymce3() || this.usingTinymce4AndColorbox() || this.usingCkeditor3() || this.usingFckeditor2();
    }
    // ====================
    // ==  Ajax actions  ==
    // ====================

    this.performFmRequest = (url, parameter, type) => {
        var data = this.defaultParameters();
        if (parameter != null) {
            $.each(parameter, function (key, value) {
                data[key] = value;
            });
        }

        return $.ajax({
            type: 'GET',
            beforeSend: (request) => {
                var token = this.getUrlParam('token');
                if (token !== null) {
                    request.setRequestHeader("Authorization", 'Bearer ' + token);
                }
            },
            dataType: type || 'text',
            url: this.settings.connectorUrl + '/' + url,
            data: data,
            cache: false
        }).fail((jqXHR, textStatus, errorThrown) => {
            this.displayErrorResponse(jqXHR);
        });
    }

    this.displayErrorResponse = (jqXHR) => {
        console.log('Display Error Response');
        this.notify('<div style="max-height:50vh;overflow: scroll;">' + jqXHR.responseText + '</div>');
    };

    this.notify = (body, callback) => {
        $('#notify').find('.btn-primary').toggle(callback !== undefined);
        $('#notify').find('.btn-primary').unbind().click(callback);
        console.log(cresenity.isJson(body));
        if (cresenity.isJson(body)) {
            json = JSON.parse(body);
            eval(cresenity.base64.decode(json.js));
            console.log(cresenity.base64.decode(json.js))
            $('#notify').find('.modal-body').html(json.html);
            $('#notify').modal('show');
        } else {
            $('#notify').modal('show').find('.modal-body').html(body);
        }

    }

    this.notImp = () => {
        notify('Not yet implemented!');
    }

    this.defaultParameters = () => {
        return {
            working_dir: $('#working_dir').val(),
            type: $('#type').val()
        };
    }

    this.dialog = (title, value, callback) => {
        $('#dialog').find('input').val(value);
        $('#dialog').on('shown.bs.modal', function () {
            $('#dialog').find('input').focus();
        });
        $('#dialog').find('.btn-primary').unbind().click(function (e) {
            callback($('#dialog').find('input').val());
        });
        $('#dialog').modal('show').find('.modal-title').text(title);
    }

    this.refreshFoldersAndItems = (data) => {

        this.loadFolders();
        if (data != 'OK') {
            data = Array.isArray(data) ? data.join('<br/>') : data;
            this.notify(data);
        }

    }

    this.loadFolders = () => {

        var reloadOptions = {};
        reloadOptions.selector = '#tree';
        reloadOptions.url = this.settings.connectorUrl + '/folder';
        reloadOptions.onSuccess = (data) => {
            this.loadItems();
        }
        cresenity.reload(reloadOptions);
    }



// ======================
// ==  Folder actions  ==
// ======================

    this.goTo= (new_dir) => {
        $('#working_dir').val(new_dir);
        this.loadItems();
    }

    this.getPreviousDir = () => {
        var working_dir = $('#working_dir').val();
        return working_dir.substring(0, working_dir.lastIndexOf('/'));
    }

    this.setOpenFolders = () => {
        $('#tree [data-path]').each(function (index, folder) {
            // close folders that are not parent
            var should_open = ($('#working_dir').val() + '/').startsWith($(folder).data('path') + '/');
            $(folder).children('i')
                    .toggleClass('fa-folder-open', should_open)
                    .toggleClass('fa-folder', !should_open);
        });
        $('#tree .nav-item').removeClass('active');
        $('#tree [data-path="' + $('#working_dir').val() + '"]').parent('.nav-item').addClass('active');
    }


    this.controllerMethod.move = (items) => {

        this.performFmRequest('move', {items: items.map(function (item) {
                return item.name;
            })}).done(this.refreshFoldersAndItems);
    };

    this.controllerMethod.open = (item) => {
        this.goTo(item.url);
    }

    this.controllerMethod.preview = (items) => {
        var carousel = $('#carouselTemplate').clone().attr('id', 'previewCarousel').removeClass('d-none');
        var imageTemplate = carousel.find('.carousel-item').clone().removeClass('active');
        var indicatorTemplate = carousel.find('.carousel-indicators > li').clone().removeClass('active');
        carousel.children('.carousel-inner').html('');
        carousel.children('.carousel-indicators').html('');
        carousel.children('.carousel-indicators,.carousel-control-prev,.carousel-control-next').toggle(items.length > 1);
        items.forEach(function (item, index) {
            var carouselItem = imageTemplate.clone()
                    .addClass(index === 0 ? 'active' : '');
            if (item.thumb_url) {
                carouselItem.find('.carousel-image').css('background-image', 'url(\'' + item.url + '?timestamp=' + item.time + '\')');
            } else {
                carouselItem.find('.carousel-image').css('width', '50vh').append($('<div>').addClass('mime-icon ico-' + item.icon));
            }

            carouselItem.find('.carousel-label').attr('target', '_blank').attr('href', item.url)
                    .append(item.name)
                    .append($('<i class="fas fa-external-link-alt ml-2"></i>'));
            carousel.children('.carousel-inner').append(carouselItem);
            var carouselIndicator = indicatorTemplate.clone()
                    .addClass(index === 0 ? 'active' : '')
                    .attr('data-slide-to', index);
            carousel.children('.carousel-indicators').append(carouselIndicator);
        });
        // carousel swipe control
        var touchStartX = null;
        carousel.on('touchstart', function (event) {
            var e = event.originalEvent;
            if (e.touches.length == 1) {
                var touch = e.touches[0];
                touchStartX = touch.pageX;
            }
        }).on('touchmove', function (event) {
            var e = event.originalEvent;
            if (touchStartX != null) {
                var touchCurrentX = e.changedTouches[0].pageX;
                if ((touchCurrentX - touchStartX) > 60) {
                    touchStartX = null;
                    carousel.carousel('prev');
                } else if ((touchStartX - touchCurrentX) > 60) {
                    touchStartX = null;
                    carousel.carousel('next');
                }
            }
        }).on('touchend', function () {
            touchStartX = null;
        });
        // end carousel swipe control

        this.notify(carousel);
    }


    // ==========================
    // ==  Multiple Selection  ==
    // ==========================

    this.toggleSelected = (e) => {
        if (!this.multiSelectionEnabled) {
            this.selected = [];
        }
        

        var sequence = $(e.target).closest('a').data('id');
        var elementIndex = this.selected.indexOf(sequence);
        
        if (elementIndex === -1) {
            this.selected.push(sequence);
        } else {    
            this.selected.splice(elementIndex, 1);
        }

        this.updateSelectedStyle();
    }

    this.clearSelected = () => {
        this.selected = [];
        this.multiSelectionEnabled = false;
        this.updateSelectedStyle();
    }

    this.updateSelectedStyle = () => {
        this.items.forEach(function (item, index) {
            $('[data-id=' + index + ']')
                    .find('.square')
                    .toggleClass('selected', cfm.selected.indexOf(index) > -1);
        });
        this.toggleActions();
    }

    this.getOneSelectedElement = (orderOfItem) => {
        var index = orderOfItem !== undefined ? orderOfItem : cfm.selected[0];
        return this.items[index];
    }

    this.getSelectedItems = () => {
        return cfm.selected.reduce( (arrObjects, id) => {
            arrObjects.push(this.getOneSelectedElement(id));
            return arrObjects;
        }, []);
    }

    this.toggleActions = () => {
        var oneSelected = cfm.selected.length === 1;
        var manySelected = cfm.selected.length >= 1;
        var onlyImage = this.getSelectedItems()
                .filter(function (item) {
                    return !item.is_image;
                })
                .length === 0;
        var onlyFile = this.getSelectedItems()
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
        $('#actions').toggleClass('d-none', cfm.selected.length === 0);
        $('#fab').toggleClass('d-none', cfm.selected.length !== 0);
    }


    this.controllerMethod.rename = (item) => {
        this.dialog(this.settings.lang['message-rename'], item.name,  (new_name) => {
            this.performFmRequest('rename', {
                file: item.name,
                new_name: new_name
            }).done(this.refreshFoldersAndItems);
        });
    }

    this.controllerMethod.trash = (items) => {
        this.notify(this.settings.lang['message-delete'],  () => {
            this.performFmRequest('delete', {
                items: items.map(function (item) {
                    return item.name;
                })
            }).done(this.refreshFoldersAndItems)
        });
    }

    this.controllerMethod.crop = (item) => {
        this.performFmRequest('crop', {img: item.name})
                .done(this.hideNavAndShowEditor);
    }

    this.controllerMethod.resize = (item) => {
        this.performFmRequest('resize', {img: item.name})
                .done(this.hideNavAndShowEditor);
    }

    this.controllerMethod.download = (items) => {
        items.forEach( (item, index) => {
            var data = this.defaultParameters();
            data['file'] = item.name;
            var token = this.getUrlParam('token');
            if (token) {
                data['token'] = token;
            }

            setTimeout( () => {
                window.location.href = this.connectorUrl + '/download?' + $.param(data);
            }, index * 100);
        });
    }

    this.loadItems = () => {
        this.loading(true);
        this.performFmRequest('item', {showList: this.showList, sortType: this.sortType}, 'html')
                .done( (data) => {
                    cfm.selected = [];
                    var response = JSON.parse(data);
                    var working_dir = response.working_dir;
                    cfm.items = response.items;
                    var hasItems = cfm.items.length !== 0;
                    $('#empty').toggleClass('d-none', hasItems);
                    $('#content').html('').removeAttr('class');
                    if (hasItems) {
                        $('#content').addClass(response.display).addClass('preserve_actions_space');
                        cfm.items.forEach( (item, index) => {
                            var template = $('#item-template').clone()
                                    .removeAttr('id class')
                                    .attr('data-id', index)
                                    .click(cfm.toggleSelected)
                                    .dblclick(function (e) {
                                        if (item.is_file) {
                                            cfm.controllerMethod.use(cfm.getSelectedItems());
                                        } else {
                                            cfm.goTo(item.url);
                                        }
                                    });
                            if (item.thumb_url) {
                                var image = $('<div>').css('background-image', 'url("' + item.thumb_url + '?timestamp=' + item.time + '")');
                            } else {
                                var image = $('<div>').addClass('mime-icon ico-' + item.icon);
                            }

                            template.find('.square').append(image);
                            template.find('.item_name').text(item.name);
                            template.find('time').text((new Date(item.time * 1000)).toLocaleString());
                            $('#content').append(template);
                        });
                    }

                    $('#nav-buttons > ul').removeClass('d-none');
                    $('#working_dir').val(working_dir);
                    console.log('Current working_dir : ' + working_dir);
                    var breadcrumbs = [];
                    var validSegments = working_dir.split('/').filter(function (e) {
                        return e;
                    });
                    validSegments.forEach( (segment, index) => {
                        if (index === 0) {
                            // set root folder name as the first breadcrumb
                            breadcrumbs.push($("[data-path='/" + segment + "']").text());
                        } else {
                            breadcrumbs.push(segment);
                        }
                    });
                    $('#current_folder').text(breadcrumbs[breadcrumbs.length - 1]);
                    $('#breadcrumbs > ol').html('');
                    breadcrumbs.forEach( (breadcrumb, index) => {
                        var li = $('<li>').addClass('breadcrumb-item').text(breadcrumb);
                        if (index === breadcrumbs.length - 1) {
                            li.addClass('active').attr('aria-current', 'page');
                        } else {
                            li.click( () => {
                                // go to corresponding path
                                this.goTo('/' + validSegments.slice(0, 1 + index).join('/'));
                            });
                        }

                        $('#breadcrumbs > ol').append(li);
                    });
                    var atRootFolder = this.getPreviousDir() == '';
                    $('#to-previous').toggleClass('d-none invisible-lg', atRootFolder);
                    $('#show_tree').toggleClass('d-none', !atRootFolder).toggleClass('d-block', atRootFolder);
                    this.setOpenFolders();
                    this.loading(false);
                    this.toggleActions();
                });
    }

    this.loading = (showLoading) => {
        $('#loading').toggleClass('d-none', !showLoading);
    };

    this.createFolder = (folderName) => {
        this.performFmRequest('newFolder', {name: folderName})
                .done(this.refreshFoldersAndItems);
    };




// ======================
// ==  Navbar actions  ==
// ======================

    $('#multi_selection_toggle').click(() => {
        this.multiSelectionEnabled = !multiSelectionEnabled;
        $('#multi_selection_toggle i')
                .toggleClass('fa-times', this.multiSelectionEnabled)
                .toggleClass('fa-check-double', !this.multiSelectionEnabled);
        if (!multiSelectionEnabled) {
            this.clearSelected();
        }
    });
    $('#to-previous').click( () => {
        var previous_dir = this.getPreviousDir();
        if (previous_dir == '') {
            return;
        }
        this.goTo(previous_dir);
    });
    this.toggleMobileTree = (should_display) => {
        if (should_display === undefined) {
            should_display = !$('#tree').hasClass('in');
        }
        $('#tree').toggleClass('in', should_display);
    }

    $('#show_tree').click((e) => {
        this.toggleMobileTree();
    });
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
    $(document).on('click', '[data-display]', function() {
        cfm.showList = $(this).data('display');
        cfm.loadItems();
    });
    $(document).on('click', '[data-action]', function()  {
        cfm.controllerMethod[$(this).data('action')]($(this).data('multiple') ? cfm.getSelectedItems() : cfm.getOneSelectedElement());
    });

    $(document).on('click', '#tree a', (e) => {
        this.goTo($(e.target).closest('a').data('path'));
        this.toggleMobileTree(false);
    });


    fab($('#fab'), {
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
    this.settings.sortings.forEach(function (sort) {
        $('#nav-buttons .dropdown-menu').append(
                $('<a>').addClass('dropdown-item').attr('data-sortby', sort.by)
                .append($('<i>').addClass('fas fa-fw fa-' + sort.icon))
                .append($('<span>').text(sort.label))
                .click(function () {
                    sort_type = sort.by;
                    loadItems();
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


}

