<?php

class m141202_115701_relabel_gl_disorders extends CDbMigration
{
	public function up()
	{
		foreach (array(
			'44219007' => 'Pseudoexfoliation',
			'417746004' => 'Trauma') as $snomed => $term) {
			$this->update('disorder',array('term' => $term),"id = $snomed");
		}

	}

	public function down()
	{
		foreach (array(
			'44219007' => 'Pseudoexfoliation of lens capsule',
			'417746004' => 'Traumatic injury',
		 ) as $snomed => $term) {
			$this->update('disorder',array('term' => $term),"id = $snomed");
		}
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