<?php
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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2002-2008 (update and modification) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
namespace oat\taoGroups\controller\form;

/**
 * An Instance form dedicated to display/retrieve data about
 * a Group resource.
 *
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 */
class Group extends \tao_actions_form_Instance
{

    /**
     * @see tao_actions_form_Users::initElements()
     */
    public function initElements()
    {
        parent::initElements();
		$this->form->removeElement(\tao_helpers_Uri::encode(TAO_GROUP_MEMBERS_PROP));
    }

} 

?>