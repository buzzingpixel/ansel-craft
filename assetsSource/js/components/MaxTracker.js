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
            isOverMax: 'bool',
            isAtMax: 'bool'
        },

        init: function() {
            var self = this;

            self.model.onChange('isOverMax', function(val) {
                val ? self.setOver() : self.removeOver();
            });

            if (self.commonStorage.sharedModel.get('preventUploadOverMax')) {
                self.model.onChange('isAtMax', function(val) {
                    val ? self.preventUploads() : self.allowUploads();
                });
            }

            self.checkImageCount();

            self.commonStorage.eventTriggers.onChange('imageControllerUuids', function() {
                self.checkImageCount();
            });
        },

        checkImageCount: function() {
            var self = this;
            var imageCount = self.commonStorage.eventTriggers.get('imageControllerUuids').length;
            var maxQty = self.commonStorage.sharedModel.get('maxQty');

            self.model.set('isAtMax', maxQty && imageCount >= maxQty);
            self.model.set('isOverMax', maxQty && imageCount > maxQty);
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
