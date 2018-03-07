window.ANSEL = window.ANSEL || {};

function runImageProcessor(F) {
    'use strict';

    if (! window.jQuery || ! F.controller || ! F.model) {
        setTimeout(function() {
            runImageProcessor(F);
        }, 10);
        return;
    }

    F.controller.make('ImageProcessor', {
        inProgress: false,

        csrfToken: null,
        uploadKey: null,
        processActionUrl: null,

        tries: {},

        init: function() {
            var self = this;
            var $anselFields = $('.JSAnselField');
            var $anselField = $anselFields.eq(0);

            self.csrfToken = $anselField.data('csrfToken');
            self.uploadKey = $anselField.data('uploadKey');
            self.processActionUrl = $anselField.data('processActionUrl');

            self.watcher();
        },

        watcher: function() {
            var self = this;
            var keys = Object.keys(F.AnselGlobalImageQueue);

            if (self.inProgress || ! keys.length) {
                setTimeout(function() {
                    self.watcher();
                }, 500);
                return;
            }

            self.inProgress = true;

            self.processImageByKey(keys[0]);

            setTimeout(function() {
                self.watcher();
            }, 500);
        },

        processImageByKey: function(key) {
            var self = this;
            var obj = F.AnselGlobalImageQueue[key];
            var ajaxData = new FormData();

            ajaxData.append('CRAFT_CSRF_TOKEN', self.csrfToken);
            ajaxData.append('anselData', obj);

            $.ajax({
                url: self.processActionUrl,
                type: 'post',
                data: ajaxData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                complete: function() {
                    self.inProgress = false;
                },
                success: function(data) {
                    delete F.AnselGlobalImageQueue[key];
                    obj.controller.processImageCallback(data);
                },
                error: function() {
                    var tries = self.tries[key] || 0;

                    delete F.AnselGlobalImageQueue[key];

                    if (tries > 3) {
                        return;
                    }

                    tries++;

                    self.tries[key] = tries;

                    F.AnselGlobalImageQueue[key] = obj;
                }
            });
        }
    });
}

runImageProcessor(window.ANSEL);
