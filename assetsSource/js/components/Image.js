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
        $fieldsModal: null,

        base64Image: null,
        cacheFile: null,
        fileName: null,

        uuid: null,

        model: {
            isOverAllowed: 'bool',
            'runCrop': 'int',
            coords: 'object',
            imageSave: 'int'
        },

        init: function() {
            var self = this;

            self.setUp();
            self.setUpRowId();
            self.initFieldEditor();
            self.watchOpenFieldEditor();
            self.watchForCoverChange();
            self.watchForOrderChange();
            self.watchForRemove();
            self.setUpCrop();
        },

        setUp: function() {
            var self = this;
            var imageUuids = self.commonStorage.eventTriggers.get(
                'imageControllerUuids'
            ).slice(0);

            self.uuid = F.uuid.make();

            imageUuids.push(self.uuid);

            self.commonStorage.eventTriggers.set(
                'imageControllerUuids',
                imageUuids
            );

            self.$image = $(self.commonStorage.imageTemplate);

            // TODO: detect what methods should be used to set up this image
            // TODO: base64? Existing src etc.

            self.$image.find('.JSAnselField__Input--CacheFile').val(
                self.cacheFile
            );

            self.$image.find('.JSAnselField__Input--FileName').val(
                self.fileName
            );

            self.$image.data('jsUuid', self.uuid);

            self.$imageTag = self.$image.find('.JSAnselField__ImageTag');

            self.$imageTag.attr('src', self.base64Image);

            self.commonStorage.$imagesHolder.append(self.$image);

            self.commonStorage.sorter.addItems(self.$image);

            self.base64Image = null;

            self.$image.find('.JSAnselField__Input--CacheFileLocation').val(
                self.cacheFile
            );

            self.model.onChange('imageSave', function() {
                self.processImage();
            });
        },

        setUpRowId: function() {
            var self = this;
            var $imageInputs = self.$image.find('.JSAnselField__Input');
            var oldId = self.$image.data('id');

            self.$image.data('id', self.uuid).attr('data-id', self.uuid);

            $imageInputs.each(function() {
                var $el = $(this);
                var name = $el.attr('name');

                name = name.replace(
                    'anselRowId' + oldId,
                    'anselRowId' + self.uuid
                );

                $el.attr('name', name);
            });
        },

        initFieldEditor: function() {
            var self = this;
            var $editIcon = self.$image.find('.JSAnselField__ImageIconEdit');

            $editIcon.on('click.' + self.uuid, function() {
                self.$fieldsModal = $(self.commonStorage.fieldsModalHtml);
                self.openFieldEditor();
            });
        },

        openFieldEditor: function() {
            var self = this;
            self.$fieldsModal = $(self.commonStorage.fieldsModalHtml);
            self._openFieldEditor();
        },

        _openFieldEditor: function() {
            var self = this;
            var $cancel = self.$fieldsModal.find('.AnselField__FieldsModalCancel');
            var $save = self.$fieldsModal.find('.AnselField__FieldsModalSave');
            var $prev = self.$fieldsModal.find('.AnselField__FieldsModalPrev');
            var $next = self.$fieldsModal.find('.AnselField__FieldsModalNext');
            var $img = self.$fieldsModal.find('.JSAnselField__FieldsModalPreviewImageTag');
            var $heading = self.$fieldsModal.find('.JSAnselField__FieldsModalBodyHeading');
            var modal = new window.Garnish.Modal(self.$fieldsModal);
            var $modalTitle = self.$fieldsModal.find('.JSAnselField__FieldsModalTitle');
            var $imageTitle = self.$image.find('.JSAnselField__Input--Title');
            var $modalCaption = self.$fieldsModal.find('.JSAnselField__FieldsModalCaption');
            var $imageCaption = self.$image.find('.JSAnselField__Input--Caption');
            var $modalCoverSwitch = self.$fieldsModal.find('.JSAnselField__FieldsModalCover').find('.lightswitch');
            var $modalCover = $modalCoverSwitch.find(':input');
            var $imageCover = self.$image.find('.JSAnselField__Input--Cover');
            var $previousImage = self.$image.prev('.JSAnselField__Image');
            var $nextImage = self.$image.next('.JSAnselField__Image');
            var $prevNextContainer = self.$fieldsModal.find('.AnselField__FieldsModalFooterSecondaryButtons');

            function saveValues() {
                // Set them to empty first in case they return falsy values and confuse jquery
                $imageTitle.val('');
                $imageCaption.val('');
                $imageCover.val('');

                $imageTitle.val($modalTitle.val());
                $imageCaption.val($modalCaption.val());
                $imageCover.val($modalCover.val());

                if ($modalCover.val()) {
                    self.commonStorage.eventTriggers.set(
                        'activeCover',
                        self.uuid
                    );
                }
            }

            function closeEditor() {
                if (! modal) {
                    return;
                }

                modal.hide();
                modal.destroy();
                modal = null;
            }

            // TODO: hide prev/next image buttons as required

            $img.attr('src', self.$imageTag.attr('src'));

            $heading.text(self.fileName);

            self.$fieldsModal.find('.lightswitch').lightswitch();

            $modalTitle.val($imageTitle.val());
            $modalCaption.val($imageCaption.val());

            if ($imageCover.val()) {
                $modalCoverSwitch.trigger('mousedown').trigger('mouseup');
            }

            $cancel.on('click', function() {
                closeEditor();
            });

            $save.on('click', function() {
                saveValues();
                closeEditor();
            });

            if ($previousImage.length) {
                $prev.on('click', function() {
                    saveValues();
                    modal.hide();
                    self.commonStorage.eventTriggers.set('openFieldEditor', '');
                    setTimeout(function() {
                        closeEditor();
                        self.commonStorage.eventTriggers.set(
                            'openFieldEditor',
                            $previousImage.data('jsUuid')
                        );
                    }, 50);
                });
            } else {
                $prev.addClass('disabled');
            }

            if ($nextImage.length) {
                $next.on('click', function() {
                    saveValues();
                    modal.hide();
                    self.commonStorage.eventTriggers.set('openFieldEditor', '');
                    setTimeout(function() {
                        closeEditor();
                        self.commonStorage.eventTriggers.set(
                            'openFieldEditor',
                            $nextImage.data('jsUuid')
                        );
                    }, 50);
                });
            } else {
                $next.addClass('disabled');
            }

            if (! $previousImage.length && ! $nextImage.length) {
                $prevNextContainer.hide();
            }
        },

        watchOpenFieldEditor: function() {
            var self = this;

            self.commonStorage.eventTriggers.onChange(
                'openFieldEditor.' + self.uuid,
                function(val) {
                    if (val !== self.uuid) {
                        return;
                    }

                    setTimeout(function() {
                        self.openFieldEditor();
                    }, 50);
                }
            );
        },

        watchForCoverChange: function() {
            var self = this;

            self.commonStorage.eventTriggers.onChange(
                'activeCover.' + self.uuid,
                function(val) {
                    if (val === self.uuid) {
                        return;
                    }

                    self.$image.find('.JSAnselField__Input--Cover').val('');
                }
            );
        },

        watchForOrderChange: function() {
            var self = this;

            function orderChange() {
                var pos = self.$image.prevAll('.JSAnselField__Image').length + 1;
                var maxQty = self.commonStorage.sharedModel.get('maxQty');

                ! maxQty || pos <= maxQty ?
                    self.model.set('isOverAllowed', false) :
                    self.model.set('isOverAllowed', true);
            }

            self.model.onChange('isOverAllowed.' + self.uuid, function(val) {
                val ? self.$image.addClass('AnselField__Image--IsOverMax') :
                    self.$image.removeClass('AnselField__Image--IsOverMax');
            });

            self.commonStorage.eventTriggers.onChange(
                'orderChange.' + self.uuid,
                function() {
                    orderChange();
                }
            );

            orderChange();
        },

        watchForRemove: function() {
            var self = this;

            self.$image.find('.JSAnselField__ImageIconRemove')
                .on('click.' + self.uuid, function() {
                    self.remove();
                }
            );
        },

        setUpCrop: function() {
            var self = this;

            self.anselCropController = F.controller.construct('ImageCrop', {
                model: self.model,
                commonStorage: self.commonStorage,
                $imageTag: self.$imageTag
            });

            self.$image.find('.JSAnselField__ImageIconCrop').on('click', function() {
                self.model.set('runCrop', self.model.get('runCrop') + 1);
            });

            self.model.onChange('coords', function(val) {
                self.updateCoordsInputs(val);
                self.updateThumbImgPosition(val);
            });
        },

        updateCoordsInputs: function(coords) {
            var self = this;

            self.$image.find('.JSAnselField__Input--X').val(Math.floor(coords.x));
            self.$image.find('.JSAnselField__Input--Y').val(Math.floor(coords.y));

            self.$image.find('.JSAnselField__Input--Width').val(Math.min(
                coords.w,
                self.anselCropController.imgWidth
            ));

            self.$image.find('.JSAnselField__Input--Height').val(Math.min(
                coords.h,
                self.anselCropController.imgHeight
            ));
        },

        updateThumbImgPosition: function(coords) {
            var self = this;
            var ratio = coords.w / coords.h;
            var holderWidth = 168;
            var holderHeight = Math.round(holderWidth / ratio);
            var rx = holderWidth / coords.w;
            var ry = holderHeight / coords.h;
            var thumbWidth = Math.round(rx * self.anselCropController.imgWidth);
            var thumbHeight = Math.round(ry * self.anselCropController.imgHeight);
            var thumbX = Math.round(rx * coords.x) * -1;
            var thumbY = Math.round(ry * coords.y) * -1;

            // Check if the source file is missing
            // if (self.sourceFileMissing) {
            //     self.$anselImage.css('position', 'static');
            //     self.$anselImage.show();
            //     return;
            // }

            self.$image.find('.JSAnselField__ImageWrapperInner').css({
                height: holderHeight + 'px',
                overflow: 'hidden',
                position: 'relative',
                width: holderWidth + 'px'
            });

            self.$imageTag.css({
                height: thumbHeight + 'px',
                left: thumbX + 'px',
                'max-width': 'none',
                position: 'absolute',
                top: thumbY + 'px',
                width: thumbWidth + 'px'
            });
        },

        remove: function() {
            var self = this;
            var imageUuids = self.commonStorage.eventTriggers.get(
                'imageControllerUuids'
            ).slice(0);

            // TODO: check if we need to set delete inputs

            self.anselCropController.destroy();

            self.$image.find('.JSAnselField__ImageIconRemove').off(
                'click.' + self.uuid
            );

            self.commonStorage.eventTriggers.offChange(
                'orderChange.' + self.uuid
            );

            self.model.offChange('isOverAllowed.' + self.uuid);

            self.commonStorage.eventTriggers.offChange(
                'activeCover.' + self.uuid
            );

            self.commonStorage.eventTriggers.offChange(
                'openFieldEditor.' + self.uuid
            );

            self.$image.find('.JSAnselField__ImageIconEdit').off(
                'click.' + self.uuid
            );

            self.commonStorage.sorter.removeItems(self.$image);

            self.$image.remove();

            self.$imageTag = null;
            self.$image = null;
            self.$fieldsModal = null;
            self.base64Image = null;
            self.cacheFile = null;
            self.fileName = null;
            self.uuid = null;
            self.model = null;

            imageUuids.splice(imageUuids.indexOf(self.uuid), 1);

            self.commonStorage.eventTriggers.set(
                'imageControllerUuids',
                imageUuids
            );
        },

        processImageTimer: 0,

        processImage: function() {
            var self = this;

            clearTimeout(self.processImageTimer);

            self.processImageTimer = setTimeout(function() {
                F.AnselGlobalImageQueue[self.uuid] = {
                    controller: self,
                    coords: self.model.get('coords'),
                    fileLocation: self.cacheFile, // TODO: use the appropriate source here
                    fileLocationType: 'cacheFile',
                    quality: self.commonStorage.sharedModel.get('quality'),
                    maxWidth: self.commonStorage.sharedModel.get('maxWidth'),
                    maxHeight: self.commonStorage.sharedModel.get('maxHeight'),
                    forceJpg: self.commonStorage.sharedModel.get('forceJpg')
                };
            }, 500);
        },

        processImageCallback: function(json) {
            var self = this;

            self.$image.find('.JSAnselField__Input--PreFileLocation').val(
                json.model.fileLocation
            );

            self.$image.find('.JSAnselField__Input--PreFileLocationType').val(
                json.model.fileLocationType
            );

            self.$image.find('.JSAnselField__Input--PreH').val(
                json.model.h
            );

            self.$image.find('.JSAnselField__Input--PreW').val(
                json.model.w
            );

            self.$image.find('.JSAnselField__Input--PreX').val(
                json.model.x
            );

            self.$image.find('.JSAnselField__Input--PreY').val(
                json.model.y
            );

            self.$image.find('.JSAnselField__Input--PreHighQualityImgCacheLocation').val(
                json.model.highQualityImgCacheLocation
            );

            self.$image.find('.JSAnselField__Input--PreStandardImgCacheLocation').val(
                json.model.standardImgCacheLocation
            );

            self.$image.find('.JSAnselField__Input--PreThumbImgCacheLocation').val(
                json.model.thumbImgCacheLocation
            );

            self.$image.find('.JSAnselField__Input--PreMaxHeight').val(
                json.model.maxHeight
            );

            self.$image.find('.JSAnselField__Input--PreMaxWidth').val(
                json.model.maxWidth
            );
        }
    });
}

runImage(window.ANSEL);
