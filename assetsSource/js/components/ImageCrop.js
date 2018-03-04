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
        /**
         * Locked - cancel crop sets this to locked so that no changes to
         * position occur after lasso has been accepted
         *
         * @var {Boolean} locked
         */
        locked: false,

        commonStorage: {},
        $imageTag: null,
        setInitialCoords: true,
        model: {},

        $cropImage: null,
        $approveCropBtn: null,
        $cancelCropBtn: null,

        imgWidth: null,
        imgHeight: null,
        scale: null,
        prevCropValues: null,

        limitedWidth: 0,
        limitedHeight: 0,

        resizingInProgress: false,
        setupInProgress: false,

        Jcrop: null,
        JcropChangeTimeout: 0,

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

            self.setUpCrop();

            self.$approveCropBtn.on('click.approve', function() {
                self.closeCrop();
            });

            self.$cancelCropBtn.on('click.cancel', function() {
                self.closeCrop();
                self.resetCropValues();
            });

            self.setUpKeyBindings();
        },

        setUpCrop: function() {
            var self = this;
            var coords = self.model.get('coords');
            var ratioWidth = self.commonStorage.sharedModel.get('ratioWidth');
            var ratioHeight = self.commonStorage.sharedModel.get('ratioHeight');
            var minWidth = self.commonStorage.sharedModel.get('minWidth');
            var minHeight = self.commonStorage.sharedModel.get('minHeight');
            var JcropOptions = {
                onChange: function(coords) {
                    if (self.locked) {
                        return;
                    }

                    // Run the callback
                    self.jCropOnChange(coords);

                    // Set the model
                    self.model.set('coords', coords);
                },
                bgColor: 'black',
                bgOpacity: 0.5
            };

            self.locked = false;

            // If Jcrop is already set up we need to destroy it
            if (self.Jcrop) {
                self.Jcrop.destroy();
            }

            self.setScale();

            // Set the aspect ratio
            if (ratioWidth && ratioHeight) {
                JcropOptions.aspectRatio = ratioWidth / ratioHeight;

                // Set min height to aspect ratio if applicable
                // or set min width to aspect ratio if applicable
                if (minWidth && ! minHeight) {
                    minHeight = Math.floor(
                        (ratioHeight * minWidth) / ratioWidth
                    );
                } else if (minHeight && ! minWidth) {
                    minWidth = Math.floor(
                        (ratioWidth * minHeight) / ratioHeight
                    );
                }
            }

            // Set min size
            if (minWidth || minHeight) {
                JcropOptions.minSize = [
                    minWidth ? minWidth * self.scale : 0,
                    minHeight ? minHeight * self.scale : 0
                ];
            }

            // Set the select area
            JcropOptions.setSelect = [
                coords.x,
                coords.y,
                coords.w,
                coords.h
            ];

            // Set image width
            self.$cropImage.css('width', self.imgWidth * self.scale);
            self.$cropImage.css('height', self.imgHeight * self.scale);
            JcropOptions.boxWidth = self.imgWidth * self.scale;
            JcropOptions.boxHeight = self.imgHeight * self.scale;
            JcropOptions.xscale = self.scale;
            JcropOptions.yscale = self.scale;

            // Set up Jcrop
            self.$cropImage.JcropAnsel(JcropOptions, function() {
                self.Jcrop = this;

                // Resizing has ended
                self.resizingInProgress = false;

                setTimeout(function() {
                    self.setupInProgress = false;
                }, 10);
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

            // Check if image is over any max
            if (self.imgWidth > maxWidth || self.imgHeight > maxHeight) {
                // Set ratios
                widthRatio = maxWidth / self.imgWidth;
                heightRatio = maxHeight / self.imgHeight;

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

        jCropOnChange: function(coords) {
            var self = this;
            var minWidth = self.commonStorage.sharedModel.get('minWidth') || 0;
            var minHeight = self.commonStorage.sharedModel.get('minHeight') || 0;
            var maxWidth = self.commonStorage.sharedModel.get('maxWidth') || 0;
            var maxHeight = self.commonStorage.sharedModel.get('maxHeight') || 0;
            var limitedWidth = self.limitedWidth || 0;
            var limitedHeight = self.limitedHeight || 0;
            var oldCoords = self.model.get('coords');
            var stop = false;
            var $body = $('body');

            // Check if this is a resizing event or if Jcrop is not set
            if (self.resizingInProgress || ! self.Jcrop) {
                stop = true;
            }

            // Check if anything has changed
            if (oldCoords &&
                oldCoords.w === coords.w &&
                oldCoords.h === coords.h &&
                oldCoords.y === coords.y &&
                oldCoords.y2 === coords.y2 &&
                oldCoords.x === coords.x &&
                oldCoords.x2 === coords.x2
            ) {
                stop = true;
            }

            self.model.set('coords', coords);

            if (stop) {
                return;
            }

            // Make sure one side doesn't get too small
            if (maxWidth && maxHeight) {
                // If max height and we're over max width scale
                if (minHeight && (coords.w > maxWidth)) {
                    // Increase the min height as the width increases over max
                    minHeight = (coords.w * minHeight) / maxWidth;

                    // If the minHeight exceeds actual image height, limit width
                    if (minHeight >= self.imgHeight) {
                        // Make sure a limited width is set
                        self.limitedWidth = self.limitedWidth || coords.w;

                        // Get the min of the limited width or coords width
                        self.limitedWidth = Math.min(
                            coords.w,
                            self.limitedWidth
                        );

                        // Set a max width
                        limitedWidth = self.limitedWidth;
                    }
                }

                // If min width and we're over max height scale
                if (minWidth && (coords.h > maxHeight)) {
                    // Increase the min width as the height increase over max
                    minWidth = (coords.h * minWidth) / maxHeight;

                    if (minWidth >= self.imgWidth) {
                        // Make sure a limited height is set
                        self.limitedHeight = self.limitedHeight || coords.h;

                        // Get the min of the limited height or coords height
                        self.lmitedHeight = Math.min(
                            coords.h,
                            self.limitedHeight
                        );

                        // Set a max height
                        limitedHeight = self.limitedHeight;
                    }
                }
            }

            // Make sure we don't go over the actual image dimensions
            minWidth = Math.min(self.imgWidth, minWidth);
            minHeight = Math.min(self.imgHeight, minHeight);

            // Clear any previous mouseup event from last change
            $body.off('mouseup.JcropChange');

            // Clear the previous timeout from last event change
            clearTimeout(self.JcropChangeTimeout);

            // Set the Jcrop values if applicable
            if (minWidth || minHeight || limitedWidth || limitedHeight) {
                // If we're scaling up, apply the changes immediately
                if (oldCoords.h <= coords.h && oldCoords.w <= coords.w) {
                    // Apply the constraints
                    self.Jcrop.setOptions({
                        minSize: [
                            minWidth * self.scale,
                            minHeight * self.scale
                        ],
                        maxSize: [
                            limitedWidth,
                            limitedHeight
                        ]
                    });

                    // Reapply the old min size to allow for scaling down
                    // after short wait
                    self.JcropChangeTimeout = setTimeout(function() {
                        self.Jcrop.setOptions({
                            minSize: [
                                minWidth ? minWidth * self.scale : 0,
                                minHeight ? minHeight * self.scale : 0
                            ]
                        });
                    }, 500);

                // Otherwise we're scaling down and need to wait until mouseup
                } else {
                    // Apply the min size on mouseup
                    $body.on('mouseup.JcropChange', function() {
                        self.Jcrop.setOptions({
                            minSize: [
                                minWidth * self.scale,
                                minHeight * self.scale
                            ],
                            maxSize: [
                                limitedWidth,
                                limitedHeight
                            ]
                        });

                        // Reapply the old min size to allow for scaling down
                        // after short wait
                        self.JcropChangeTimeout = setTimeout(function() {
                            self.Jcrop.setOptions({
                                minSize: [
                                    minWidth ? minWidth * self.scale : 0,
                                    minHeight ? minHeight * self.scale : 0
                                ]
                            });
                        }, 500);
                    });
                }
            }
        },

        setUpKeyBindings: function() {
            var self = this;
            var $window = $(window);
            var lastKey = null;

            // Save crop values on "enter" or "ctrl/cmd + s"
            $window.on('keydown.ansel', function(e) {
                // Save crop values if the keycode is enter key
                if (e.keyCode === 13) {
                    self.closeCrop();

                // If key code is escape key
                } else if (e.keyCode === 27) {
                    self.closeCrop();
                    self.resetCropValues();

                // Otherwise if last key was ctrl/cmd and this key is s
                } else if (lastKey && (lastKey === 91 && e.keyCode === 83)) {
                    e.preventDefault();

                    self.closeCrop();
                }

                // Check if the key is ctrl/cmd, otherwise we don't care about
                // last key
                lastKey = e.keyCode === 91 ? 91 : null;
            });

            // If ctrl/cmd is release, reset last key to null
            $window.on('keyup.ansel', function(e) {
                if (e.keyCode !== 91) {
                    return;
                }

                lastKey = null;
            });
        },

        closeCrop: function() {
            var self = this;
            var $window = $(window);

            // Lock the values
            self.locked = true;

            // Detach the crop elements
            self.$el.detach();

            // Disable click and keyboard bindings
            self.$approveCropBtn.off('click.approve');
            self.$cancelCropBtn.off('click.cancel');
            $window.off('keydown.ansel');
            $window.off('keyup.ansel');
        },

        resetCropValues: function() {
            var self = this;

            setTimeout(function() {
                self.model.set('coords', self.prevCropValues);
            }, 200);
        }
    });
}

runImageCrop(window.ANSEL);
