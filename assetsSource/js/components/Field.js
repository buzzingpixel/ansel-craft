window.ANSEL = window.ANSEL || {};

function runField(F) {
    'use strict';

    if (! window.jQuery || ! F.controller || ! F.model) {
        setTimeout(function() {
            runField(F);
        }, 10);
        return;
    }

    F.controller.make('Field', {
        init: function() {
            var self = this;

            console.log('init');

            self.$el.on('dragover', function(e) {
                e.preventDefault();
            });

            self.$el.on('dragenter', function(e) {
                e.preventDefault();
                $(this).addClass('AnselField--DragInProgress');
                console.log('dragenter');
            });

            self.$el.on('dragleave', function(e) {
                e.preventDefault();
                $(this).removeClass('AnselField--DragInProgress');
                console.log('dragleave');
            });

            self.$el.on('drop', function(e) {
                e.preventDefault();
                $(this).removeClass('AnselField--DragInProgress');
                console.log('drop');
            });
        }
    });
}

runField(window.ANSEL);
