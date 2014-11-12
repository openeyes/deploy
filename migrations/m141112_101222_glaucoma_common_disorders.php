<?php

class m141112_101222_glaucoma_common_disorders extends CDbMigration
{
	public function up()
	{
		$base_path = Yii::app()->basePath;
		$data_file = Yii::app()->basePath."/modules/deploy/data/g21d.csv";

		shell_exec("$base_path/yiic g21 ".escapeshellarg($data_file)." 1>&2");
	}

	public function down()
	{
	}
}
