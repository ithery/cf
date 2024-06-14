<?php

/**
 * Description of Summernote.
 *
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jan 28, 2018, 9:43:02 PM
 */
class CElement_FormInput_Textarea_Summernote extends CElement_FormInput_Textarea {
    protected $toolbarType = 'default';

    protected $customToolbarJson = '[]';

    protected $haveDragDrop = false;

    protected $uploadUrl;

    protected $sanitizePaste = false;

    public function __construct($id) {
        parent::__construct($id);
        CManager::registerModule('summernote');
    }

    public function build() {
        $this->addClass('summernote-control');
    }

    public function setToolbarType($toolbarType) {
        $this->toolbarType = $toolbarType;

        return $this;
    }

    public function setCustomToolbarJson($json) {
        $this->customToolbarJson = $json;

        return $this;
    }

    public function setDragDrop($bool = true) {
        $this->haveDragDrop = $bool;

        return $this;
    }

    public function setUploadUrl($url) {
        $this->uploadUrl = $url;

        return $this;
    }

    public function setSanitizePaste($bool = true) {
        $this->sanitizePaste = $bool;

        return $this;
    }

    protected function getToolbarJson($toolbarType = null) {
        if ($toolbarType == null) {
            $toolbarType = $this->toolbarType;
        }
        $json = '[]';
        switch ($toolbarType) {
            case 'standard':
                $json = "
                    [
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['font', ['strikethrough', 'superscript', 'subscript']],
                        ['fontsize', ['fontsize']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['height', ['height']]
                    ]
                ";

                break;
            case 'non-video':
                $json = "
                    [
                        ['fontstyle', ['style']],
                        ['style', ['bold', 'underline', 'clear']],
                        ['fontfamily', ['fontname']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['table', ['table']],
                        ['media', ['link', 'picture']],
                        ['misc', ['fullscreen', 'codeview', 'help']]
                    ]
                ";

                break;
            case 'link-only':
                $json = "
                    [
                        ['media', ['link']],
                    ]
                ";

                break;
            case 'text-only':
                $json = "
                    [
                        ['fontstyle', ['style']],
                        ['style', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
                        ['font', ['fontname', 'fontsize']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['link', ['link']],
                        ['height', ['height']]
                    ]
                ";

                break;
            case 'text-media':
                $json = "
                        [
                            ['fontstyle', ['style']],
                            ['style', ['bold', 'italic', 'underline', 'clear']],
                            ['font', ['strikethrough', 'superscript', 'subscript']],
                            ['fontsize', ['fontname', 'fontsize']],
                            ['color', ['color']],
                            ['para', ['ul', 'ol', 'paragraph']],
                            ['media', ['link', 'picture']],
                            ['height', ['height']],
                            ['others', ['codeview']]
                        ]
                    ";

                break;
            case 'text':
                $json = "
                    [
                        ['style', ['bold', 'italic', 'underline']],
                    ]
                ";

                break;
            case 'text-link-para':
                $json = "
                    [
                        ['style', ['bold', 'italic', 'underline']],
                        ['para', ['paragraph']],
                        ['link', ['link']],
                    ]
                ";
                // no break
            case 'text-link':
                $json = "
                    [
                        ['style', ['bold', 'italic', 'underline']],
                        ['link', ['link']],
                    ]
                ";

                break;
            case 'custom':
                $json = $this->customToolbarJson;

                break;
        }

        return $json;
    }

    public function js($indent = 0) {
        $placeholder = '';
        if ($this->placeholder) {
            $placeholder = 'placeholder: "' . $this->placeholder . '",';
        }

        $additionalOptions = 'disableDragAndDrop: true,';
        if ($this->haveDragDrop) {
            $additionalOptions = 'disableDragAndDrop: false,';
        }
        if ($this->toolbarType != 'default') {
            $additionalOptions .= 'toolbar:' . $this->getToolbarJson() . ',';
        }

        $additionalCallbackOptions = '';
        if ($this->uploadUrl) {
            $additionalCallbackOptions = "
                onImageUpload: function(files) {
                    var uploadUrl = '" . $this->uploadUrl . "';

                    var data = new FormData();
                    $.each(files, function(i, file) {
                        data.append('files[]', file, file.name);
                    });

                    $.ajax(uploadUrl, {
                        data: data,
                        cache: false,
                        contentType: false,
                        processData: false,
                        dataType: 'json',
                        type: 'POST',
                        success: function(data) {
                            if (! data.errCode) {
                                $.each(data.data, function(i, val) {
                                    var imgNode = $('<img/>');
                                    imgNode.attr('src', val.url);
                                    imgNode.attr('alt', val.name);
                                    $('#" . $this->id . "').summernote('insertNode', imgNode[0]);
                                });
                            } else {
                                alert('Oops, something went wrong when uploading image');
                            }
                        }
                    });
                },
            ";
        }

        if ($this->sanitizePaste) {
            $additionalCallbackOptions .= "
            onPaste: function (e) {
                var bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('Text');
                e.preventDefault();
                document.execCommand('insertText', false, bufferText);
            },
            ";
        }

        $js = '';
        $js .= "

        $('#" . $this->id . "').summernote({
            height: '300px',
            codeviewFilter: true,
			codeviewIframeFilter: true,
            " . $placeholder . '
            // shortcuts: false,
            ' . $additionalOptions . "
            maximumImageFileSize:1024*1024, // 1 MB
            onCreateLink: function(originalLink) {
                return originalLink; // return originalLink
            },
            callbacks: {
                onInit: function (e, layoutInfo) {
                    // to prevent error in validation.js in cres.js, to be able to get form input name
                    if (e.editable[0]) {
                        e.editable[0].setAttribute('name', '" . $this->name . "');
                    }
                },
                " . $additionalCallbackOptions . "
                onImageUploadError: function(msg){
                    alert('Oops, something went wrong with image url');
                }
            }
        });
        ";

        $js .= parent::js();

        return $js;
    }
}
