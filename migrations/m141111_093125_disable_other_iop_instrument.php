<?php

class m141111_093125_disable_other_iop_instrument extends CDbMigration
{
	public function up()
	{
		if ($i = \OEModule\OphCiExamination\models\OphCiExamination_Instrument::model()->find('name=?',array('Other'))) {
			$i->active = 0;

			if (!$i->save()) {
				throw new Exception("Unable to save OphCiExamination_Instrument: ".print_r($i->errors,true));
			}
		}
	}

	public function down()
	{
	}
}
