<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class G21Command extends CConsoleCommand
{
	public function run($args)
	{
		if (!empty($args)) {
			$file = $args[0];
		} else {
			$file = "/tmp/g21d.csv";
		}

		$fp = fopen($file,"r");

		$i = 0;

		$gla = Subspecialty::model()->find('name=?',array('Glaucoma'));

		if (!Disorder::model()->findByPk(103)) {
			Yii::app()->db->createCommand("insert into disorder `id`,`fully_specified_name`,`term`,`active` values (103,'Glaucoma Suspect (disorder)','Glaucoma Suspect',1)")->query();
		}

		$snomeds = array();

		while ($data = fgetcsv($fp)) {
			$snomed = $data[1];
			$snomeds[] = $snomed;

			if (!$cod = CommonOphthalmicDisorder::model()->find('disorder_id=? and subspecialty_id=?',array($snomed,$gla->id))) {
				$cod = new CommonOphthalmicDisorder;
				$cod->subspecialty_id = $gla->id;
				$cod->disorder_id = $snomed;
			}

			$cod->display_order = $i+2;

			$cod->save();

			$i++;
			if ($i >= 10) break;
		}

		$i = 0;

		$list = array();

		while ($data = fgetcsv($fp)) {
			$snomed = $data[1];
			$snomeds[] = $snomed;

			$list[$data[0]] = $data;

			$i++;
			if ($i >= 10) break;
		}

		ksort($list);

		$i = 0;

		foreach ($list as $data) {
			$snomed = $data[1];

			if (!$cod = CommonOphthalmicDisorder::model()->find('disorder_id=? and subspecialty_id=?',array($snomed,$gla->id))) {
				$cod = new CommonOphthalmicDisorder;
				$cod->subspecialty_id = $gla->id;
				$cod->disorder_id = $snomed;
			}

			$cod->display_order = $i+12;

			$i++;
			$cod->save();
		}
	}
}
