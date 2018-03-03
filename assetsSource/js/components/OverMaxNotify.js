window.ANSEL = window.ANSEL || {};

function runMaxTracker(F) {
    'use strict';

    if (! window.jQuery || ! F.controller || ! F.model) {
        setTimeout(function() {
            runMaxTracker(F);
        }, 10);
        return;
    }

    F.controller.make('MaxTracker', {
        commonStorage: {},

        notification: null,

        model: {
            isOverMax: 'bool'
        },

        init: function() {
            var self = this;

            self.model.set('isOverMax', false);

            self.checkImageCount();

            self.commonStorage.eventTriggers.onChange('orderChange', function() {
                self.checkImageCount();
            });

            if (self.commonStorage.sharedModel.get('preventUploadOverMax')) {
                self.commonStorage.eventTriggers.onChange('imageCount', function(val) {
                    if (val >= self.commonStorage.sharedModel.get('maxQty')) {
                        self.preventUploads();
                        return;
                    }

                    self.allowUploads();
                });
            }

            self.model.onChange('isOverMax', function(val) {
                if (val) {
                    self.setOver();
                    return;
                }

                self.removeOver();
            });
        },

        checkImageCount: function() {
            var self = this;
            var $images = self.commonStorage.$el.find('.JSAnselField__Image');
            var imageCount = $images.length;
            var maxQty = self.commonStorage.sharedModel.get('maxQty');

            self.commonStorage.eventTriggers.set('imageCount', imageCount);

            if (! maxQty || imageCount <= maxQty) {
                self.model.set('isOverMax', false);
                return;
            }

            self.model.set('isOverMax', true);
        },

        setOver: function() {
            var self = this;
            var maxQty = self.commonStorage.sharedModel.get('maxQty');
            var pluralized = maxQty > 1 ? 'images' : 'image';

            if (self.notification) {
                return;
            }

            self.notification = F.controller.construct('Notification', {
                commonStorage: self.commonStorage,
                message: 'This field is limited to ' + maxQty + ' ' + pluralized + '. All images uploaded beyond that will not be displayed.'
            });
        },

        removeOver: function() {
            var self = this;

            if (! self.notification) {
                return;
            }

            self.notification.destroy();

            self.notification = null;
        },

        preventUploads: function() {
            var self = this;
            var $hideEls = self.commonStorage.$el.find('.JSAnselField__DropImagesToUpload');

            $hideEls = $hideEls.add(
                self.commonStorage.$el.find('.JSAnselField__SelectImage')
            );

            $hideEls.hide();
        },

        allowUploads: function() {
            var self = this;
            var $hideEls = self.commonStorage.$el.find('.JSAnselField__DropImagesToUpload');

            $hideEls = $hideEls.add(
                self.commonStorage.$el.find('.JSAnselField__SelectImage')
            );

            $hideEls.show();
        }
    });
}

runMaxTracker(window.ANSEL);
