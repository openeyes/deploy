<?php

class m141112_101222_glaucoma_common_disorders extends CDbMigration
{
	public function up()
	{
		$data_file = Yii::app()->basePath."/modules/deploy/data/g21d.csv";

		$g21 = new G21CommonDisorders;
		$g21->import($data_file);
	}

	public function down()
	{
	}
}
