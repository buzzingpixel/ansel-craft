window.ANSEL = window.ANSEL || {};

function runCraftFileChooser(F) {
    'use strict';

    if (! window.jQuery || ! F.controller || ! F.model) {
        setTimeout(function() {
            runCraftFileChooser(F);
        }, 10);
        return;
    }

    F.controller.make('CraftFileChooser', {
        commonStorage: {},

        events: {
            click: function() {
                this.openModal();
            }
        },

        openModal: function() {
            var self = this;
            var modal;

            modal = window.Craft.createElementSelectorModal('craft\\elements\\Asset', {
                criteria: {
                    kind: 'image'
                },
                multiSelect: true,
                sources: [
                    'folder:' + self.commonStorage.sharedModel.get('uploadFolderId')
                ],
                onSelect: function(files) {
                    $('.modal-shade').remove();

                    modal.destroy();

                    for (var i in files) {
                        if (! files.hasOwnProperty(i)) {
                            continue;
                        }

                        self.addFile(files[i]);
                    }
                }
            });
        },

        addFile: function(file) {
            var self = this;
            var img = new Image();
            var minWidth = self.commonStorage.sharedModel.get('minWidth');
            var minHeight = self.commonStorage.sharedModel.get('minHeight');

            img.onload = function() {
                var message = '';
                var $img;
                var $imgTag;

                if ((minWidth && img.width < minWidth) ||
                    (minHeight && img.height < minHeight)
                ) {
                    if (minWidth && minHeight) {
                        message = 'Field requires images to be at least ' + minWidth + 'px wide and ' + minHeight + 'px tall.';
                    } else if (minWidth) {
                        message = 'Field requires images to be at least ' + minWidth + 'px wide.';
                    } else if (minHeight) {
                        message = 'Field requires images to be at least ' + minHeight + 'px tall.';
                    }

                    F.controller.construct('Notification', {
                        commonStorage: self.commonStorage,
                        error: true,
                        message: message,
                        destroyEvents: ['dragStart', 'imageControllerUuids']
                    });

                    return;
                }

                $img = $(self.commonStorage.imageTemplate);
                $imgTag = $img.find('.JSAnselField__ImageTag');

                $img.find('.JSAnselField__Input--OriginalAssetId').val(file.id);

                $imgTag.on('load', function() {
                    setTimeout(function() {
                        F.controller.construct('Image', {
                            commonStorage: self.commonStorage,
                            insertImage: true,
                            $image: $img
                        });
                    }, 100);
                });

                $img.data('originalAssetId', file.id);
                $imgTag.attr('src', file.url);
            };

            img.src = file.url;
        }
    });
}

runCraftFileChooser(window.ANSEL);
