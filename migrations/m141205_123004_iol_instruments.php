<?php

class m141205_123004_iol_instruments extends CDbMigration
{
	public $map = array(
		'ORAcc' => 'ORA IOPcc',
		'Palp' => 'Palpation',
		'DCT' => 'Dynamic Contour Tonometry',
		'GAT' => 'Goldmann',
		'Perk' => 'Perkins',
	);

	public function up()
	{
		$this->insert('ophciexamination_instrument',array('name'=>'ORA IOPg','short_name'=>'ORAg','display_order'=>65,'active'=>1));

		foreach ($this->map as $short_name => $name) {
			$this->update('ophciexamination_instrument',array('short_name' => $short_name),"name = '$name'");
		}
	}

	public function down()
	{
		$this->delete('ophciexamination_instrument',"name = 'ORA IOPg'");

		foreach ($this->map as $short_name => $name) {
			$this->update('ophciexamination_instrument',array('short_name' => null),"name = '$name'");
		}
	}
}
