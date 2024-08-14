'use strict';

const {createElement} = wp.element;
const {registerBlockType} = wp.blocks;
const {InspectorControls} = wp.blockEditor;
const {serverSideRender: ServerSideRender} = wp;
const {PanelBody, SelectControl, ToggleControl, TextControl, RadioControl, Placeholder} = wp.components;

const SWPFIcon = <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 61.6 61.5"><g><path d="m47.2 12.2-4.6 2-11.5-5.3h-.4l-11.5 5.2-4.6-2.1 16.3-7.5ZM24.8 17l-.4.4v5l-4.8 2.2V15l10.7-4.9v4.3Zm17.3 2.4-10.7-4.9v-4.3l10.6 5Zm-28.4 8.5v-15l4.8 2.2v10.4l.3.4L42 36.7l.1 9.6-4.9-2.2v-5l-.3-.5ZM47.9 13v4.2l-4.8 2.2-.1-4.2Zm-5.4 22.7L20.3 25.6l4.6-2.1 22.3 10.2Zm5.4 13.8-16.6 7.6v-4.2l11.4-5.3.4-.4V36.7l4.8-2.2Zm-8.8-1.3-8.2 3.7-16.3-7.5 4.6-2.1 11.5 5.3h.4l5.6-2.6 4.6 2.1Zm-25.4 1.3v-4.3l16.6 7.6V57ZM0 55.4V6.3a6 6 0 0 1 1.8-4.4A6 6 0 0 1 6.3 0h49.1a6 6 0 0 1 4.4 1.8 6 6 0 0 1 1.8 4.4v49.1a6.15 6.15 0 0 1-6.2 6.2H6.3a6 6 0 0 1-4.4-1.8A5.91 5.91 0 0 1 0 55.4Zm58.7 3.3a4.53 4.53 0 0 0 1.4-3.3V6.3A4.53 4.53 0 0 0 58.7 3a4.53 4.53 0 0 0-3.3-1.4H6.3A4.18 4.18 0 0 0 3 3a4.71 4.71 0 0 0-1.4 3.3v49.1A4.53 4.53 0 0 0 3 58.7a4.53 4.53 0 0 0 3.3 1.4h49.1a4.53 4.53 0 0 0 3.3-1.4Z"/></g></svg>

registerBlockType('swpf/filter-selector', {
    title: swpf_block_data.i18n.title,
    icon: SWPFIcon,
    category: 'widgets',
    keywords: swpf_block_data.i18n.filter_keywords,
    description: swpf_block_data.i18n.description,
    attributes: {
        filterId: {
            type: 'string',
        },
    },

    edit(props) {
        const {attributes: {filterId = ''}, setAttributes} = props;
        const filterOptions = Object.entries(swpf_block_data.filters).map(value => ({
                value: value[0],
                label: value[1]
            }));
        let jsx;

        filterOptions.unshift({
            value: '',
            label: swpf_block_data.i18n.filter_select
        });

        function selectFilter(value) {
            setAttributes({filterId: value});
        }


        jsx = [
            <InspectorControls key="swpf-selector-inspector-controls">
                <PanelBody title={swpf_block_data.i18n.filter_settings}>
                    <SelectControl
                        label={swpf_block_data.i18n.filter_selected}
                        value={filterId}
                        options={filterOptions}
                        onChange={selectFilter}
                        />
                </PanelBody>
            </InspectorControls>
        ];

        jsx.push(
            <Placeholder
                key="swpf-selector-wrap"
                icon={SWPFIcon}
                instructions={swpf_block_data.i18n.title}
                className="swpf-gutenberg-filter-selector-wrap">
                <SelectControl
                    key="swpf-selector-select-control"
                    value={filterId}
                    options={filterOptions}
                    onChange={selectFilter}
                    />
            </Placeholder>
            );
        return jsx;
    },
    save() {
        return null;
    },
});
