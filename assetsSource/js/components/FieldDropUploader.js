window.ANSEL = window.ANSEL || {};

function runFieldDropUploader(F) {
    'use strict';

    if (! window.jQuery || ! F.controller || ! F.model) {
        setTimeout(function() {
            runFieldDropUploader(F);
        }, 10);
        return;
    }

    F.controller.make('FieldDropUploader', {
        commonStorage: {},
        uploadFiles: {},
        uploadInProgress: false,

        init: function() {
            var self = this;
            var $el = self.commonStorage.$el;

            $el.on(
                'drag dragstart dragend dragover dragenter dragleave drop',
                function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                })
                .on('dragover dragenter', function() {
                    $el.addClass('AnselField--DragInProgress');
                    self.commonStorage.eventTriggers.set(
                        'dragStart',
                        self.commonStorage.eventTriggers.get('dragStart') + 1
                    );
                })
                .on('dragleave dragend drop', function() {
                    $el.removeClass('AnselField--DragInProgress');
                    self.commonStorage.eventTriggers.set(
                        'dragEnd',
                        self.commonStorage.eventTriggers.get('dragEnd') + 1
                    );
                })
                .on('drop', function(e) {
                    var files = e.originalEvent.dataTransfer.files;
                    $el.addClass('AnselField--IsUploading');
                    self.commonStorage.eventTriggers.set(
                        'drop',
                        self.commonStorage.eventTriggers.get('drop') + 1
                    );
                    $.each(files, function(i, file) {
                        self.uploadFiles[F.uuid.make()] = file;
                    });
                });

            self.processUploadFilesWatcher();
        },

        processUploadFilesWatcher: function() {
            var self = this;
            var maxQty = self.commonStorage.sharedModel.get('maxQty');
            var pluralized = maxQty > 1 ? 'images' : 'image';
            var imageCount = self.commonStorage.eventTriggers.get('imageControllerUuids').length;
            var numFilesWaiting = Object.keys(self.uploadFiles).length;

            F.imagesBeingUploaded = numFilesWaiting;

            if (self.commonStorage.$el.hasClass('AnselField--IsUploading') &&
                ! numFilesWaiting
            ) {
                self.commonStorage.$el.removeClass('AnselField--IsUploading');
                self.commonStorage.eventTriggers.set(
                    'uploadComplete',
                    self.commonStorage.eventTriggers.get('uploadComplete') + 1
                );
            }

            if (numFilesWaiting &&
                self.commonStorage.sharedModel.get('preventUploadOverMax') &&
                imageCount >= maxQty
            ) {
                F.controller.construct('Notification', {
                    commonStorage: self.commonStorage,
                    message: 'This field is limited to ' + maxQty + ' ' + pluralized + ' and does not allow image uploads beyond that quantity',
                    destroyEvents: ['dragStart', 'imageControllerUuids']
                });

                self.uploadFiles = {};
            }

            if (self.uploadInProgress ||
                ! numFilesWaiting
            ) {
                setTimeout(function() {
                    self.processUploadFilesWatcher();
                }, 500);
                return;
            }

            self.commonStorage.eventTriggers.set(
                'uploadStart',
                self.commonStorage.eventTriggers.get('uploadStart') + 1
            );

            self.uploadInProgress = true;

            self.processUploadFile(Object.keys(self.uploadFiles)[0]);

            setTimeout(function() {
                self.processUploadFilesWatcher();
            }, 500);
        },

        processUploadFile: function(key) {
            var self = this;
            var file = self.uploadFiles[key];
            var ajaxData = new FormData();

            ajaxData.append(
                'CRAFT_CSRF_TOKEN',
                self.commonStorage.sharedModel.get('csrfToken')
            );

            ajaxData.append(
                'uploadKey',
                self.commonStorage.sharedModel.get('uploadKey')
            );

            ajaxData.append('file', file, file.name);

            ajaxData.append(
                'minWidth',
                self.commonStorage.sharedModel.get('minWidth')
            );

            ajaxData.append(
                'minHeight',
                self.commonStorage.sharedModel.get('minHeight')
            );

            $.ajax({
                url: self.commonStorage.sharedModel.get('uploadActionUrl'),
                type: 'post',
                data: ajaxData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                complete: function() {
                    delete self.uploadFiles[key];
                    self.uploadInProgress = false;
                },
                success: function(data) {
                    if (! data.success) {
                        F.controller.construct('Notification', {
                            commonStorage: self.commonStorage,
                            error: true,
                            heading: file.name,
                            message: data.message,
                            destroyEvents: ['dragStart']
                        });

                        return;
                    }

                    F.controller.construct('Image', {
                        commonStorage: self.commonStorage,
                        base64Image: data.file.base64,
                        cacheFile: data.file.cacheFile,
                        fileName: file.name
                    });
                },
                error: function() {
                    F.controller.construct('Notification', {
                        commonStorage: self.commonStorage,
                        error: true,
                        heading: file.name,
                        message: 'An unknown error occurred while uploading this file',
                        destroyEvents: ['dragStart']
                    });
                }
            });
        }
    });
}

runFieldDropUploader(window.ANSEL);
