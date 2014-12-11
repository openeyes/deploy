<?php

class m141120_082557_common_ophthalmic_disorder_groups extends CDbMigration
{
	public function up()
	{
		foreach (array(
			'Adult glaucoma diagnoses and findings',
			'Paediatric glaucoma diagnoses and findings',
			'Non glaucoma diagnoses'
			) as $i => $group) {
			$this->insert('common_ophthalmic_disorder_group',array('id' => $i+1, 'name' => $group, 'display_order' => $i));
		}
	}

	public function down()
	{
		$this->delete('common_ophthalmic_disorder_group');
	}
}
