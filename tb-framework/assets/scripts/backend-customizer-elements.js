/*
 *  @package    TB Framework
 *  @author     Tony Bogdanov <support@tonybogdanov.com>
 *  @license    MIT http://www.opensource.org/licenses/mit-license.php
 *  @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */
(function($) {
    "use strict";

    $(function() {
        var $body = $('body');

        /**
         * TB\Form\Element\Color
         */
        $('.tb-color-picker').each(function() {
            var $this = $(this);

            $this.wpColorPicker({
                defaultColor: $this.attr('data-default'),
                change: function(e, ui) {
                    $this.val(ui.color.toCSS()).trigger('change');
                }
            });
        });

        /**
         * TB\Form\Element\MultiMedia
         */
        $body

            // open media selection modal
            .on('tb-media-open', '.tb-media-collection .tb-media-item', function() {
                var $item = $(this);
                var $media = $item.parents('.tb-media').first();

                var labels = JSON.parse($media.attr('data-labels'));
                var type = $media.attr('data-type');

                $media.data('tb-media-item', $item);

                var handler = $media.data('tb-media-handler');
                if(handler) {
                    return handler.open();
                }

                var options = {
                    title: labels.modal_title,
                    button: {
                        text: labels.modal_button
                    },
                    multiple: false
                };
                if(0 < type.length) {
                    options.library = {type: type};
                }

                $media.data('tb-media-handler', handler = wp.media(options));

                handler.on('open', function() {
                    var id = parseInt($media.data('tb-media-item').attr('data-id'));

                    if(0 === id) {
                        handler.state().get('selection').add([]);
                        return;
                    }

                    var media = wp.media.attachment(id);
                    media.fetch();

                    handler.state().get('selection').add([media]);
                });
                handler.on('select', function() {
                    $media.data('tb-media-item').trigger('tb-media-select',
                        handler.state().get('selection').first().toJSON());
                });

                handler.open();
            })

            // select media
            .on('tb-media-select', '.tb-media-collection .tb-media-item', function(e, value) {
                var $item = $(this);

                if(false === value) {
                    $item.removeAttr('data-id').attr('data-empty', '');
                } else {
                    var $media = $item.parents('.tb-media').first();
                    var $preview = $item.find('.tb-media-item-preview');

                    var icons = JSON.parse($media.attr('data-icons'));

                    switch(value.type) {
                        case 'image':
                            $preview.attr('data-type', value.type).attr('style', 'background-image:url(' + value.url + ')');
                            break;

                        default:
                            $preview.attr('data-type', 'generic').attr('style', 'background-image:url(' + icons.generic + ')');
                    }

                    $preview.attr('title', value.filename);
                    $item.attr('data-id', value.id).removeAttr('data-empty');

                    $media.trigger('tb-media-add');
                }

                $media.trigger('tb-media-update');
            })

            // remove media element
            .on('tb-media-remove', '.tb-media-collection .tb-media-item', function() {
                var $item = $(this);
                var $media = $item.parents('.tb-media').first();

                $item.remove();
                $media.trigger('tb-media-update').trigger('tb-media-add');
            })

            // add new empty media element
            .on('tb-media-add', '.tb-media', function() {
                var $media = $(this);
                var $items = $media.find('.tb-media-collection .tb-media-item');

                if(
                    0 === $items.filter('[data-empty]').length &&
                    $items.length < parseInt($media.attr('data-limit'))
                ) {
                    $media.find('.tb-media-collection').append($media.find('.tb-media-template').clone().contents().unwrap());
                }
            })

            // update media value
            .on('tb-media-update', '.tb-media', function() {
                var value = [];

                $(this).find('.tb-media-collection .tb-media-item').each(function() {
                    if(this.hasAttribute('data-id')) {
                        value.push(parseInt($(this).attr('data-id')));
                    }
                }).end().find('input').first().val(JSON.stringify(value)).trigger('change');
            })

            // click on: preview box
            .on('click', '.tb-media-item-preview', function(e) {
                e.preventDefault();
                $(this).parents('.tb-media-item').first().trigger('tb-media-open');
            })

            // click on: remove button
            .on('click', '.tb-media-item-remove', function(e) {
                e.preventDefault();
                $(this).parents('.tb-media-item').first().trigger('tb-media-remove');
            });
    });
})(jQuery);