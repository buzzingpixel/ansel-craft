window.ANSEL = window.ANSEL || {};

function runNotifications(F) {
    'use strict';

    if (! window.jQuery || ! F.controller || ! F.model) {
        setTimeout(function() {
            runNotifications(F);
        }, 10);
        return;
    }

    F.controller.make('Notifications', {
        commonStorage: {},
        $notifications: null,

        init: function() {
            var self = this;

            self.commonStorage.eventTriggers.onChange(
                'notificationChange',
                function() {
                    setTimeout(function() {
                        self.triggerChange();
                    }, 500);
                }
            );
        },

        triggerChange: function() {
            var self = this;
            var $notifications = self.commonStorage.$notificationList;
            var $items = $notifications.find('.JSAnselField__Notification');
            var count = $items.length;

            if (! count) {
                $notifications.removeClass(
                    'AnselField__Notifications--HasNotifications'
                );
                return;
            }

            $notifications.addClass(
                'AnselField__Notifications--HasNotifications'
            );
        }
    });
}

runNotifications(window.ANSEL);
