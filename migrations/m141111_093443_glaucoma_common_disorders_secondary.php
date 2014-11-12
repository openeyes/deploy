<?php

class m141111_093443_glaucoma_common_disorders_secondary extends CDbMigration
{
	public function up()
	{
		$data_file = Yii::app()->basePath."/modules/deploy/data/oe-4745-v2.csv";

		$si = new SecondaryToImport;
		$si->import($data_file,true);
	}

	public function down()
	{
	}
}
