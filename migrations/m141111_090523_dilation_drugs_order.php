<?php

class m141111_090523_dilation_drugs_order extends CDbMigration
{
	public function up()
	{
		foreach (array(5,6,4,2,3,1) as $i => $id) {
			$this->dbConnection->createCommand("update ophciexamination_dilation_drugs set display_order = $i where id = $id")->query();
		}
	}

	public function down()
	{
	}
}
