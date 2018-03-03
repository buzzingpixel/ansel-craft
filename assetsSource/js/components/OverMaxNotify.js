window.ANSEL = window.ANSEL || {};

function runOverMaxNotify(F) {
    'use strict';

    if (! window.jQuery || ! F.controller || ! F.model) {
        setTimeout(function() {
            runOverMaxNotify(F);
        }, 10);
        return;
    }

    F.controller.make('OverMaxNotify', {
        commonStorage: {},

        notification: null,

        model: {
            isOverMax: 'bool'
        },

        init: function() {
            var self = this;

            self.model.set('isOverMax', false);

            self.checkIfOverMax();

            self.commonStorage.eventTriggers.onChange('orderChange', function() {
                self.checkIfOverMax();
            });

            self.model.onChange('isOverMax', function(val) {
                if (val) {
                    self.setNotification();
                    return;
                }

                self.removeNotification();
            });
        },

        checkIfOverMax: function() {
            var self = this;
            var $images = self.commonStorage.$el.find('.JSAnselField__Image');
            var imageCount = $images.length;
            var maxQty = self.commonStorage.sharedModel.get('maxQty');

            if (! maxQty || imageCount <= maxQty) {
                self.model.set('isOverMax', false);
                return;
            }

            self.model.set('isOverMax', true);
        },

        setNotification: function() {
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

        removeNotification: function() {
            var self = this;

            if (! self.notification) {
                return;
            }

            self.notification.destroy();

            self.notification = null;
        }
    });
}

runOverMaxNotify(window.ANSEL);
