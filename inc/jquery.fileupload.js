

(function ($) {

    var defaultNamespace = 'file_upload',
        undef = 'undefined',
        func = 'function',
        num = 'number',
        FileUpload,
        methods;
        
    FileUpload = function (container) {
        var fileUpload = this,
            uploadForm = (container.is('form') ? container : container.find('form').first()),
            fileInput = uploadForm.find('input:file').first(),
            settings = {
                namespace: defaultNamespace,
                cssClass: defaultNamespace,
                dragDropSupport: true,
                dropZone: container,
                url: uploadForm.attr('action'),
                method: uploadForm.attr('method'),
                fieldName: fileInput.attr('name'),
                multipart: true,
                multiFileRequest: false,
                formData: function () {
                    return uploadForm.serializeArray();
                },
                withCredentials: false,
                forceIframeUpload: false
            },
            documentListeners = {},
            dropZoneListeners = {},
            protocolRegExp = /^http(s)?:\/\//,
            optionsReference,

            MultiLoader = function (callBack, numberComplete) {
                var loaded = 0;
                this.complete = function () {
                    loaded += 1;
                    if (loaded === numberComplete) {
                        callBack();
                    }
                };
            },

            isXHRUploadCapable = function () {
                return typeof XMLHttpRequest !== undef && typeof File !== undef && (
                    !settings.multipart || typeof FormData !== undef || typeof FileReader !== undef
                );
            },

            initEventHandlers = function () {
                if (settings.dragDropSupport) {
                    if (typeof settings.onDocumentDragEnter === func) {
                        documentListeners['dragenter.' + settings.namespace] = settings.onDocumentDragEnter;
                    }
                    if (typeof settings.onDocumentDragLeave === func) {
                        documentListeners['dragleave.' + settings.namespace] = settings.onDocumentDragLeave;
                    }
                    documentListeners['dragover.'   + settings.namespace] = fileUpload.onDocumentDragOver;
                    documentListeners['drop.'       + settings.namespace] = fileUpload.onDocumentDrop;
                    $(document).bind(documentListeners);
                    if (typeof settings.onDragEnter === func) {
                        dropZoneListeners['dragenter.' + settings.namespace] = settings.onDragEnter;
                    }
                    if (typeof settings.onDragLeave === func) {
                        dropZoneListeners['dragleave.' + settings.namespace] = settings.onDragLeave;
                    }
                    dropZoneListeners['dragover.'   + settings.namespace] = fileUpload.onDragOver;
                    dropZoneListeners['drop.'       + settings.namespace] = fileUpload.onDrop;
                    settings.dropZone.bind(dropZoneListeners);
                }
                fileInput.bind('change.' + settings.namespace, fileUpload.onChange);
            },

            removeEventHandlers = function () {
                $.each(documentListeners, function (key, value) {
                    $(document).unbind(key, value);
                });
                $.each(dropZoneListeners, function (key, value) {
                    settings.dropZone.unbind(key, value);
                });
                fileInput.unbind('change.' + settings.namespace);
            },

            initUploadEventHandlers = function (files, index, xhr, settings) {
                if (typeof settings.onProgress === func) {
                    xhr.upload.onprogress = function (e) {
                        settings.onProgress(e, files, index, xhr, settings);
                    };
                }
                if (typeof settings.onLoad === func) {
                    xhr.onload = function (e) {
                        settings.onLoad(e, files, index, xhr, settings);
                    };
                }
                if (typeof settings.onAbort === func) {
                    xhr.onabort = function (e) {
                        settings.onAbort(e, files, index, xhr, settings);
                    };
                }
                if (typeof settings.onError === func) {
                    xhr.onerror = function (e) {
                        settings.onError(e, files, index, xhr, settings);
                    };
                }
            },

            getFormData = function (settings) {
                if (typeof settings.formData === func) {
                    return settings.formData();
                } else if ($.isArray(settings.formData)) {
                    return settings.formData;
                } else if (settings.formData) {
                    var formData = [];
                    $.each(settings.formData, function (name, value) {
                        formData.push({name: name, value: value});
                    });
                    return formData;
                }
                return [];
            },

            isSameDomain = function (url) {
                if (protocolRegExp.test(url)) {
                    var host = location.host,
                        indexStart = location.protocol.length + 2,
                        index = url.indexOf(host, indexStart),
                        pathIndex = index + host.length;
                    if ((index === indexStart || index === url.indexOf('@', indexStart) + 1) &&
                            (url.length === pathIndex || $.inArray(url.charAt(pathIndex), ['/', '?', '#']) !== -1)) {
                        return true;
                    }
                    return false;
                }
                return true;
            },

            nonMultipartUpload = function (file, xhr, sameDomain) {
                if (sameDomain) {
                    xhr.setRequestHeader('X-File-Name', unescape(encodeURIComponent(file.name)));
                }
                xhr.setRequestHeader('Content-Type', file.type);
                xhr.send(file);
            },

            formDataUpload = function (files, xhr, settings) {
                var formData = new FormData(),
                    i;
                $.each(getFormData(settings), function (index, field) {
                    formData.append(field.name, field.value);
                });
                for (i = 0; i < files.length; i += 1) {
                    formData.append(settings.fieldName, files[i]);
                }
                xhr.send(formData);
            },

            loadFileContent = function (file, callBack) {
                var fileReader = new FileReader();
                fileReader.onload = function (e) {
                    file.content = e.target.result;
                    callBack();
                };
                fileReader.readAsBinaryString(file);
            },

            buildMultiPartFormData = function (boundary, files, fields) {
                var doubleDash = '--',
                    crlf     = '\r\n',
                    formData = '';
                $.each(fields, function (index, field) {
                    formData += doubleDash + boundary + crlf +
                        'Content-Disposition: form-data; name="' +
                        unescape(encodeURIComponent(field.name)) +
                        '"' + crlf + crlf +
                        unescape(encodeURIComponent(field.value)) + crlf;
                });
                $.each(files, function (index, file) {
                    formData += doubleDash + boundary + crlf +
                        'Content-Disposition: form-data; name="' +
                        unescape(encodeURIComponent(settings.fieldName)) +
                        '"; filename="' + unescape(encodeURIComponent(file.name)) + '"' + crlf +
                        'Content-Type: ' + file.type + crlf + crlf +
                        file.content + crlf;
                });
                formData += doubleDash + boundary + doubleDash + crlf;
                return formData;
            },
            
            fileReaderUpload = function (files, xhr, settings) {
                var boundary = '----MultiPartFormBoundary' + (new Date()).getTime(),
                    loader,
                    i;
                xhr.setRequestHeader('Content-Type', 'multipart/form-data; boundary=' + boundary);
                loader = new MultiLoader(function () {
                    xhr.sendAsBinary(buildMultiPartFormData(
                        boundary,
                        files,
                        getFormData(settings)
                    ));
                }, files.length);
                for (i = 0; i < files.length; i += 1) {
                    loadFileContent(files[i], loader.complete);
                }
            },

            upload = function (files, index, xhr, settings) {
                var sameDomain = isSameDomain(settings.url),
                    filesToUpload;
                initUploadEventHandlers(files, index, xhr, settings);
                xhr.open(settings.method, settings.url, true);
                if (sameDomain) {
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                } else if (settings.withCredentials) {
                    xhr.withCredentials = true;
                }
                if (!settings.multipart) {
                    nonMultipartUpload(files[index], xhr, sameDomain);
                } else {
                    if (typeof index === num) {
                        filesToUpload = [files[index]];
                    } else {
                        filesToUpload = files;
                    }
                    if (typeof FormData !== undef) {
                        formDataUpload(filesToUpload, xhr, settings);
                    } else if (typeof FileReader !== undef) {
                        fileReaderUpload(filesToUpload, xhr, settings);
                    } else {
                        $.error('Browser does neither support FormData nor FileReader interface');
                    }
                }
            },

            handleUpload = function (event, files, index) {
                var xhr = new XMLHttpRequest(),
                    uploadSettings = $.extend({}, settings);
                if (typeof settings.initUpload === func) {
                    settings.initUpload(
                        event,
                        files,
                        index,
                        xhr,
                        uploadSettings,
                        function () {
                            upload(files, index, xhr, uploadSettings);
                        }
                    );
                } else {
                    upload(files, index, xhr, uploadSettings);
                }
            },

            handleFiles = function (event, files) {
                var i;
                if (settings.multiFileRequest) {
                    handleUpload(event, files);
                } else {
                    for (i = 0; i < files.length; i += 1) {
                        handleUpload(event, files, i);
                    }
                }
            },

            legacyUploadFormDataInit = function (input, settings) {
                var formData = getFormData(settings);
                uploadForm.find(':input').not(':disabled')
                    .attr('disabled', true)
                    .addClass(settings.namespace + '_disabled');
                $.each(formData, function (index, field) {
                    $('<input type="hidden"/>')
                        .attr('name', field.name)
                        .val(field.value)
                        .addClass(settings.namespace + '_form_data')
                        .insertBefore(fileInput);
                });
                input.insertAfter(fileInput);
            },

            legacyUploadFormDataReset = function (input, settings) {
                input.remove();
                uploadForm.find('.' + settings.namespace + '_disabled')
                    .removeAttr('disabled')
                    .removeClass(settings.namespace + '_disabled');
                uploadForm.find('.' + settings.namespace + '_form_data').remove();
            },

            legacyUpload = function (input, iframe, settings) {
                iframe
                    .unbind('abort')
                    .bind('abort', function (e) {
                        iframe.readyState = 0;
                        // javascript:false as iframe src prevents warning popups on HTTPS in IE6
                        // concat is used here to prevent the "Script URL" JSLint error:
                        iframe.unbind('load').attr('src', 'javascript'.concat(':false;'));
                        if (typeof settings.onAbort === func) {
                            settings.onAbort(e, [{name: input.val(), type: null, size: null}], 0, iframe, settings);
                        }
                    })
                    .unbind('load')
                    .bind('load', function (e) {
                        iframe.readyState = 4;
                        if (typeof settings.onLoad === func) {
                            settings.onLoad(e, [{name: input.val(), type: null, size: null}], 0, iframe, settings);
                        }
                    });
                uploadForm
                    .attr('action', settings.url)
                    .attr('target', iframe.attr('name'));
                legacyUploadFormDataInit(input, settings);
                iframe.readyState = 2;
                uploadForm.get(0).submit();
                legacyUploadFormDataReset(input, settings);
            },

            handleLegacyUpload = function (event, input) {
                // javascript:false as iframe src prevents warning popups on HTTPS in IE6:
                var iframe = $('<iframe src="javascript:false;" style="display:none" name="iframe_' +
                    settings.namespace + '_' + (new Date()).getTime() + '"></iframe>'),
                    uploadSettings = $.extend({}, settings);
                iframe.readyState = 0;
                iframe.abort = function () {
                    iframe.trigger('abort');
                };
                iframe.bind('load', function () {
                    iframe.unbind('load');
                    if (typeof settings.initUpload === func) {
                        settings.initUpload(
                            event,
                            [{name: input.val(), type: null, size: null}],
                            0,
                            iframe,
                            uploadSettings,
                            function () {
                                legacyUpload(input, iframe, uploadSettings);
                            }
                        );
                    } else {
                        legacyUpload(input, iframe, uploadSettings);
                    }
                }).appendTo(uploadForm);
            },

            resetFileInput = function () {
                fileInput.unbind('change.' + settings.namespace);
                var inputClone = fileInput.clone(true);
                $('<form/>').append(inputClone).get(0).reset();
                fileInput.replaceWith(inputClone);
                fileInput = inputClone;
                fileInput.bind('change.' + settings.namespace, fileUpload.onChange);
            };

        this.onDocumentDragOver = function (e) {
            if (typeof settings.onDocumentDragOver === func &&
                    settings.onDocumentDragOver(e) === false) {
                return false;
            }
            e.preventDefault();
        };
        
        this.onDocumentDrop = function (e) {
            if (typeof settings.onDocumentDrop === func &&
                    settings.onDocumentDrop(e) === false) {
                return false;
            }
            e.preventDefault();
        };

        this.onDragOver = function (e) {
            if (typeof settings.onDragOver === func &&
                    settings.onDragOver(e) === false) {
                return false;
            }
            var dataTransfer = e.originalEvent.dataTransfer;
            if (dataTransfer) {
                dataTransfer.dropEffect = dataTransfer.effectAllowed = 'copy';
            }
            e.preventDefault();
        };

        this.onDrop = function (e) {
            if (typeof settings.onDrop === func &&
                    settings.onDrop(e) === false) {
                return false;
            }
            var dataTransfer = e.originalEvent.dataTransfer;
            if (dataTransfer && dataTransfer.files && isXHRUploadCapable()) {
                handleFiles(e, dataTransfer.files);
            }
            e.preventDefault();
        };
        
        this.onChange = function (e) {
            if (typeof settings.onChange === func &&
                    settings.onChange(e) === false) {
                return false;
            }
            if (!settings.forceIframeUpload && e.target.files && isXHRUploadCapable()) {
                handleFiles(e, e.target.files);
            } else {
                handleLegacyUpload(e, $(e.target));
            }
            resetFileInput();
        };

        this.init = function (options) {
            if (options) {
                $.extend(settings, options);
                optionsReference = options;
            }
            if (container.data(settings.namespace)) {
                $.error('FileUpload with namespace "' + settings.namespace + '" already assigned to this element');
                return;
            }
            container
                .data(settings.namespace, fileUpload)
                .addClass(settings.cssClass);
            settings.dropZone.not(container).addClass(settings.cssClass);
            initEventHandlers();
        };

        this.options = function (options) {
            var oldCssClass,
                oldDropZone;
            if (typeof options === undef) {
                return $.extend({}, settings);
            }
            if (optionsReference) {
                $.extend(optionsReference, options);
            }
            removeEventHandlers();
            $.each(options, function (name, value) {
                switch (name) {
                case 'namespace':
                    $.error('The FileUpload namespace cannot be updated.');
                    return;
                case 'cssClass':
                    oldCssClass = settings.cssClass;
                    break;
                case 'dropZone':
                    oldDropZone = settings.dropZone;
                    break;
                }
                settings[name] = value;
            });
            if (typeof oldCssClass !== undef) {
                container
                    .removeClass(oldCssClass)
                    .addClass(settings.cssClass);
                (oldDropZone ? oldDropZone : settings.dropZone).not(container)
                    .removeClass(oldCssClass);
                settings.dropZone.not(container).addClass(settings.cssClass);
            } else if (oldDropZone) {
                oldDropZone.not(container).removeClass(settings.cssClass);
                settings.dropZone.not(container).addClass(settings.cssClass);
            }
            initEventHandlers();
        };
        
        this.option = function (name, value) {
            var options;
            if (typeof value === undef) {
                return settings[name];
            }
            options = {};
            options[name] = value;
            fileUpload.options(options);
        };
        
        this.destroy = function () {
            removeEventHandlers();
            container
                .removeData(settings.namespace)
                .removeClass(settings.cssClass);
            settings.dropZone.not(container).removeClass(settings.cssClass);
        };
    };

    methods = {
        init : function (options) {
            return this.each(function () {
                (new FileUpload($(this))).init(options);
            });
        },
        
        option: function (option, value, namespace) {
            namespace = namespace ? namespace : defaultNamespace;
            var fileUpload = $(this).data(namespace);
            if (fileUpload) {
                if (typeof option === 'string') {
                    return fileUpload.option(option, value);
                }
                return fileUpload.options(option);
            } else {
                $.error('No FileUpload with namespace "' + namespace + '" assigned to this element');
            }
        },
                
        destroy : function (namespace) {
            namespace = namespace ? namespace : defaultNamespace;
            return this.each(function () {
                var fileUpload = $(this).data(namespace);
                if (fileUpload) {
                    fileUpload.destroy();
                } else {
                    $.error('No FileUpload with namespace "' + namespace + '" assigned to this element');
                }
            });

        }
    };
    
    $.fn.fileUpload = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.fileUpload');
        }
    };
    
}(jQuery));