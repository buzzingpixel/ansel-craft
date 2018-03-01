window.ANSEL = window.ANSEL || {};

function runNotification(F) {
    'use strict';

    if (! window.jQuery || ! F.controller || ! F.model) {
        setTimeout(function() {
            runNotification(F);
        }, 10);
        return;
    }

    F.controller.make('Notification', {
        eventTriggers: null,
        error: false,
        heading: '',
        message: '',
        destroyEvents: [],
        $notificationList: null,
        $line: null,

        init: function() {
            var self = this;
            var message = '';

            self.$notificationList = self.$el.find(
                '.JSAnselField__Notifications'
            );

            self.$line = $(
                '<li class="AnselField__Notification JSAnselField__Notification"></li>'
            );

            if (self.heading) {
                message += '<strong>' + self.heading + '</strong>: ';
            }

            message += self.message;

            self.$line.html(message);

            if (self.error) {
                self.$line.addClass('AnselField__Notification--IsError');
            }

            self.$line.appendTo(self.$notificationList);

            self.eventTriggers.set(
                'notificationChange',
                self.eventTriggers.get('notificationChange') + 1
            );

            self.destroyEvents.forEach(function(i) {
                self.eventTriggers.onChange(i + '.' + self.uuid, function() {
                    self.destroy();
                });
            });
        },

        destroy: function() {
            var self = this;

            self.$line.remove();

            self.destroyEvents.forEach(function(i) {
                self.eventTriggers.offChange(i + '.' + self.uuid);
            });

            self.eventTriggers.set(
                'notificationChange',
                self.eventTriggers.get('notificationChange') + 1
            );
        }
    });
}

runNotification(window.ANSEL);
