<?php

class m141111_090347_previous_operations extends CDbMigration
{
	public function up()
	{
		foreach (CommonPreviousOperation::model()->findAll(array('order' => 'name asc')) as $i => $cpo) {
			$cpo->display_order = $i;

			if (!$cpo->save()) {
				throw new Exception("Unable to save CommonPreviousOperation: ".print_r($cpo->errors,true));
			}
		}
	}

	public function down()
	{
	}
}
