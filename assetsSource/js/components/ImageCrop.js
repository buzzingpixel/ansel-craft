window.ANSEL = window.ANSEL || {};

function runImageCrop(F) {
    'use strict';

    if (! window.jQuery || ! F.controller || ! F.model) {
        setTimeout(function() {
            runImageCrop(F);
        }, 10);
        return;
    }

    F.controller.make('ImageCrop', {
        commonStorage: {},
        $imageTag: null,
        setInitialCoords: true,
        model: {},

        $cropImage: null,
        $approveCropBtn: null,
        $cancelCropBtn: null,

        imgWidth: null,
        imgHeight: null,

        init: function() {
            var self = this;

            /*var timer = setTimeout(function() {
                // TODO: handle image loading fail
                console.log('TODO: image load failed');
            }, 30000);*/

            self.$el = $(self.commonStorage.cropTableTemplate);
            self.el = self.$el.get(0);

            self.$cropImage = self.$el.find('.JSAnselCropTable__Img');
            self.$approveCropBtn = self.$el.find('.JSAnselToolBar__Button--ApproveCrop');
            self.$cancelCropBtn = self.$el.find('.JSAnselToolBar__Button--CancelCrop');

            self.$cropImage.on('load', function() {
                var img = this;

                self.$cropImage.off('load');

                // clearTimeout(timer);

                self.imgWidth = img.width;
                self.imgHeight = img.height;

                if (self.setInitialCoords) {
                    self.setUpInitialCoords();
                }

                self.model.onChange('runCrop', function() {
                    self.render();
                });
            });

            self.$cropImage.attr('src', self.$imageTag.attr('src'));
        },

        setUpInitialCoords: function() {
            var self = this;
            var coords = {
                h: 200,
                w: 200,
                x: 0,
                y: 0
            };
            var ratioWidth = self.commonStorage.sharedModel.get('ratioWidth');
            var ratioHeight = self.commonStorage.sharedModel.get('ratioHeight');

            if (ratioWidth && ratioHeight) {
                // If image ratio is greater than ratio
                if ((ratioHeight / ratioWidth) <=
                    (self.imgHeight / self.imgWidth)
                ) {
                    coords.h = self.imgWidth * (ratioHeight / ratioWidth);
                    coords.y = (self.imgHeight - coords.h) / 2;
                    coords.x = 0;
                    coords.w = self.imgWidth;
                } else {
                    coords.w = self.imgHeight * (ratioWidth / ratioHeight);
                    coords.x = (self.imgWidth - coords.w) / 2;
                    coords.y = 0;
                    coords.h = self.imgHeight;
                }
            } else {
                coords.w = self.imgWidth;
                coords.h = self.imgHeight;
            }

            self.model.set('coords', coords);
        },

        render: function() {
            var self = this;

            self.prevCropValues = self.model.get('coords');

            $('body').append(self.$el);
        }
    });
}

runImageCrop(window.ANSEL);
