<?php

class m141113_122742_sphere_and_cylinder_ranges extends CDbMigration
{
	public function up()
	{
		$this->set_range('sphere',45,30);
		$this->set_range('cylinder',25,25);
	}

	public function down()
	{
		$this->set_range('sphere',20,20);
		$this->set_range('cylinder',20,20);
	}

	public function set_range($type, $from_negative, $to_positive)
	{
		$this->dbConnection->createCommand("delete from ophciexamination_refraction_{$type}_integer")->query();

		for ($i=0; $i<=$to_positive; $i++) {
			$this->insert("ophciexamination_refraction_{$type}_integer",array(
				'value' => $i,
				'display_order' => $i+1,
				'sign_id' => 1,
			));
		}

		for ($i=0; $i<=$from_negative;$i++) {
			$this->insert("ophciexamination_refraction_{$type}_integer",array(
				'value' => $i,
				'display_order' => $i+1,
				'sign_id' => 2,
			));
		}
	}
}
