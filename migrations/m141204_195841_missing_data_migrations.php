<?php

class m141204_195841_missing_data_migrations extends CDbMigration
{
	public function up()
	{
		//OES-489
		$this->update('socialhistory_occupation', array('name' => 'Disability Benefits'), 'name = "Sickness"');
		//OE-4616
		$this->update('socialhistory_smoking_status', array('name' => 'Unknown'), 'name = "Tobacco smoking consumption unknown"');
		//OES-490
		$this->update('socialhistory_driving_status', array('name' => 'Car/Motorcycle'), 'name = "Motor Vehicle"');
		$this->update('socialhistory_driving_status', array('name' => 'HGV/Bus Drive'), 'name = "HGV"');
		//OES-491
		$this->update('ophciexamination_instrument', array('name' => 'ORA IOPcc'), 'name = "ORA"');
		//OES-495
		$this->update('ophciexamination_overallperiod', array('active' => false), 'name = "Discharge"');
		$this->insert('ophciexamination_overallperiod', array('name' => 'Not required', 'display_order' => 0));
		$this->insert('ophciexamination_overallperiod', array('name' => '18 months', 'display_order' => 8));
	}

	public function down()
	{
		$this->update('socialhistory_occupation', array('name' => 'Sickness'), 'name = "Disability Benefits"');
		$this->update('socialhistory_smoking_status', array('name' => 'Tobacco smoking consumption unknown'), 'name = "Unknown"');
		$this->update('socialhistory_driving_status', array('name' => 'Motor Vehicle'), 'name = "Car/Motorcycle"');
		$this->update('socialhistory_driving_status', array('name' => 'HGV'), 'name = "HGV/Bus Drive"');
		$this->update('ophciexamination_instrument', array('name' => 'ORA'), 'name = "ORA IOPcc"');
		$this->update('ophciexamination_overallperiod', array('active' => true), 'name = "Discharge"');
		$this->delete('ophciexamination_overallperiod', 'name = ?', 'Not required');
		$this->delete('ophciexamination_overallperiod', 'name = ?', '18 months');
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