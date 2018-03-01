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
        commonStorage: {},
        error: false,
        heading: '',
        message: '',
        destroyEvents: [],
        $line: null,

        init: function() {
            var self = this;
            var message = '';

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

            self.$line.appendTo(self.commonStorage.$notificationList);

            self.commonStorage.eventTriggers.set(
                'notificationChange',
                self.commonStorage.eventTriggers.get('notificationChange') + 1
            );

            self.destroyEvents.forEach(function(i) {
                self.commonStorage.eventTriggers.onChange(
                    i + '.' + self.uuid,
                    function() {
                        self.destroy();
                    }
                );
            });
        },

        destroy: function() {
            var self = this;

            self.$line.remove();

            self.destroyEvents.forEach(function(i) {
                self.commonStorage.eventTriggers.offChange(
                    i + '.' + self.uuid
                );
            });

            self.commonStorage.eventTriggers.set(
                'notificationChange',
                self.commonStorage.eventTriggers.get('notificationChange') + 1
            );
        }
    });
}

runNotification(window.ANSEL);
