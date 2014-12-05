<?php

class m141205_123413_clinic_reviewed_event_types extends CDbMigration
{
	public function up()
	{
		$queue_id = $this->dbConnection->createCommand()->select("id")->from("patientticketing_queue")->where("name = :name",array(":name" => "Clinic Reviewed"))->queryScalar();
		$prescription_id = $this->dbConnection->createCommand()->select("id")->from("event_type")->where("class_name = :cn",array(":cn" => "OphDrPrescription"))->queryScalar();
		$correspondence_id = $this->dbConnection->createCommand()->select("id")->from("event_type")->where("class_name = :cn",array(":cn" => "OphCoCorrespondence"))->queryScalar();

		$this->insert('patientticketing_queue_event_type',array('queue_id' => $queue_id, 'event_type_id' => $prescription_id, 'display_order' => 1));
		$this->insert('patientticketing_queue_event_type',array('queue_id' => $queue_id, 'event_type_id' => $correspondence_id, 'display_order' => 1));
	}

	public function down()
	{
		$queue_id = $this->dbConnection->createCommand()->select("id")->from("patientticketing_queue")->where("name = :name",array(":name" => "Clinic Reviewed"))->queryScalar();

		$this->delete('patientticketing_queue_event_type',"queue_id = $queue_id");
	}
}
