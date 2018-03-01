window.ANSEL = window.ANSEL || {};

function runField(F) {
    'use strict';

    var SharedModelConstructor;

    if (! window.jQuery || ! F.controller || ! F.model) {
        setTimeout(function() {
            runField(F);
        }, 10);
        return;
    }

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

        init: function() {
            var self = this;
            var commonObjSend = {
                el: self.$el,
                sharedModel: self.populateSharedModel()
            };

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
        }
    });
}

runField(window.ANSEL);
