<?php
/**
 * @package    DA Software Co.
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    Proprietary Software
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Class TB_Form_Element_MultiMedia
 */
class TB_Form_Element_MultiMedia extends TB_Form_Element_Hidden
{
    /**
     * @inheritDoc
     */
    protected function getDefaultOptions()
    {
        return array_replace(array(
            // default value
            'default' => array(),

            // filter media type, can be: image, video, audio or empty for any
            'type' => '',

            // strip duplicate items
            'unique' => false,

            // limit up to a certain number of items
            'limit' => PHP_INT_MAX
        ), parent::getDefaultOptions());
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultSerializationFilters()
    {
        return array(
            $this->sm()->create('form.filter.array_map', array(
                array(
                    TB_Form_Filter_ArrayMap::CONFIG_FILTERS => array(
                        $this->sm()->create('form.filter.post_to_property', array(
                            array(
                                TB_Form_Filter_PostToProperty::CONFIG_PROPERTY => 'ID'
                            )
                        ))
                    )
                )
            )),
            $this->sm()->create('form.filter.array_values'),
            $this->sm()->create('form.filter.json')
        );
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultDeserializationFilters()
    {
        return array_merge(array(
            $this->sm()->create('form.filter.json_to_array')
        ), $this->getOption('unique') ? array(
            $this->sm()->create('form.filter.array_unique')
        ) : array(), array(
            $this->sm()->create('form.filter.array_slice', array(
                array(
                    TB_Form_Filter_ArraySlice::CONFIG_LENGTH => $this->getOption('limit')
                )
            )),
            $this->sm()->create('form.filter.array_map', array(
                array(
                    TB_Form_Filter_ArrayMap::CONFIG_REMOVE_ON_EXCEPTION => true,
                    TB_Form_Filter_ArrayMap::CONFIG_FILTERS => array(
                        $this->sm()->create('form.filter.integer'),
                        $this->sm()->create('form.filter.attachment', array(
                            array(
                                TB_Form_Filter_Attachment::CONFIG_THROW_NOT_FOUND => true
                            )
                        ))
                    )
                )
            ))
        ));
    }

    /**
     * @return array
     */
    protected function getLabels()
    {
        return array(
            'modal_title' => __('Select Media', 'tb'),
            'modal_button' => __('Select', 'tb'),
            'remove' => __('Remove', 'tb')
        );
    }

    /**
     * @return array
     */
    protected function getIcons()
    {
        return array(
            'generic' => includes_url('images/media/default.png')
        );
    }

    /**
     * @param WP_Post|null $attachment
     *
     * @return TB_DOM_Tag
     */
    protected function getTemplate(WP_Post $attachment = null)
    {
        // template
        $template = TB_DOM_Tag::nlInnerAfter('div', false, array_merge(array(
            'class' => 'tb-media-item',
            'data-id' => isset($attachment) ? $attachment->ID : 0
        ), isset($attachment) ? array() : array(
            'data-empty' => null
        )))->setIndentation(1);

        // icons
        $icons = $this->getIcons();

        // preview type
        $type = isset($attachment) ? explode('/', $attachment->post_mime_type) : array('generic');
        $type = in_array($type[0], array(
            'image'
        )) ? $type[0] : 'generic';

        // preview attributes
        $preview = array();
        if(isset($attachment)) {
            $preview['title'] = basename(get_attached_file($attachment->ID));

            switch($type) {
                case 'image':
                    $image = wp_get_attachment_image_src($attachment->ID, 'full');
                    $preview['style'] = 'background-image:url(' . $image[0] . ')';
                    break;

                default:
                    $preview['style'] = 'background-image:url(' . $icons['generic'] . ')';
            }
        }

        return $template

            // preview box
            ->append(TB_DOM_Tag::nlInnerAfter('div', false, array_merge(array(
                'class' => 'tb-media-item-preview',
                'data-type' => $type
            ), $preview))->setIndentation(1))

            // remove button
            ->append(TB_DOM_Tag::nlInnerAfter('button', false, array(
                'type' => 'button',
                'class' => 'button tb-media-item-remove'
            ))->setIndentation(1)->text(__('Remove', 'tb')));
    }

    /**
     * @inheritDoc
     */
    public function render(array $parents = array())
    {
        // carrier
        $element = parent::render($parents);
        $carrier = 'input' === $element->getTag() ? $element : $element->findOne('input');

        // parent media element
        $media = TB_DOM_Tag::nlInnerAfter('div', false, array(
            'class' => 'tb-media',
            'data-type' => $this->getOption('type'),
            'data-limit' => $this->getOption('limit'),
            'data-labels' => json_encode($this->getLabels()),
            'data-icons' => json_encode($this->getIcons())
        ))->setIndentation(1)

        // carrier <input>
        ->append($carrier)

        // template
        ->append(TB_DOM_Tag::nlInnerAfter('div', false, array(
            'class' => 'tb-media-template'
        ))->setIndentation(1)->append($this->getTemplate()))

        // collection
        ->append($collection = TB_DOM_Tag::nlInnerAfter('div', false, array(
            'class' => 'tb-media-collection'
        ))->setIndentation(1));

        // fill collection with data
        $data = $this->getValue();

        /** @var WP_Post $attachment */
        foreach($data as $attachment) {
            $collection->append($this->getTemplate($attachment));
        }

        // empty element for adding
        if(count($data) < $this->getOption('limit')) {
            $collection->append($this->getTemplate());
        }

        return $this->decorate(__CLASS__, $media, $parents);
    }
}