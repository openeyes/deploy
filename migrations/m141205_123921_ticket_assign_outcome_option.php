<?php

class m141205_123921_ticket_assign_outcome_option extends CDbMigration
{
	public function up()
	{
		$this->insert('patientticketing_ticketassignoutcomeoption',array('id'=>1,'name'=>'Discharge','display_order'=>0,'episode_status_id'=>6,'followup'=>0));
		$this->insert('patientticketing_ticketassignoutcomeoption',array('id'=>2,'name'=>'Follow up','display_order'=>1,'episode_status_id'=>5,'followup'=>1));
	}

	public function down()
	{
		$this->delete('patientticketing_ticketassignoutcomeoption',"id=1");
		$this->delete('patientticketing_ticketassignoutcomeoption',"id=2");
	}
}
