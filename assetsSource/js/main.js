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

    F.imagesBeingUploaded = 0;

    F.AnselGlobalImageQueue = {};

    F.controller.construct('ImageProcessor');

    F.runInitOnFields = function() {
        $('.JSAnselField').each(function() {
            var $el = $(this);

            if ($el.data('anselInit')) {
                return;
            }

            $el.data('anselInit', true);

            F.controller.construct('Field', {
                el: this
            });
        });
    };

    F.runInitOnFields();
}

runMain(window.ANSEL);
