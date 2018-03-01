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

        init: function() {
            var self = this;

            // TODO: detect what methods should be used to set up this image
            // TODO: base64? Existing src etc.

            self.$image = $(self.commonStorage.imageTemplate);

            self.$imageTag = self.$image.find('.JSAnselField__ImageTag');

            self.$imageTag.attr('src', self.base64Image);

            self.commonStorage.$imagesHolder.append(self.$image);

            self.commonStorage.sorter.addItems(self.$image);

            self.initFieldEditor();
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
            var $cancel = self.$fieldsModal.find('.AnselField__FieldsModalCancel');
            var $save = self.$fieldsModal.find('.AnselField__FieldsModalSave');
            var $prev = self.$fieldsModal.find('.AnselField__FieldsModalPrev');
            var $next = self.$fieldsModal.find('.AnselField__FieldsModalNext');
            var $img = self.$fieldsModal.find('.JSAnselField__FieldsModalPreviewImageTag');
            var $heading = self.$fieldsModal.find('.JSAnselField__FieldsModalBodyHeading');
            var modal = new window.Garnish.Modal(self.$fieldsModal);

            $img.attr('src', self.$imageTag.attr('src'));

            $heading.text(self.fileName);

            self.$fieldsModal.find('.lightswitch').lightswitch();

            // TODO: hide prev/next image buttons as required

            function closeEditor() {
                if (! modal) {
                    return;
                }

                modal.hide();
                modal.destroy();
                modal = null;
            }

            $cancel.on('click', function() {
                closeEditor();
            });

            $save.on('click', function() {
                // TODO: Do actual saving functionality
                closeEditor();
            });

            $prev.on('click', function() {
                // TODO: Do saving and then show the modal for the previous image
                closeEditor();
            });

            $next.on('click', function() {
                // TODO: Do saving and then show the modal for the next image
                closeEditor();
            });
        }
    });
}

runImage(window.ANSEL);
