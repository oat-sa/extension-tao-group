/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2017  (original work) Open Assessment Technologies SA;
 *
 * @author Alexander Zagovorichev <zagovorichev@1pt.com>
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
             * @param config
             * @param {String} [config.classUri] - rdf uri of the Group for current environment
             * @param {String} [config.id] - id of the Group for current environment
             * @param {String} [config.type] - Type of the instance
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

            /**
             * Group deleting
             *
             * @param uri
             * @return {*}
             */
            deleteGroup: function deleteGroup (uri) {

                return new Promise(function(resolve, reject) {

                    if (!_.isString(uri) || _.isEmpty(uri)) {
                        return reject(new TypeError(__('Group uri is not valid')));
                    }

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
