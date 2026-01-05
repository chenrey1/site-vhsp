(function ($) {
    'use strict';

    var defaultOptions = {
        serverPath: '',
        fileFieldName: 'fileToUpload',
        data: [],                       // Additional data for ajax [{name: 'key', value: 'value'}]
        headers: {},                    // Additional headers
        xhrFields: {},                  // Additional fields
        urlPropertyName: 'file',        // How to get url from the json response (for instance 'url' for {url: ....})
        statusPropertyName: 'success',  // How to get status from the json response
        success: undefined,             // Success callback: function (data, trumbowyg, $modal, values) {}
        error: undefined,               // Error callback: function () {}
        imageWidthModalEdit: false      // Add ability to edit image width
    };

    function getDeep(object, propertyParts) {
        var mainProperty = propertyParts.shift(),
            otherProperties = propertyParts;

        if (object !== null) {
            if (otherProperties.length === 0) {
                return object[mainProperty];
            }

            if (typeof object === 'object') {
                return getDeep(object[mainProperty], otherProperties);
            }
        }
        return object;
    }

    addXhrProgressEvent();

    $.extend(true, $.trumbowyg, {
        plugins: {
            pasteUploadImage: {
                init: function (trumbowyg) {
                    trumbowyg.o.plugins.pasteUploadImage = $.extend(true, {}, defaultOptions, trumbowyg.o.plugins.pasteUploadImage || {});

                    var isUploading = false;
                    var prefix = trumbowyg.o.prefix;

                    trumbowyg.pasteHandlers.push(function (pasteEvent) {
                        try {
                            var event = (pasteEvent.originalEvent || pasteEvent);
                            var items = event.clipboardData.items,
                                mustPreventDefault = false;
                            var html = event.clipboardData.getData('text/html') || "";
                            console.log(html);
                            var parsed = new DOMParser().parseFromString(html, 'text/html');
                            var img = parsed.querySelector('img');

                            if(!!img) { 
                                if (!img.src.startsWith("blob") && !img.src.startsWith("data:")) return;
                            }

                            for (var i = items.length - 1; i >= 0; i -= 1) {
                                if (items[i].type.match(/^image\//)) {
                                    if (isUploading) {
                                        return;
                                    }
                                    isUploading = true;

                                    var data = new FormData();
                                    data.append(trumbowyg.o.plugins.pasteUploadImage.fileFieldName, items[i].getAsFile());

                                    trumbowyg.o.plugins.pasteUploadImage.data.map(function (cur) {
                                        data.append(cur.name, cur.value);
                                    });

                                    if ($('.' + prefix + 'progress', $('.' + prefix + 'box')).length === 0) {
                                        $('.' + prefix + 'box .' + prefix + 'button-pane')
                                            .after(
                                                $('<div/>', {
                                                    'class': prefix + 'progress',
                                                    'style': 'width: 100%;height: 3px;position: absolute;z-index: 9999999;'
                                                }).append(
                                                    $('<div/>', {
                                                        'class': prefix + 'progress-bar',
                                                        'style': 'background: rgb(43, 192, 106); width: 0%; height: 100%; transition: width 150ms linear 0s;'
                                                    })
                                                )
                                            );
                                    }

                                    $.ajax({
                                        url: trumbowyg.o.plugins.pasteUploadImage.serverPath,
                                        headers: trumbowyg.o.plugins.pasteUploadImage.headers,
                                        xhrFields: trumbowyg.o.plugins.pasteUploadImage.xhrFields,
                                        type: 'POST',
                                        data: data,
                                        cache: false,
                                        dataType: 'json',
                                        processData: false,
                                        contentType: false,

                                        progressUpload: function (e) {
                                            $('.' + prefix + 'progress-bar').css('width', Math.round(e.loaded * 100 / e.total) + '%');
                                        },

                                        success: function (data) {
                                            if (trumbowyg.o.plugins.pasteUploadImage.success) {
                                                trumbowyg.o.plugins.pasteUploadImage.success(data, trumbowyg, values);
                                            } else {
                                                if (!!getDeep(data, trumbowyg.o.plugins.pasteUploadImage.statusPropertyName.split('.'))) {
                                                    var url = getDeep(data, trumbowyg.o.plugins.pasteUploadImage.urlPropertyName.split('.'));
                                                    trumbowyg.execCmd('insertImage', url, false, true);
                                                    var $img = $('img[src="' + url + '"]:not([alt])', trumbowyg.$box);
                                                    trumbowyg.$c.trigger('tbwuploadsuccess', [trumbowyg, data, url]);
                                                } else {
                                                    $('.' + prefix + 'progress-bar').css('background', 'rgb(193, 63, 71)');
                                                    trumbowyg.$c.trigger('tbwuploaderror', [trumbowyg, data]);
                                                }
                                            }

                                            setTimeout(function () {
                                                $('.' + prefix + 'progress', $('.' + prefix + 'box')).remove();
                                            }, 2500);

                                            isUploading = false;
                                        },

                                        error: trumbowyg.o.plugins.pasteUploadImage.error || function () {
                                            $('.' + prefix + 'progress-bar').css('background', 'rgb(193, 63, 71)');
                                            trumbowyg.$c.trigger('tbwuploaderror', [trumbowyg]);

                                            isUploading = false;
                                        }
                                    });

                                    mustPreventDefault = true;
                                }
                            }

                            if (mustPreventDefault) {
                                pasteEvent.stopPropagation();
                                pasteEvent.preventDefault();
                            }
                        } catch (c) {
                        }
                    });
                }
            }
        }
    });

    function addXhrProgressEvent() {
        if (!$.trumbowyg.addedXhrProgressEvent) {   // Avoid adding progress event multiple times
            var originalXhr = $.ajaxSettings.xhr;
            $.ajaxSetup({
                xhr: function () {
                    var that = this,
                        req = originalXhr();

                    if (req && typeof req.upload === 'object' && that.progressUpload !== undefined) {
                        req.upload.addEventListener('progress', function (e) {
                            that.progressUpload(e);
                        }, false);
                    }

                    return req;
                }
            });
            $.trumbowyg.addedXhrProgressEvent = true;
        }
    }
})(jQuery);
