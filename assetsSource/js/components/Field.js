window.ANSEL = window.ANSEL || {};

function runField(F) {
    'use strict';

    if (! window.jQuery || ! F.controller || ! F.model) {
        setTimeout(function() {
            runField(F);
        }, 10);
        return;
    }

    F.controller.make('Field', {
        uploadKey: '',
        uploadActionUrl: '',
        csrfToken: '',
        uploadFiles: {},
        uploadInProgress: false,

        init: function() {
            var self = this;
            var $el = self.$el;

            self.uploadKey = self.$el.data('uploadKey');
            self.uploadActionUrl = self.$el.data('uploadActionUrl');
            self.csrfToken = self.$el.data('csrfToken');

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

            console.log('here');

            setTimeout(function() {
                self.processUploadFilesWatcher();
            }, 1000);
        },

        processUploadFile: function(key) {
            var self = this;
            var file = self.uploadFiles[key];
            var ajaxData = new FormData();

            ajaxData.append('CRAFT_CSRF_TOKEN', self.csrfToken);
            ajaxData.append('uploadKey', self.uploadKey);
            ajaxData.append('file', file, file.name);

            $.ajax({
                url: self.uploadActionUrl,
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

runField(window.ANSEL);
