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
        notificationChange: 'int'
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
        requireTitle: 'int',
        titleLabel: 'string',
        showCaption: 'bool',
        requireCaption: 'bool',
        captionLabel: 'string',
        showCover: 'bool',
        requireCover: 'bool',
        coverLabel: 'string'
    });

    F.controller.make('Field', {
        sharedModel: null,
        eventTriggers: null,

        commonStorage: {
            sorter: null
        },

        init: function() {
            var self = this;
            var commonObjSend;

            self.eventTriggers = new EventTriggers();

            commonObjSend = {
                el: self.$el,
                sharedModel: self.populateSharedModel(),
                eventTriggers: self.eventTriggers
            };

            commonObjSend.commonObjSend = commonObjSend;

            self.initSorting();

            F.controller.construct('Notifications', commonObjSend);
            F.controller.construct('FieldDropUploader', commonObjSend);
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

            self.sharedModel = new SharedModelConstructor(settingsObj);

            return self.sharedModel;
        },

        initSorting: function() {
            var self = this;

            self.commonStorage.sorter = new window.Garnish.DragSort({
                container: self.$el.find('.JSAnselField__ImagesHolder'),
                axis: null,
                collapseDraggees: true,
                magnetStrength: 4,
                helperLagBase: 1.5,
                helperOpacity: 0.6,
                onSortChange: function() {
                    // TODO: make this work
                    console.log('onSortChange');
                    // Trigger a generic field change event
                    // self.sharedModel.set(
                    //     'fieldChangeEvent',
                    //     self.sharedModel.get('fieldChangeEvent') + 1
                    // );
                },
                onDragStop: function() {
                    // TODO: make this work
                    console.log('onDragStop');
                    // Trigger a generic field change event
                    // self.sharedModel.set(
                    //     'fieldChangeEvent',
                    //     self.sharedModel.get('fieldChangeEvent') + 1
                    // );
                }
            });

            // TODO: remove this and handle this from each row's controller
            self.$el.find('.JSAnselField__ImagesHolder').find('.JSAnselField__Image').each(function() {
                self.commonStorage.sorter.addItems($(this));
            });
        }
    });
}

runField(window.ANSEL);
