<?php

class m141111_090347_previous_operations extends CDbMigration
{
	public function up()
	{
		foreach ($this->dbConnection->createCommand()->select("*")->from("common_previous_operation")->order("name asc")->queryAll() as $i => $cpo) {
			$this->dbConnection->createCommand("update common_previous_operation set display_order = $i where id = {$cpo['id']}")->query();
		}
	}

	public function down()
	{
	}
}
