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
 * Copyright (c) 2017-2019  (original work) Open Assessment Technologies SA;
 *
 * @author Alexander Zagovorichev <zagovorichev@1pt.com>
 */

define(['jquery', 'i18n', 'util/url', 'core/promise', 'core/request'], function ($, __, urlUtil, Promise, coreRequest) {
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
             * @param {String} [config.signature] - id signature received from BE, required
             * @param {String} [config.type] - Type of the instance
             * @return {*}
             */
            addInstance (config) {

                const _defaults = {
                    classUri: 'http_2_www_0_tao_0_lu_1_Ontologies_1_TAOGroup_0_rdf_3_Group',
                    id: 'http://www.tao.lu/Ontologies/TAOGroup.rdf#Group',
                    type: 'instance'
                };

                config = Object.assign({}, _defaults, config);

                return new Promise((resolve, reject) => {
                        coreRequest({
                            url: urlUtil.route('addInstance', 'Groups', 'taoGroups'),
                            method: 'POST',
                            data: config,
                            dataType: 'json',
                        })
                        .then(function (group) {
                            resolve(group);
                        })
                        .catch(function () {
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
            deleteGroup (uri) {

                return new Promise((resolve, reject) => {

                    if (typeof uri !== 'string' || uri.trim() === '') {
                        return reject(new TypeError(__('Group uri is not valid')));
                    }

                    coreRequest({
                        url: urlUtil.route('delete', 'Groups', 'taoGroups'),
                        method: 'POST',
                        data: {uri: uri},
                        dataType: 'json',
                    })
                    .then(function (response) {
                        resolve(response);
                    })
                    .catch(function () {
                        reject(new Error(__('Unable to delete group')));
                    });
                });
            }
        };
    };
});
