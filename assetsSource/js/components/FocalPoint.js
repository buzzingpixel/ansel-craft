window.ANSEL = window.ANSEL || {};

function runFocalPoint(F) {
    'use strict';

    if (! window.jQuery || ! F.controller || ! F.model) {
        setTimeout(function() {
            runFocalPoint(F);
        }, 10);
        return;
    }

    F.controller.make('FocalPoint', {
        commonStorage: {},

        uuid: null,

        scale: 1,

        $image: null,

        $wrapper: null,
        $wrapperInner: null,
        $tableImgTag: null,
        $cancel: null,
        $approve: null,

        realImgWidth: null,
        realImgHeight: null,

        focalY: 0.5,
        focalX: 0.5,

        init: function() {
            var self = this;

            self.uuid = F.uuid.make();

            self.$el = $(self.commonStorage.focalPointTableTemplate);
            self.el = self.$el.get(0);

            self.$wrapper = self.$el.find('.JSAnselCropTable__FocalImgWrapper');
            self.$wrapperInner = self.$el.find('.JSAnselCropTable__FocalImgWrapperInner');
            self.$tableImgTag = self.$el.find('.JSAnselCropTable__FocalImg');
            self.$cancel = self.$el.find('.JSAnselToolBar__Button--Cancel');
            self.$approve = self.$el.find('.JSAnselToolBar__Button--Approve');

            self.$tableImgTag.on('load', function() {
                var img = this;

                self.realImgWidth = img.width;
                self.realImgHeight = img.height;

                self.model.onChange('runFocusPoint', function() {
                    self.render();
                });
            });

            self.$tableImgTag.attr('src', self.$imageTag.attr('src'));
        },

        render: function() {
            var self = this;

            self.focalX = parseFloat(self.$image.find('.JSAnselField__Input--FocalX').val());
            self.focalY = parseFloat(self.$image.find('.JSAnselField__Input--FocalY').val());

            self.setScale();

            $('body').append(self.$el);

            self.renderInner();

            self.$approve.on('click.approve-' + self.uuid, function() {
                self.close();
                self.triggerSave();
            });

            self.$cancel.on('click.cancel-' + self.uuid, function() {
                self.close();
            });

            self.setUpKeyBindings();
        },

        renderInner: function() {
            var self = this;
            var coords = self.model.get('coords');
            var holderWidth = coords.w * self.scale;
            var holderHeight = coords.h * self.scale;
            var rx = holderWidth / coords.w;
            var ry = holderHeight / coords.h;
            var imgWidth = Math.round(rx * self.realImgWidth);
            var imgHeight = Math.round(ry * self.realImgHeight);
            var imgX = Math.round(rx * coords.x) * -1;
            var imgY = Math.round(ry * coords.y) * -1;

            self.$wrapperInner.css({
                height: holderHeight,
                margin: '0 auto',
                overflow: 'hidden',
                position: 'relative',
                width: holderWidth
            });

            self.$tableImgTag.css({
                height: imgHeight + 'px',
                left: imgX + 'px',
                'max-width': 'none',
                position: 'absolute',
                top: imgY + 'px',
                width: imgWidth + 'px'
            });

            self.trackDot();
        },

        trackDot: function() {
            var self = this;
            var focalYPercentage = self.focalY * 100;
            var focalXPercentage = self.focalX * 100;
            var $focalDot = self.$el.find('.JSAnselCropTable__FocalDot');
            var $window = $(window);
            var mouseIsDown = false;
            var offset = self.$wrapperInner.offset();
            var leftOffset = offset.left;
            var topOffset = offset.top;
            var width = self.$wrapperInner.innerWidth();
            var height = self.$wrapperInner.innerHeight();
            var mousePosY;
            var mousePosX;

            function setDotPercentage(y, x) {
                $focalDot.css({
                    left: x + '%',
                    top: y + '%'
                });
            }

            setDotPercentage(focalYPercentage, focalXPercentage);

            self.$wrapperInner.on('mousedown.focal-' + self.uuid, function() {
                mouseIsDown = true;
            });

            $window.on('mouseup.focal-' + self.uuid, function() {
                mouseIsDown = false;
            });

            $window.on('mousemove.focal-' + self.uuid, function(e) {
                if (! mouseIsDown) {
                    return;
                }

                mousePosY = e.pageY - topOffset;
                mousePosY = mousePosY > 0 ? mousePosY : 0;
                mousePosY = mousePosY < height ? mousePosY : height;

                mousePosX = e.pageX - leftOffset;
                mousePosX = mousePosX > 0 ? mousePosX : 0;
                mousePosX = mousePosX < width ? mousePosX : width;

                focalYPercentage = (mousePosY / height) * 100;
                focalXPercentage = (mousePosX / width) * 100;

                setDotPercentage(focalYPercentage, focalXPercentage);

                self.focalY = parseFloat((focalYPercentage / 100).toPrecision(3));
                self.focalX = parseFloat((focalXPercentage / 100).toPrecision(3));
            });
        },

        setScale: function() {
            var self = this;
            var widthRatio;
            var heightRatio;
            var winWidth = window.innerWidth;
            var winHeight = window.innerHeight;
            var maxWidth = Math.max(winWidth - 80, winWidth * 0.90);
            var maxHeight = Math.max(winHeight - 140, winHeight * 0.90);
            var coords = self.model.get('coords');

            // Check if image is over any max
            if (coords.w > maxWidth || coords.h > maxHeight) {
                // Set ratios
                widthRatio = maxWidth / coords.w;
                heightRatio = maxHeight / coords.h;

                // Set scale
                self.scale = (
                    Math.round(Math.min(widthRatio, heightRatio) * 100) / 100
                );

                // We're done here
                return;
            }

            // If we got past the check, the scale is 1
            self.scale = 1;
        },

        setUpKeyBindings: function() {
            var self = this;
            var $window = $(window);
            var lastKey = null;

            // Save crop values on "enter" or "ctrl/cmd + s"
            $window.on('keydown.ansel-' + self.uuid, function(e) {
                // Save crop values if the keycode is enter key
                if (e.keyCode === 13) {
                    self.close();
                    self.triggerSave();

                // If key code is escape key
                } else if (e.keyCode === 27) {
                    self.close();

                // Otherwise if last key was ctrl/cmd and this key is s
                } else if (lastKey && (lastKey === 91 && e.keyCode === 83)) {
                    e.preventDefault();
                    self.close();
                    self.triggerSave();
                }

                // Check if the key is ctrl/cmd, otherwise we don't care about
                // last key
                lastKey = e.keyCode === 91 ? 91 : null;
            });

            // If ctrl/cmd is release, reset last key to null
            $window.on('keyup.ansel-' + self.uuid, function(e) {
                if (e.keyCode !== 91) {
                    return;
                }

                lastKey = null;
            });
        },

        close: function() {
            var self = this;
            var $window = $(window);

            self.$el.detach();

            self.$approve.off('click.approve-' + self.uuid);
            self.$cancel.off('click.cancel-' + self.uuid);
            self.$wrapperInner.off('mousedown.focal-' + self.uuid);
            $window.off('mouseup.focal-' + self.uuid);
            $window.off('mousemove.focal-' + self.uuid);
            $window.off('keydown.ansel-' + self.uuid);
            $window.off('keyup.ansel-' + self.uuid);
        },

        triggerSave: function() {
            var self = this;

            self.$image.find('.JSAnselField__Input--FocalX').val(self.focalX);
            self.$image.find('.JSAnselField__Input--FocalY').val(self.focalY);
        },

        destroy: function() {
            var self = this;

            self.close();

            self.$image = null;
            self.$wrapper = null;
            self.$wrapperInner = null;
            self.$tableImgTag = null;
            self.$cancel = null;
            self.$approve = null;
        }
    });
}

runFocalPoint(window.ANSEL);
