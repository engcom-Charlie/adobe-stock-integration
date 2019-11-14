/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'uiComponent',
    'underscore'
], function (Component, _) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Magento_AdobeStockImageAdminUi/grid/column/preview/keywords',
            chipsProvider: 'componentType = filtersChips, ns = ${ $.ns }',
            searchChipsProvider: 'componentType = keyword_search, ns = ${ $.ns }',
            defaultKeywordsLimit: 5,
            keywordsLimit: 5,
            canViewMoreKeywords: true,
            modules: {
                searchChips: '${ $.searchChipsProvider }',
                chips: '${ $.chipsProvider }',
                preview: '${ $.parentName }.preview'
            },
            exports: {
                inputValue: '${ $.provider }:params.search',
                chipInputValue: '${ $.searchChipsProvider }:value'
            }
        },

        /**
         * Init observable variables
         * @return {Object}
         */
        initObservable: function () {
            this._super()
                .observe([
                    'keywordsLimit',
                    'canViewMoreKeywords'
                ]);

            return this;
        },

        /**
         * Returns keywords to display under the attributes image
         *
         * @returns {*[]}
         */
        getKeywords: function (record) {
            return record.keywords;
        },

        /**
         * Returns keywords limit to show no of keywords
         */
        getKeywordsLimit: function () {
            return this.keywordsLimit();
        },

        /**
         * Show all the related keywords
         */
        viewAllKeywords: function (record) {
            this.keywordsLimit(record.keywords.length);
            this.canViewMoreKeywords(false);
            this.preview().updateHeight();
            this.preview().scrollToPreview();
        },

        /**
         * Hide all the related keywords
         */
        hideAllKeywords: function () {
            this.keywordsLimit(this.defaultKeywordsLimit);
            this.canViewMoreKeywords(true);
        },

        /**
         * Drop all filters and initiate search on keyword click event
         */
        searchByKeyWord: function (keyword) {
            _.invoke(this.chips().elems(), 'clear');
            this.inputValue(keyword);
            this.chipInputValue(keyword);
        }
    });
});
