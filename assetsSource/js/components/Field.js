window.ANSEL = window.ANSEL || {};

function runField(F) {
    'use strict';

    var EventTriggers;
    var SharedModelConstructor;

    if (! window.jQuery || ! F.controller || ! F.model) {
        setTimeout(function() {
            runField(F);
        }, 10);
        return;
    }

    EventTriggers = F.model.make({
        dragStart: 'int',
        dragEnd: 'int',
        drop: 'int',
        uploadStart: 'int',
        uploadComplete: 'int',
        notificationChange: 'int',
        activeCover: 'string',
        openFieldEditor: 'string',
        orderChange: 'int',
        imageControllerUuids: 'array',
        modalClosed: 'int',
        imageProcessingQueue: 'int',
        imageProcessingInProgress: 'bool'
    });

    SharedModelConstructor = F.model.make({
        uploadKey: 'string',
        uploadActionUrl: 'string',
        csrfToken: 'string',
        elementId: 'int',
        fieldId: 'int',
        fieldName: 'string',
        uploadLocation: 'int',
        uploadFolderId: 'int',
        saveLocation: 'int',
        saveFolderId: 'int',
        minQty: 'int',
        maxQty: 'int',
        preventUploadOverMax: 'bool',
        quality: 'int',
        forceJpg: 'bool',
        retinaMode: 'bool',
        minWidth: 'int',
        minHeight: 'int',
        maxWidth: 'int',
        maxHeight: 'int',
        ratio: 'string',
        ratioWidth: 'int',
        ratioHeight: 'int',
        showTitle: 'bool',
        requireTitle: 'bool',
        titleLabel: 'string',
        showCaption: 'bool',
        requireCaption: 'bool',
        captionLabel: 'string',
        showCover: 'bool',
        requireCover: 'bool',
        coverLabel: 'string'
    });

    F.controller.make('Field', {
        commonStorage: {
            el: null,
            $el: null,
            sharedModel: null,
            eventTriggers: null,
            sorter: null,
            imageTemplate: null,
            $imagesHolder: null,
            $notificationList: null
        },

        init: function() {
            var self = this;
            var sendObj;

            self.setUp();

            sendObj = {
                commonStorage: self.commonStorage
            };

            self.commonStorage.eventTriggers.onChange('imageProcessingInProgress', function(val) {
                if (! val) {
                    return;
                }

                F.controller.construct('Notification', {
                    commonStorage: self.commonStorage,
                    heading: 'Image Processing',
                    message: 'This field is currently processing images...',
                    destroyEvents: ['imageProcessingInProgress']
                });
            });

            self.commonStorage.eventTriggers.onChange('imageProcessingQueue', function(val) {
                if (val) {
                    self.commonStorage.eventTriggers.set('imageProcessingInProgress', true);
                    return;
                }

                self.commonStorage.eventTriggers.set('imageProcessingInProgress', false);
            });

            F.controller.construct('Notifications', sendObj);
            F.controller.construct('FieldDropUploader', sendObj);
            F.controller.construct('MaxTracker', sendObj);
            F.controller.construct('MinTracker', sendObj);

            F.controller.construct('CraftFileChooser', {
                el: self.$el.find('.JSAnselField__SelectImageButton').get(0),
                commonStorage: self.commonStorage
            });

            self.$el.find('.JSAnselField__Image').each(function() {
                F.controller.construct('Image', {
                    commonStorage: self.commonStorage,
                    $image: $(this)
                });
            });
        },

        setUp: function() {
            var self = this;
            var tmpl = self.$el.find('.JSAnselField__ImageTemplate').html();
            var $imageHolder = self.$el.find('.JSAnselField__ImagesHolder');

            self.commonStorage = {
                el: self.el,
                $el: self.$el,
                sharedModel: self.populateSharedModel(),
                eventTriggers: new EventTriggers(),
                sorter: self.setUpSorting($imageHolder),
                imageTemplate: tmpl,
                $imagesHolder: $imageHolder,
                $notificationList: self.$el.find('.JSAnselField__Notifications'),
                fieldsModalHtml: self.$el.find('.JSAnselField__ModalTemplate').html(),
                cropTableTemplate: self.$el.find('.JSAnselField__CropTableTemplate').html(),
                focalPointTableTemplate: self.$el.find('.JSAnselField__FocalPointTableTemplate').html(),
                translateActionUrl: self.$el.data('translateActionUrl')
            };
        },

        populateSharedModel: function() {
            var self = this;
            var rawSettings = self.$el.data('settings');
            var settingsObj = {
                uploadKey: self.$el.data('uploadKey'),
                uploadActionUrl: self.$el.data('uploadActionUrl'),
                csrfToken: self.$el.data('csrfToken')
            };

            for (var i in rawSettings) {
                if (! rawSettings.hasOwnProperty(i)) {
                    continue;
                }

                settingsObj[i] = rawSettings[i];
            }

            return new SharedModelConstructor(settingsObj);
        },

        setUpSorting: function($imageHolder) {
            var self = this;

            return new window.Garnish.DragSort({
                container: $imageHolder,
                axis: null,
                collapseDraggees: true,
                magnetStrength: 4,
                helperLagBase: 1.5,
                helperOpacity: 0.6,
                onSortChange: function() {
                    self.commonStorage.eventTriggers.set(
                        'orderChange',
                        self.commonStorage.eventTriggers.get('orderChange') + 1
                    );
                },
                onDragStop: function() {
                    self.commonStorage.eventTriggers.set(
                        'orderChange',
                        self.commonStorage.eventTriggers.get('orderChange') + 1
                    );
                }
            });
        }
    });
}

runField(window.ANSEL);
