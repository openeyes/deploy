<?php

class m141205_103537_rename_acute_angle_closure_disorder extends CDbMigration
{
	public function up()
	{
		$this->update('disorder',array('term' => 'Acute angle-closure'),"id = 30041005");
	}

	public function down()
	{
		$this->update('disorder',array('term' => 'Acute angle-closure glaucoma'),"id = 30041005");
	}
}
