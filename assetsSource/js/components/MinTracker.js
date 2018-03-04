window.ANSEL = window.ANSEL || {};

function runMinTracker(F) {
    'use strict';

    if (! window.jQuery || ! F.controller || ! F.model) {
        setTimeout(function() {
            runMinTracker(F);
        }, 10);
        return;
    }

    F.controller.make('MinTracker', {
        commonStorage: {},

        notification: null,

        model: {
            isUnderMin: 'bool'
        },

        init: function() {
            var self = this;

            self.model.onChange('isUnderMin', function(val) {
                val ? self.setUnder() : self.removeUnder();
            });

            self.checkImageCount();

            self.commonStorage.eventTriggers.onChange('imageControllerUuids', function() {
                self.checkImageCount();
            });
        },

        checkImageCount: function() {
            var self = this;
            var imageCount = self.commonStorage.eventTriggers.get('imageControllerUuids').length;
            var minQty = self.commonStorage.sharedModel.get('minQty');

            if (! minQty || imageCount >= minQty) {
                self.model.set('isUnderMin', false);
                return;
            }

            self.model.set('isUnderMin', true);
        },

        setUnder: function() {
            var self = this;
            var minQty = self.commonStorage.sharedModel.get('minQty');
            var pluralized = minQty > 1 ? 'images' : 'image';

            if (self.notification) {
                return;
            }

            self.notification = F.controller.construct('Notification', {
                commonStorage: self.commonStorage,
                message: 'This field requires at least ' + minQty + ' ' + pluralized + '.'
            });
        },

        removeUnder: function() {
            var self = this;

            if (! self.notification) {
                return;
            }

            self.notification.destroy();
            self.notification = null;
        }
    });
}

runMinTracker(window.ANSEL);
