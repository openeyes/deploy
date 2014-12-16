<?php

class m141111_093125_disable_other_iop_instrument extends CDbMigration
{
	public function up()
	{
		$this->dbConnection->createCommand("update ophciexamination_instrument set active = 0 where name = 'Other'")->query();
	}

	public function down()
	{
	}
}
