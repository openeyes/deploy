<?php

class m141111_092705_cvi_statuses extends CDbMigration
{
	public function up()
	{
		foreach (array(
				1 => 'Not eligible',
				2 => 'Eligible but not offered',
				3 => 'Eligible but declined',
				4 => 'Sight Impaired',
				5 => 'Severely Sight Impaired',
				6 => 'Unknown',
			) as $display_order => $name) {

			if (!$cs = PatientOphInfoCviStatus::model()->find('name=?',array($name))) {
				$cs = new PatientOphInfoCviStatus;
				$cs->name = $name;
			}

			$cs->display_order = $display_order;
			$cs->active = 1;

			if (!$cs->save()) {
				throw new Exception("Unable to save CVI status item: ".print_r($cs->errors,true));
			}
		}

		if ($cs = PatientOphInfoCviStatus::model()->find('name=?',array('Not Certified'))) {
			$cs->active = 0;

			if (!$cs->save()) {
				throw new Exception("Unable to save CVI status item: ".print_r($cs->errors,true));
			}
		}
	}

	public function down()
	{
	}
}
