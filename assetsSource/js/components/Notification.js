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
        destroyAfter: false,
        $line: null,

        init: function() {
            var self = this;
            var ajaxData = new FormData();

            ajaxData.append(
                'CRAFT_CSRF_TOKEN',
                self.commonStorage.sharedModel.get('csrfToken')
            );

            ajaxData.append('toTranslate[heading]', self.heading);
            ajaxData.append('toTranslate[message]', self.message);

            $.ajax({
                url: self.commonStorage.translateActionUrl,
                type: 'post',
                data: ajaxData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: function(json) {
                    self.render(json.message, json.heading);
                },
                error: function() {
                    self.render(self.message, self.heading);
                }
            });
        },

        render: function(message, heading) {
            var self = this;
            var localMessage = '';

            self.$line = $(
                '<li class="AnselField__Notification JSAnselField__Notification"></li>'
            );

            if (heading) {
                localMessage += '<strong>' + heading + '</strong>: ';
            }

            localMessage += message;

            self.$line.html(localMessage);

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

            if (! self.destroyAfter) {
                return;
            }

            setTimeout(function() {
                self.destroy();
            }, self.destroyAfter);
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
