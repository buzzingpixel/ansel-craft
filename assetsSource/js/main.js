window.ANSEL = window.ANSEL || {};

function runMain(F) {
    'use strict';

    if (! window.jQuery || ! F.controller || ! F.model
    ) {
        setTimeout(function() {
            runMain(F);
        }, 10);
        return;
    }

    F.AnselGlobalImageQueue = {};

    F.controller.construct('ImageProcessor');

    $('.JSAnselField').each(function() {
        F.controller.construct('Field', {
            el: this
        });
    });
}

runMain(window.ANSEL);
