/**
 * Copyright (c) 2017 Open Assessment Technologies, S.A.
 *
 * @author A.Zagovorichev, <zagovorichev@1pt.com>
 */

define(['jquery', 'lodash', 'i18n', 'util/url', 'core/promise'], function ($, _, __, urlUtil, Promise) {
    'use strict';

    /**
     * Creates a group provider
     *
     * @typedef {Object} groupProvider
     * @type {{addInstance: groupProvider.addInstance}}
     */
    return function groupProviderFactory () {

        /**
         * @returns {groupProvider}
         */
        return {

            /**
             * Create new group
             *
             * @return {*}
             */
            addInstance: function addInstance (config) {

                var _defaults = {
                    classUri: 'http_2_www_0_tao_0_lu_1_Ontologies_1_TAOGroup_0_rdf_3_Group',
                    id: 'http://www.tao.lu/Ontologies/TAOGroup.rdf#Group',
                    type: 'instance'
                };

                config = _.defaults(config || {}, _defaults);

                return new Promise(function(resolve, reject){

                    $.ajax({
                        url: urlUtil.route('addInstance', 'Groups', 'taoGroups'),
                        type: 'post',
                        data: config,
                        dataType: 'json'
                    })
                        .done(function (group) {
                            resolve(group);
                        })
                        .fail(function () {
                            reject(new Error(__('Unable to create new group')));
                        });
                });
            },

            deleteGroup: function deleteGroup (uri) {
                return new Promise(function(resolve, reject) {

                    $.ajax({
                        url: urlUtil.route('delete', 'Groups', 'taoGroups'),
                        type: 'post',
                        data: {uri: uri},
                        dataType: 'json'
                    })
                        .done(function (response) {
                            resolve(response);
                        })
                        .fail(function () {
                            reject(new Error(__('Unable to delete group')));
                        });
                });
            }
        };
    };
});
