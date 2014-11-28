<?php

class m141128_105528_glaucoma_child_common_disorders extends CDbMigration
{
	public function up()
	{
		$oph_id = $this->dbConnection->createCommand()->select("id")->from("specialty")->where("name = :name",array(":name" => 'Ophthalmology'))->queryScalar();
		$glaucoma_id = $this->dbConnection->createCommand()->select("id")->from("subspecialty")->where("specialty_id = :si and name = :name",array(":si" => $oph_id, ":name" => "Glaucoma"))->queryScalar();

		$this->dbConnection->createCommand("update disorder set specialty_id = $oph_id where id in (104,105,106)")->query();

		foreach (array(
			array('id' => 107, 'term' => 'Primary Congenital Glaucoma with neonatal or newborn onset (0-1 month)', 'fully_specified_name' => 'Primary Congenital Glaucoma with neonatal or newborn onset (0-1 month) (disorder)'),
			array('id' => 108, 'term' => 'Primary Congenital Glaucoma with infantile onset (>1-24 months)', 'fully_specified_name' => 'Primary Congenital Glaucoma with infantile onset (>1-24 months) (disorder)'),
			array('id' => 109, 'term' => 'Primary Congenital Glaucoma with Late onset or late-recognized (>2 years)', 'fully_specified_name' => 'Primary Congenital Glaucoma with Late onset or late-recognized (>2 years) (disorder)'),
			array('id' => 110, 'term' => 'Primary Congenital Glaucoma with Spontaneously arrested', 'fully_specified_name' => 'Primary Congenital Glaucoma with Spontaneously arrested (disorder)'),
			array('id' => 111, 'term' => 'Peters syndrome', 'fully_specified_name' => 'Peters plus syndrome (disorder)'),
			array('id' => 112, 'term' => 'Persistent fetal vasculature (if glaucoma present before cataract surgery)', 'fully_specified_name' => 'PHPV (disorder)'),
			array('id' => 113, 'term' => 'Childhood Glaucoma Associated with Non-acquired Ocular Anomalies', 'fully_specified_name' => 'Childhood Glaucoma Associated with Non-acquired Ocular Anomalies (disorder)'),
			array('id' => 114, 'term' => 'Childhood Glaucoma Associated with Non-acquired Systemic Disease or Syndrome', 'fully_specified_name' => 'Childhood Glaucoma Associated with Non-acquired Systemic Disease or Syndrome (disorder)'),
			array('id' => 115, 'term' => 'Childhood Glaucoma Following Cataract Surgery', 'fully_specified_name' => 'Childhood Glaucoma Following Cataract Surgery (disorder)'),
			array('id' => 116, 'term' => 'Congenital idiopathic cataract', 'fully_specified_name' => 'Congenital idiopathic cataract (disorder)'),
			array('id' => 117, 'term' => 'Congenital cataract associated with ocular anomalies / systemic disease (no previous glaucoma)', 'fully_specified_name' => 'Congenital cataract associated with ocular anomalies / systemic disease (no previous glaucoma) (disorder)'),
			array('id' => 118, 'term' => 'Acquired cataract (no previous glaucoma)', 'fully_specified_name' => 'Acquired cataract (no previous glaucoma) (disorder)'),
			array('id' => 119, 'term' => 'Childhood Glaucoma of Unknown Aetiology', 'fully_specified_name' => 'Childhood Glaucoma of Unknown Aetiology (disorder)'),
			) as $disorder) {

			if (!$this->dbConnection->createCommand()->select("id")->from("disorder")->where("id = :id",array(":id" => $disorder['id']))->queryRow()) {
				$disorder['specialty_id'] = $oph_id;
				$this->insert('disorder',$disorder);
			}
		}

		$fp = fopen(dirname(__FILE__)."/../data/oe-5154-child.csv","r");

		$cod_do = $this->dbConnection->createCommand()->select("max(display_order)")->from("common_ophthalmic_disorder")->where("subspecialty_id = :si",array(":si" => $glaucoma_id))->queryScalar() + 1;

		while ($data = fgetcsv($fp)) {
			if (@$data[4]) {
				var_dump($data[4]);

				if (@$data[1]) {
					if (!$disorder = $this->dbConnection->createCommand()->select("*")->from("disorder")->where("id = :id",array(":id" => $data[4]))->queryRow()) {
						throw new Exception("Disorder not found: {$data[4]}");
					}

					$this->insert('common_ophthalmic_disorder',array(
						'disorder_id' => $disorder['id'],
						'subspecialty_id' => $glaucoma_id,
						'display_order' => $cod_do++,
					));

					$parent = $this->dbConnection->createCommand()->select("*")->from("common_ophthalmic_disorder")->order("id desc")->limit(1)->queryRow();
					$secto_do = 0;
				} else if (@$data[2]) {
					if (!isset($parent)) {
						throw new Exception("Secondary item found without parent set: ".print_r($data,true));
					}

					if (!$disorder = $this->dbConnection->createCommand()->select("*")->from("disorder")->where("id = :id",array(":id" => $data[4]))->queryRow()) {
						throw new Exception("Disorder not found: {$data[4]}");
					}

					$this->insert('secondaryto_common_oph_disorder',array(
						'parent_id' => $parent['id'],
						'disorder_id' => $disorder['id'],
						'display_order' => $secto_do++,
					));
				}
			}
		}

		fclose($fp);
	}

	public function down()
	{
	}
}
