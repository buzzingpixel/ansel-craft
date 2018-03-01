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
        eventTriggers: null,
        $notifications: null,

        init: function() {
            var self = this;

            self.$notifications = self.$el.find('.JSAnselField__Notifications');

            self.eventTriggers.onChange('notificationChange', function() {
                setTimeout(function() {
                    self.triggerChange();
                }, 500);
            });
        },

        triggerChange: function() {
            var self = this;
            var $items = self.$notifications.find('.JSAnselField__Notification');
            var count = $items.length;

            if (! count) {
                self.$notifications.removeClass(
                    'AnselField__Notifications--HasNotifications'
                );
                return;
            }

            self.$notifications.addClass(
                'AnselField__Notifications--HasNotifications'
            );
        }
    });
}

runNotifications(window.ANSEL);
