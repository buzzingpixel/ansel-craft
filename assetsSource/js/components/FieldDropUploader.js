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
        sharedModel: null,
        eventTriggers: null,
        commonObjSend: null,

        uploadFiles: {},
        uploadInProgress: false,

        init: function() {
            var self = this;
            var $el = self.$el;

            $el.on(
                'drag dragstart dragend dragover dragenter dragleave drop',
                function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                })
                .on('dragover dragenter', function() {
                    $el.addClass('AnselField--DragInProgress');
                    self.eventTriggers.set(
                        'dragStart',
                        self.eventTriggers.get('dragStart') + 1
                    );
                })
                .on('dragleave dragend drop', function() {
                    $el.removeClass('AnselField--DragInProgress');
                    self.eventTriggers.set(
                        'dragEnd',
                        self.eventTriggers.get('dragEnd') + 1
                    );
                })
                .on('drop', function(e) {
                    var files = e.originalEvent.dataTransfer.files;
                    $el.addClass('AnselField--IsUploading');
                    self.eventTriggers.set(
                        'drop',
                        self.eventTriggers.get('drop') + 1
                    );
                    $.each(files, function(i, file) {
                        self.uploadFiles[F.uuid.make()] = file;
                    });
                });

            self.processUploadFilesWatcher();
        },

        processUploadFilesWatcher: function() {
            var self = this;

            if (self.$el.hasClass('AnselField--IsUploading') &&
                ! Object.keys(self.uploadFiles).length
            ) {
                self.$el.removeClass('AnselField--IsUploading');
                self.eventTriggers.set(
                    'uploadComplete',
                    self.eventTriggers.get('uploadComplete') + 1
                );
            }

            if (self.uploadInProgress ||
                ! Object.keys(self.uploadFiles).length
            ) {
                setTimeout(function() {
                    self.processUploadFilesWatcher();
                }, 500);
                return;
            }

            self.eventTriggers.set(
                'uploadStart',
                self.eventTriggers.get('uploadStart') + 1
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

            ajaxData.append('CRAFT_CSRF_TOKEN', self.sharedModel.get('csrfToken'));
            ajaxData.append('uploadKey', self.sharedModel.get('uploadKey'));
            ajaxData.append('file', file, file.name);
            ajaxData.append('minWidth', self.sharedModel.get('minWidth'));
            ajaxData.append('minHeight', self.sharedModel.get('minHeight'));

            $.ajax({
                url: self.sharedModel.get('uploadActionUrl'),
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
                            el: self.$el,
                            eventTriggers: self.eventTriggers,
                            error: true,
                            heading: file.name,
                            message: data.message,
                            destroyEvents: ['dragStart']
                        });

                        return;
                    }

                    console.log('success');
                },
                error: function() {
                    F.controller.construct('Notification', {
                        el: self.$el,
                        eventTriggers: self.eventTriggers,
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
