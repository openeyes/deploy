<?php

class m141111_090523_dilation_drugs_order extends CDbMigration
{
	public function up()
	{
		foreach (array(5,6,4,2,3,1) as $i => $id) {
			$d = \OEModule\OphCiExamination\models\OphCiExamination_Dilation_Drugs::model()->findByPk($id);

			$d->display_order = $i;

			if (!$d->save()) {
				throw new Exception("Unable to save OphCiExamination_Dilation_Drugs: ".print_r($d->errors,true));
			}
		}
	}

	public function down()
	{
	}
}
