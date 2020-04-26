<?php
defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 11, 2019, 12:53:38 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
?>
<div class="capp-fm">
    <nav class="navbar sticky-top navbar-expand-lg navbar-dark" id="nav">
        <a class="navbar-brand invisible-lg d-none d-lg-inline" id="to-previous">
            <i class="fas fa-arrow-left fa-fw"></i>
            <span class="d-none d-lg-inline"><?php echo clang::__('filemanager.nav-back'); ?></span>
        </a>
        <a class="navbar-brand d-block d-lg-none" id="show_tree">
            <i class="fas fa-bars fa-fw"></i>
        </a>
        <a class="navbar-brand d-block d-lg-none" id="current_folder"></a>
        <a id="loading" class="navbar-brand"><i class="fas fa-spinner fa-spin"></i></a>
        <div class="ml-auto px-2">
            <a class="navbar-link d-none" id="multi_selection_toggle">
                <i class="fa fa-check-double fa-fw"></i>
                <span class="d-none d-lg-inline"><?php echo clang::__('filemanager.menu-multiple'); ?></span>
            </a>
        </div>
        <a class="navbar-toggler collapsed border-0 px-1 py-2 m-0" data-toggle="collapse" data-target="#nav-buttons">
            <i class="fas fa-cog fa-fw"></i>
        </a>
        <div class="collapse navbar-collapse flex-grow-0" id="nav-buttons">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-display="grid">
                        <i class="fas fa-th-large fa-fw"></i>
                        <span><?php echo clang::__('filemanager.nav-thumbnails'); ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-display="list">
                        <i class="fas fa-list-ul fa-fw"></i>
                        <span><?php echo clang::__('filemanager.nav-list'); ?></span>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-sort fa-fw"></i><?php echo clang::__('filemanager.nav-sort'); ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right border-0"></div>
                </li>
            </ul>
        </div>
    </nav>

    <nav class="bg-light fixed-bottom border-top d-none" id="actions">
        <a data-action="open" data-multiple="false"><i class="fas fa-folder-open"></i><?php echo clang::__('filemanager.btn-open'); ?></a>
        <?php if ($fm->config('action.preview')): ?>
            <a data-action="preview" data-multiple="true"><i class="fas fa-images"></i><?php echo clang::__('filemanager.menu-view'); ?></a>
        <?php endif; ?>
        <?php if ($fm->config('action.use')): ?>
            <a data-action="use" data-multiple="true"><i class="fas fa-check"></i><?php echo clang::__('filemanager.btn-confirm'); ?></a>
        <?php endif; ?>
    </nav>

    <div class="d-flex flex-row">
        <div id="tree"></div>

        <div id="main">
            <div id="alerts"></div>

            <nav aria-label="breadcrumb" class="d-none d-lg-block" id="breadcrumbs">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item invisible">Home</li>
                </ol>
            </nav>

            <div id="empty" class="d-none">
                <i class="far fa-folder-open"></i>
                <?php echo clang::__('filemanager.message-empty'); ?>
            </div>

            <div id="content"></div>

            <a id="item-template" class="d-none">
                <div class="square"></div>

                <div class="info">
                    <div class="item_name text-truncate"></div>
                    <time class="text-muted font-weight-light text-truncate"></time>
                </div>
            </a>
        </div>

        <div id="fab"></div>
    </div>

    <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel"><?php echo clang::__('filemanager.title-upload'); ?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aia-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <form action="<?php echo rtrim($fm->connectorUrl(), '/') . '/upload'; ?>" role='form' id='uploadForm' name='uploadForm' method='post' enctype='multipart/form-data' class="dropzone">
                        <div class="form-group" id="attachment">
                            <div class="controls text-center">
                                <div class="input-group w-100">
                                    <a class="btn btn-primary w-100 text-white" id="upload-button"><?php echo clang::__('filemanager.message-choose'); ?></a>
                                </div>
                            </div>
                        </div>
                        <input type='hidden' name='working_dir' id='working_dir'>
                        <input type='hidden' name='type' id='type' value='<?php echo CHTTP::request()->input('type'); ?>'>
                        <input type='hidden' name='_token' value='{{csrf_token()}}'>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary w-100" data-dismiss="modal"><?php echo clang::__('filemanager.btn-close'); ?></button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="notify" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary w-100" data-dismiss="modal"><?php echo clang::__('filemanager.btn-close'); ?></button>
                    <button type="button" class="btn btn-primary w-100" data-dismiss="modal"><?php echo clang::__('filemanager.btn-confirm'); ?></button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="dialog" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary w-100" data-dismiss="modal"><?php echo clang::__('filemanager.btn-close'); ?></button>
                    <button type="button" class="btn btn-primary w-100" data-dismiss="modal"><?php echo clang::__('filemanager.btn-confirm'); ?></button>
                </div>
            </div>
        </div>
    </div>

    <div id="carouselTemplate" class="d-none carousel slide bg-light" data-ride="carousel">
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
<script>
    var lang = <?php echo json_encode($fm->getTranslation()); ?>;
    var config = {};
    config.action = <?php echo json_encode($fm->config('action')); ?>;
    var actions = [];
    if (config.action.use) {
        actions.push({
            name: 'use',
            icon: 'check',
            label: 'Confirm',
            multiple: true
        });
    }
    if (config.action.use) {
        actions.push({
            name: 'rename',
            icon: 'edit',
            label: lang['menu-rename'],
            multiple: false
        });
    }
    if (config.action.use) {
        actions.push({
            name: 'download',
            icon: 'download',
            label: lang['menu-download'],
            multiple: true
        });
    }
    if (config.action.use) {
        actions.push({
            name: 'preview',
            icon: 'image',
            label: lang['menu-view'],
            multiple: true
        });
    }
    if (config.action.use) {
        actions.push({
            name: 'move',
            icon: 'paste',
            label: lang['menu-move'],
            multiple: true
        });
    }
    if (config.action.use) {
        actions.push({
            name: 'resize',
            icon: 'arrows-alt',
            label: lang['menu-resize'],
            multiple: false
        });
    }
    if (config.action.use) {
        actions.push({
            name: 'crop',
            icon: 'crop',
            label: lang['menu-crop'],
            multiple: false
        });
    }
    if (config.action.use) {
        actions.push({
            name: 'trash',
            icon: 'trash',
            label: lang['menu-delete'],
            multiple: true
        });
    }



    var sortings = [
        {
            by: 'alphabetic',
            icon: 'sort-alpha-down',
            label: lang['nav-sort-alphabetic']
        },
        {
            by: 'time',
            icon: 'sort-numeric-down',
            label: lang['nav-sort-time']
        }
    ];

    var fmRoute = '<?php echo rtrim($fm->connectorUrl(), '/'); ?>';
    var show_list;
    var sort_type = 'alphabetic';
    var multi_selection_enabled = false;
    var selected = [];
    var items = [];
    $.fn.fab = function (options) {
        var menu = this;
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
    $(document).ready(function () {
        $('#fab').fab({
            buttons: [
                {
                    icon: 'fas fa-upload',
                    label: lang['nav-upload'],
                    attrs: {id: 'upload'}
                },
                {
                    icon: 'fas fa-folder',
                    label: lang['nav-new'],
                    attrs: {id: 'add-folder'}
                }
            ]
        });
        actions.reverse().forEach(function (action) {
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
        sortings.forEach(function (sort) {
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
        loadFolders();
        performFmRequest('error')
                .done(function (response) {
                    JSON.parse(response).forEach(function (message) {
                        $('#alerts').append(
                                $('<div>').addClass('alert alert-warning')
                                .append($('<i>').addClass('fas fa-exclamation-circle'))
                                .append(' ' + message)
                                );
                    });
                });
        $(window).on('dragenter', function () {
            $('#uploadModal').modal('show');
        });
        if (usingWysiwygEditor()) {
            $('#multi_selection_toggle').hide();
        }
    });
// ======================
// ==  Navbar actions  ==
// ======================

    $('#multi_selection_toggle').click(function () {
        multi_selection_enabled = !multi_selection_enabled;
        $('#multi_selection_toggle i')
                .toggleClass('fa-times', multi_selection_enabled)
                .toggleClass('fa-check-double', !multi_selection_enabled);
        if (!multi_selection_enabled) {
            clearSelected();
        }
    });
    $('#to-previous').click(function () {
        var previous_dir = getPreviousDir();
        if (previous_dir == '')
            return;
        goTo(previous_dir);
    });
    function toggleMobileTree(should_display) {
        if (should_display === undefined) {
            should_display = !$('#tree').hasClass('in');
        }
        $('#tree').toggleClass('in', should_display);
    }

    $('#show_tree').click(function (e) {
        toggleMobileTree();
    });
    $('#main').click(function (e) {
        if ($('#tree').hasClass('in')) {
            toggleMobileTree(false);
        }
    });
    $(document).on('click', '#add-folder', function () {
        dialog(lang['message-name'], '', createFolder);
    });
    $(document).on('click', '#upload', function () {
        $('#uploadModal').modal('show');
    });
    $(document).on('click', '[data-display]', function () {
        show_list = $(this).data('display');
        loadItems();
    });
    $(document).on('click', '[data-action]', function () {
        window[$(this).data('action')]($(this).data('multiple') ? getSelectedItems() : getOneSelectedElement());
    });
// ==========================
// ==  Multiple Selection  ==
// ==========================

    function toggleSelected(e) {
        if (!multi_selection_enabled) {
            selected = [];
        }

        var sequence = $(e.target).closest('a').data('id');
        var element_index = selected.indexOf(sequence);
        if (element_index === -1) {
            selected.push(sequence);
        } else {
            selected.splice(element_index, 1);
        }

        updateSelectedStyle();
    }

    function clearSelected() {
        selected = [];
        multi_selection_enabled = false;
        updateSelectedStyle();
    }

    function updateSelectedStyle() {
        items.forEach(function (item, index) {
            $('[data-id=' + index + ']')
                    .find('.square')
                    .toggleClass('selected', selected.indexOf(index) > -1);
        });
        toggleActions();
    }

    function getOneSelectedElement(orderOfItem) {
        var index = orderOfItem !== undefined ? orderOfItem : selected[0];
        return items[index];
    }

    function getSelectedItems() {
        return selected.reduce(function (arr_objects, id) {
            arr_objects.push(getOneSelectedElement(id));
            return arr_objects
        }, []);
    }

    function toggleActions() {
        var one_selected = selected.length === 1;
        var many_selected = selected.length >= 1;
        var only_image = getSelectedItems()
                .filter(function (item) {
                    return !item.is_image;
                })
                .length === 0;
        var only_file = getSelectedItems()
                .filter(function (item) {
                    return !item.is_file;
                })
                .length === 0;
        $('[data-action=use]').toggleClass('d-none', !(many_selected && only_file));
        $('[data-action=rename]').toggleClass('d-none', !one_selected);
        $('[data-action=preview]').toggleClass('d-none', !(many_selected && only_file));
        $('[data-action=move]').toggleClass('d-none', !many_selected);
        $('[data-action=download]').toggleClass('d-none', !(many_selected && only_file));
        $('[data-action=resize]').toggleClass('d-none', !(one_selected && only_image));
        $('[data-action=crop]').toggleClass('d-none', !(one_selected && only_image));
        $('[data-action=trash]').toggleClass('d-none', !many_selected);
        $('[data-action=open]').toggleClass('d-none', !one_selected || only_file);
        $('#multi_selection_toggle').toggleClass('d-none', usingWysiwygEditor() || !many_selected);
        $('#actions').toggleClass('d-none', selected.length === 0);
        $('#fab').toggleClass('d-none', selected.length !== 0);
    }

// ======================
// ==  Folder actions  ==
// ======================

    $(document).on('click', '#tree a', function (e) {
        goTo($(e.target).closest('a').data('path'));
        toggleMobileTree(false);
    });
    function goTo(new_dir) {
        $('#working_dir').val(new_dir);
        loadItems();
    }

    function getPreviousDir() {
        var working_dir = $('#working_dir').val();
        return working_dir.substring(0, working_dir.lastIndexOf('/'));
    }

    function setOpenFolders() {
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

    // ====================
    // ==  Ajax actions  ==
    // ====================

    function performFmRequest(url, parameter, type) {
        var data = defaultParameters();
        if (parameter != null) {
            $.each(parameter, function (key, value) {
                data[key] = value;
            });
        }

        return $.ajax({
            type: 'GET',
            beforeSend: function (request) {
                var token = getUrlParam('token');
                if (token !== null) {
                    request.setRequestHeader("Authorization", 'Bearer ' + token);
                }
            },
            dataType: type || 'text',
            url: fmRoute + '/' + url,
            data: data,
            cache: false
        }).fail(function (jqXHR, textStatus, errorThrown) {
            displayErrorResponse(jqXHR);
        });
    }


    function displayErrorResponse(jqXHR) {
        console.log('Display Error Response');
        notify('<div style="max-height:50vh;overflow: scroll;">' + jqXHR.responseText + '</div>');
    }

    var refreshFoldersAndItems = function (data) {
        loadFolders();
        if (data != 'OK') {
            data = Array.isArray(data) ? data.join('<br/>') : data;
            notify(data);
        }
    };
    var hideNavAndShowEditor = function (data) {
        $('#nav-buttons > ul').addClass('d-none');
        var content = $('#content');
        if (cresenity.isJson(data)) {
            json = JSON.parse(data);
            eval(cresenity.base64.decode(json.js))
            content.html(json.html);
        } else {
            content.html(data);
        }
        content.removeClass('preserve_actions_space');
        clearSelected();
    }

    function loadFolders() {

        var reloadOptions = {};
        reloadOptions.selector = '#tree';
        reloadOptions.url = fmRoute + '/folder';
        reloadOptions.onSuccess = function (data) {
            loadItems();
        }
        cresenity.reload(reloadOptions);
    }

    function loadItems() {
        loading(true);
        performFmRequest('item', {show_list: show_list, sort_type: sort_type}, 'html')
                .done(function (data) {
                    selected = [];
                    var response = JSON.parse(data);
                    var working_dir = response.working_dir;
                    items = response.items;
                    var hasItems = items.length !== 0;
                    $('#empty').toggleClass('d-none', hasItems);
                    $('#content').html('').removeAttr('class');
                    if (hasItems) {
                        $('#content').addClass(response.display).addClass('preserve_actions_space');
                        items.forEach(function (item, index) {
                            var template = $('#item-template').clone()
                                    .removeAttr('id class')
                                    .attr('data-id', index)
                                    .click(toggleSelected)
                                    .dblclick(function (e) {
                                        if (item.is_file) {
                                            use(getSelectedItems());
                                        } else {
                                            goTo(item.url);
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
                    validSegments.forEach(function (segment, index) {
                        if (index === 0) {
                            // set root folder name as the first breadcrumb
                            breadcrumbs.push($("[data-path='/" + segment + "']").text());
                        } else {
                            breadcrumbs.push(segment);
                        }
                    });
                    $('#current_folder').text(breadcrumbs[breadcrumbs.length - 1]);
                    $('#breadcrumbs > ol').html('');
                    breadcrumbs.forEach(function (breadcrumb, index) {
                        var li = $('<li>').addClass('breadcrumb-item').text(breadcrumb);
                        if (index === breadcrumbs.length - 1) {
                            li.addClass('active').attr('aria-current', 'page');
                        } else {
                            li.click(function () {
                                // go to corresponding path
                                goTo('/' + validSegments.slice(0, 1 + index).join('/'));
                            });
                        }

                        $('#breadcrumbs > ol').append(li);
                    });
                    var atRootFolder = getPreviousDir() == '';
                    $('#to-previous').toggleClass('d-none invisible-lg', atRootFolder);
                    $('#show_tree').toggleClass('d-none', !atRootFolder).toggleClass('d-block', atRootFolder);
                    setOpenFolders();
                    loading(false);
                    toggleActions();
                });
    }

    function loading(show_loading) {
        $('#loading').toggleClass('d-none', !show_loading);
    }

    function createFolder(folder_name) {
        performFmRequest('newFolder', {name: folder_name})
                .done(refreshFoldersAndItems);
    }

// ==================================
// ==         File Actions         ==
// ==================================

    window.rename = function (item) {
        dialog(lang['message-rename'], item.name, function (new_name) {
            performFmRequest('rename', {
                file: item.name,
                new_name: new_name
            }).done(refreshFoldersAndItems);
        });
    }

    window.trash = function (items) {
        notify(lang['message-delete'], function () {
            performFmRequest('delete', {
                items: items.map(function (item) {
                    return item.name;
                })
            }).done(refreshFoldersAndItems)
        });
    }

    window.crop = function (item) {
        performFmRequest('crop', {img: item.name})
                .done(hideNavAndShowEditor);
    }

    window.resize = function (item) {
        performFmRequest('resize', {img: item.name})
                .done(hideNavAndShowEditor);
    }

    window.download = function (items) {
        items.forEach(function (item, index) {
            var data = defaultParameters();
            data['file'] = item.name;
            var token = getUrlParam('token');
            if (token) {
                data['token'] = token;
            }

            setTimeout(function () {
                location.href = fmRoute + '/download?' + $.param(data);
            }, index * 100);
        });
    }

    window.open = function (item) {
        goTo(item.url);
    }

    window.preview = function (items) {
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

        notify(carousel);
    }

    window.move = function (items) {

        performFmRequest('move', {items: items.map(function (item) {
                return item.name;
            })}).done(refreshFoldersAndItems);
    }

    function getUrlParam(paramName) {
        var reParam = new RegExp('(?:[\?&]|&)' + paramName + '=([^&]+)', 'i');
        var match = window.location.search.match(reParam);
        return (match && match.length > 1) ? match[1] : null;
    }

    window.use = function (items) {
        function useTinymce3(url) {
            if (!usingTinymce3()) {
                return;
            }

            var win = tinyMCEPopup.getWindowArg("window");
            win.document.getElementById(tinyMCEPopup.getWindowArg("input")).value = url;
            if (typeof (win.ImageDialog) != "undefined") {
                // Update image dimensions
                if (win.ImageDialog.getImageData) {
                    win.ImageDialog.getImageData();
                }

                // Preview if necessary
                if (win.ImageDialog.showPreviewImage) {
                    win.ImageDialog.showPreviewImage(url);
                }
            }
            tinyMCEPopup.close();
        }

        function useTinymce4AndColorbox(url) {
            if (!usingTinymce4AndColorbox()) {
                return;
            }

            parent.document.getElementById(getUrlParam('field_name')).value = url;
            if (typeof parent.tinyMCE !== "undefined") {
                parent.tinyMCE.activeEditor.windowManager.close();
            }
            if (typeof parent.$.fn.colorbox !== "undefined") {
                parent.$.fn.colorbox.close();
            }
        }

        function useCkeditor3(url) {
            if (!usingCkeditor3()) {
                return;
            }

            if (window.opener) {
                // Popup
                window.opener.CKEDITOR.tools.callFunction(getUrlParam('CKEditorFuncNum'), url);
            } else {
                // Modal (in iframe)
                parent.CKEDITOR.tools.callFunction(getUrlParam('CKEditorFuncNum'), url);
                parent.CKEDITOR.tools.callFunction(getUrlParam('CKEditorCleanUpFuncNum'));
            }
        }

        function useFckeditor2(url) {
            if (!usingFckeditor2()) {
                return;
            }

            var p = url;
            var w = data['Properties']['Width'];
            var h = data['Properties']['Height'];
            window.opener.SetUrl(p, w, h);
        }

        var url = items[0].url;

        if (typeof cresenity.fileManager !== 'undefined') {
            if (cresenity.fileManager.haveCallback('use')) {
                return cresenity.fileManager.doCallback('use', url);
            }
            
        }

        var callback = getUrlParam('callback');
        var useFileSucceeded = true;
        if (usingWysiwygEditor()) {
            useTinymce3(url);
            useTinymce4AndColorbox(url);
            useCkeditor3(url);
            useFckeditor2(url);
        } else if (callback && window[callback]) {
            window[callback](getSelectedItems());
        } else if (callback && parent[callback]) {
            parent[callback](getSelecteditems());
        } else if (window.opener) { // standalone button or other situations
            window.opener.SetUrl(getSelectedItems());
        } else {
            useFileSucceeded = false;
        }

        if (useFileSucceeded) {
            if (window.opener) {
                window.close();
            }
        } else {
            console.log('window.opener not found');
            // No editor found, open/download file using browser's default method
            window.open(url);
        }
    }
//end useFile

// ==================================
// ==     WYSIWYG Editors Check    ==
// ==================================

    function usingTinymce3() {
        return !!window.tinyMCEPopup;
    }

    function usingTinymce4AndColorbox() {
        return !!getUrlParam('field_name');
    }

    function usingCkeditor3() {
        return !!getUrlParam('CKEditor') || !!getUrlParam('CKEditorCleanUpFuncNum');
    }

    function usingFckeditor2() {
        return window.opener && typeof data != 'undefined' && data['Properties']['Width'] != '';
    }

    function usingWysiwygEditor() {
        return usingTinymce3() || usingTinymce4AndColorbox() || usingCkeditor3() || usingFckeditor2();
    }

// ==================================
// ==            Others            ==
// ==================================

    function defaultParameters() {
        return {
            working_dir: $('#working_dir').val(),
            type: $('#type').val()
        };
    }

    function notImp() {
        notify('Not yet implemented!');
    }

    function notify(body, callback) {
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

    function dialog(title, value, callback) {
        $('#dialog').find('input').val(value);
        $('#dialog').on('shown.bs.modal', function () {
            $('#dialog').find('input').focus();
        });
        $('#dialog').find('.btn-primary').unbind().click(function (e) {
            callback($('#dialog').find('input').val());
        });
        $('#dialog').modal('show').find('.modal-title').text(title);
    }


</script>
<script>
    new Dropzone("#uploadForm", {
        paramName: "upload[]", // The name that will be used to transfer the file
        uploadMultiple: false,
        parallelUploads: 5,
        clickable: '#upload-button',
        dictDefaultMessage: lang['message-drop'],
        init: function () {
            var _this = this; // For the closure
            this.on('success', function (file, response) {
                if (response == 'OK') {
                    loadFolders();
                } else {
                    if (cresenity.isJson(response)) {
                        json = JSON.parse(response);
                        this.defaultOptions.error(file, json.join('\n'));
                    } else {
                        this.defaultOptions.error(file, response);
                    }
                }
            });
        },
        headers: {
            'Authorization': 'Bearer ' + getUrlParam('token')
        },
        acceptedFiles: "<?php echo implode(',', $fm->availableMimeTypes()); ?>",
        maxFilesize: (<?php echo $fm->maxUploadSize(); ?> / 1000)
    });

    var fileManagerOptions = {
        config: config,
        lang: lang,

    };
    cresenity.fileManager = new CFileManager(fileManagerOptions);




</script>