<?php

class m141204_195841_missing_data_migrations extends CDbMigration
{
	public function up()
	{
		//OES-489
		if ($this->dbConnection->createCommand()->select("*")->from("socialhistory_occupation")->where("name = :n",array(":n" => "Sickness"))->queryRow()) {
			$this->update('socialhistory_occupation', array('name' => 'Disability Benefits'), 'name = "Sickness"');
		}

		//OE-4616
		if ($this->dbConnection->createCommand()->select("*")->from("socialhistory_smoking_status")->where("name = :n",array(":n" => "Tobacco smoking consumption unknown"))->queryRow()) {
			$this->update('socialhistory_smoking_status', array('name' => 'Unknown'), 'name = "Tobacco smoking consumption unknown"');
		}

		//OES-490
		if ($this->dbConnection->createCommand()->select("*")->from("socialhistory_driving_status")->where("name = :n",array(":n" => "Motor vehicle"))->queryRow()) {
			$this->update('socialhistory_driving_status', array('name' => 'Car/Motorcycle'), 'name = "Motor vehicle"');
		}

		if ($this->dbConnection->createCommand()->select("*")->from("socialhistory_driving_status")->where("name = :n",array(":n" => "HGV"))->queryRow()) {
			$this->update('socialhistory_driving_status', array('name' => 'HGV/Bus Driver'), 'name = "HGV"');
		}

		//OES-491
		if ($this->dbConnection->createCommand()->select("*")->from("ophciexamination_instrument")->where("name = :n",array(":n" => "ORA"))->queryRow()) {
			$this->update('ophciexamination_instrument', array('name' => 'ORA IOPcc'), 'name = "ORA"');
		}

		//OES-495
		$this->update('ophciexamination_overallperiod', array('active' => false), 'name = "Discharge"');
		if (!$this->dbConnection->createCommand()->select("*")->from("ophciexamination_overallperiod")->where("name = :n",array(":n" => "Not required"))->queryRow()) {
			$this->insert('ophciexamination_overallperiod', array('name' => 'Not required', 'display_order' => 0));
		}
		if (!$this->dbConnection->createCommand()->select("*")->from("ophciexamination_overallperiod")->where("name = :n",array(":n" => "18 months"))->queryRow()) {
			$this->insert('ophciexamination_overallperiod', array('name' => '18 months', 'display_order' => 8));
		}
	}

	public function down()
	{
		$this->update('socialhistory_occupation', array('name' => 'Sickness'), 'name = "Disability Benefits"');
		$this->update('socialhistory_smoking_status', array('name' => 'Tobacco smoking consumption unknown'), 'name = "Unknown"');
		$this->update('socialhistory_driving_status', array('name' => 'Motor vehicle'), 'name = "Car/Motorcycle"');
		$this->update('socialhistory_driving_status', array('name' => 'HGV'), 'name = "HGV/Bus Drive"');
		$this->update('ophciexamination_instrument', array('name' => 'ORA'), 'name = "ORA IOPcc"');
		$this->update('ophciexamination_overallperiod', array('active' => true), 'name = "Discharge"');
		$this->delete('ophciexamination_overallperiod', 'name = ?', array('Not required'));
		$this->delete('ophciexamination_overallperiod', 'name = ?', array('18 months'));
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