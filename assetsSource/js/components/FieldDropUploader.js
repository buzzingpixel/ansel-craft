window.ANSEL = window.ANSEL || {};

function runFieldDropUploader(F) {
    'use strict';

    F.controller.make('FieldDropUploader', {
        sharedModel: null,

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
                })
                .on('dragleave dragend drop', function() {
                    $el.removeClass('AnselField--DragInProgress');
                })
                .on('drop', function(e) {
                    var files = e.originalEvent.dataTransfer.files;
                    $el.addClass('AnselField--IsUploading');
                    $.each(files, function(i, file) {
                        self.uploadFiles[F.uuid.make()] = file;
                    });
                });

            self.processUploadFilesWatcher();
        },

        processUploadFilesWatcher: function() {
            var self = this;

            if (self.uploadInProgress ||
                ! Object.keys(self.uploadFiles).length
            ) {
                setTimeout(function() {
                    self.processUploadFilesWatcher();
                }, 1000);
                return;
            }

            self.uploadInProgress = true;

            self.processUploadFile(Object.keys(self.uploadFiles)[0]);

            setTimeout(function() {
                self.processUploadFilesWatcher();
            }, 1000);
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
                    console.log('complete');
                    delete self.uploadFiles[key];
                    self.$el.removeClass('AnselField--IsUploading');
                    self.uploadInProgress = false;
                },
                success: function(data) {
                    console.log('success', data);
                },
                error: function(e) {
                    console.log('fail', e);
                }
            });
        }
    });
}

runFieldDropUploader(window.ANSEL);
