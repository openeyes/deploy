<?php

class m141205_111519_vc_config extends CDbMigration
{
	public function up()
	{
		$this->insert('patientticketing_queue',array(
			'id' => 1,
			'name' => 'Clinical Review',
			'description' => 'Virtual clinic for Glaucoma review',
			'is_initial' => true,
			'assignment_fields' => '[{"id":"source","required":true,"choices":{"1":"Technician Screening 1A","2":"Technician Stable 1A"},"label":"Referral Source"}]',
			'report_definition' => '[epd]<br />[pt_source] [glr]<br />[iot]',
			'action_label' => 'Review Patient',
		));
		$this->insert('patientticketing_queue',array(
			'id' => 2,
			'name' => 'Clinic Reviewed',
			'description' => 'Glaucoma Virtual clinic administration phase',
			'is_initial' => false,
			'assignment_fields' => '[{"id":"glreview", "type":"widget", "widget_name": "TicketAssignOutcome"}]',
			'report_definition' => '[pt_glreview]',
			'action_label' => 'Close Referral',
		));
		$this->insert('patientticketing_queue',array(
			'id' => 3,
			'name' => 'Complete',
			'description' => 'Glaucoma Virtual clinic completed',
			'is_initial' => false,
			'assignment_fields' => '[{"id":"glreview", "type":"widget", "widget_name": "TicketAssignAppointment"}]',
			'report_definition' => '[pt_glreview_appointment]',
		));

		$this->insert('patientticketing_queueset',array('id'=>1,'name'=>'Glaucoma Virtual Clinic','description'=>'Virtual Clinic for Glaucoma patient review','category_id'=>1,'initial_queue_id'=>1,'summary_link'=>true,'allow_null_priority'=>true));
		$this->insert('patientticketing_queueoutcome',array('id'=>1,'queue_id'=>1,'outcome_queue_id'=>2,'display_order'=>1));
		$this->insert('patientticketing_queueoutcome',array('id'=>2,'queue_id'=>2,'outcome_queue_id'=>3,'display_order'=>1));
		$this->insert('ophciexamination_clinicoutcome_status',array('name'=>'Refer to Virtual Clinic','display_order'=>3,'patientticket'=>true));
	}

	public function down()
	{
		$this->delete('ophciexamination_clinicoutcome_status',"name = 'Refer to Virtual Clinic'");
		$this->delete('patientticketing_queueoutcome',"id in (1,2)");
		$this->delete('patientticketing_queueset',"id in (1,2)");
		$this->delete('patientticketing_queue',"id in (1,2,3)");
	}
}
