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
            isOverAllowed: 'bool'
        },

        init: function() {
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

            // TODO: detect what methods should be used to set up this image
            // TODO: base64? Existing src etc.

            self.$image = $(self.commonStorage.imageTemplate);

            self.$image.data('jsUuid', self.uuid);

            self.$imageTag = self.$image.find('.JSAnselField__ImageTag');

            self.$imageTag.attr('src', self.base64Image);

            self.commonStorage.$imagesHolder.append(self.$image);

            self.commonStorage.sorter.addItems(self.$image);

            self.initFieldEditor();

            self.watchOpenFieldEditor();

            self.watchForCoverChange();

            self.watchForOrderChange();
        },

        initFieldEditor: function() {
            var self = this;
            var $editIcon = self.$image.find('.JSAnselField__ImageIconEdit');

            $editIcon.on('click', function() {
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

            self.commonStorage.eventTriggers.onChange('openFieldEditor', function(val) {
                if (val !== self.uuid) {
                    return;
                }

                setTimeout(function() {
                    self.openFieldEditor();
                }, 50);
            });
        },

        watchForCoverChange: function() {
            var self = this;

            self.commonStorage.eventTriggers.onChange('activeCover', function(val) {
                if (val === self.uuid) {
                    return;
                }

                self.$image.find('.JSAnselField__Input--Cover').val('');
            });
        },

        watchForOrderChange: function() {
            var self = this;

            self.model.onChange('isOverAllowed', function(val) {
                if (val) {
                    self.$image.addClass('AnselField__Image--IsOverMax');
                    return;
                }
                self.$image.removeClass('AnselField__Image--IsOverMax');
            });

            self.commonStorage.eventTriggers.onChange('orderChange', function() {
                var pos = self.$image.prevAll('.JSAnselField__Image').length + 1;
                var maxQty = self.commonStorage.sharedModel.get('maxQty');

                if (! maxQty || pos <= maxQty) {
                    self.model.set('isOverAllowed', false);
                    return;
                }

                self.model.set('isOverAllowed', true);
            });

            self.commonStorage.eventTriggers.set(
                'orderChange',
                self.commonStorage.eventTriggers.set('orderChange') + 1
            );
        }
    });
}

runImage(window.ANSEL);
