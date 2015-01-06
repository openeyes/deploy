<?php

class m141205_123004_iol_instruments extends CDbMigration
{
	public $map = array(
		'ORAcc' => 'ORA IOPcc',
		'ORAg' => 'ORA IOPg',
		'Palp' => 'Palpation',
		'DCT' => 'Dynamic Contour Tonometry',
		'GAT' => 'Goldmann',
		'Perk' => 'Perkins',
	);

	public function up()
	{
		foreach ($this->map as $short_name => $name) {
			$this->update('ophciexamination_instrument',array('short_name' => $short_name),"name = '$name'");
		}
	}

	public function down()
	{
		foreach ($this->map as $short_name => $name) {
			$this->update('ophciexamination_instrument',array('short_name' => null),"name = '$name'");
		}
	}
}
