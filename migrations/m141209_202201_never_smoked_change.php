<?php

class m141209_202201_never_smoked_change extends CDbMigration
{
	public function up()
	{
		if ($this->dbConnection->createCommand()->select("*")->from("socialhistory_smoking_status")->where("name = :n and active = :active",array(":n" => "Never smoked tobacco", ":active" => true))->queryRow()) {
			$this->update('socialhistory_smoking_status', array('active' => 'false'), 'name = "Never smoked tobacco"');
		}

		if (!$this->dbConnection->createCommand()->select("*")->from("socialhistory_smoking_status")->where("name = :n",array(":n" => "Never smoked"))->queryRow()) {
			$this->insert('socialhistory_smoking_status', array('name' => 'Never smoked', 'display_order' => 5));
			$this->update('socialhistory_smoking_status', array('display_order' => 6), 'name = "Unknown"');
		}
	}

	public function down()
	{
		return true;
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}