window.ANSEL = window.ANSEL || {};

function runImage(F) {
    'use strict';

    if (! window.jQuery || ! F.controller || ! F.model) {
        setTimeout(function() {
            runImage(F);
        }, 10);
        return;
    }

    F.controller.make('Image', {
        commonStorage: {},

        $image: null,
        $imageTag: null,

        base64Image: null,
        cacheFile: null,
        fileName: null,

        init: function() {
            var self = this;

            // TODO: detect what methods should be used to set up this image
            // TODO: base64? Existing src etc.

            self.$image = $(self.commonStorage.imageTemplate);

            self.$imageTag = self.$image.find('.JSAnselField__ImageTag');

            self.$imageTag.attr('src', self.base64Image);

            self.commonStorage.$imagesHolder.append(self.$image);

            self.commonStorage.sorter.addItems(self.$image);
        }
    });
}

runImage(window.ANSEL);
