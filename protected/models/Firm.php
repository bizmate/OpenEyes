<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/**
 * This is the model class for table "firm".
 *
 * The followings are the available columns in table 'firm':
 * @property string $id
 * @property string $service_subspecialty_assignment_id
 * @property string $pas_code
 * @property string $name
 *
 * The followings are the available model relations:
 * @property ServiceSubspecialtyAssignment $serviceSubspecialtyAssignment
 * @property FirmUserAssignment[] $firmUserAssignments
 * @property LetterPhrase[] $letterPhrases
 */
class Firm extends BaseActiveRecord
{
	public $subspecialty_id;
	public $consultant_id;

	/**
	 * Returns the static model of the specified AR class.
	 * @return Firm the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'firm';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('service_subspecialty_assignment_id, name', 'required'),
			array('service_subspecialty_assignment_id', 'length', 'max'=>10),
			array('pas_code', 'length', 'max'=>4),
			array('name', 'length', 'max'=>40),
			array('name, pas_code, subspecialty_id, consultant_id', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, service_subspecialty_assignment_id, pas_code, name', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'serviceSubspecialtyAssignment' => array(self::BELONGS_TO, 'ServiceSubspecialtyAssignment', 'service_subspecialty_assignment_id'),
			'firmUserAssignments' => array(self::HAS_MANY, 'FirmUserAssignment', 'firm_id'),
			'userFirmRights' => array(self::HAS_MANY, 'UserFirmRights', 'firm_id'),
			'letterPhrases' => array(self::HAS_MANY, 'LetterPhrase', 'firm_id'),
			'members' => array(self::MANY_MANY, 'User', 'firm_user_assignment(firm_id, user_id)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'service_subspecialty_assignment_id' => 'Service Subspecialty Assignment',
			'pas_code' => 'Pas Code',
			'name' => 'Name',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('service_subspecialty_assignment_id',$this->service_subspecialty_assignment_id,true);
		$criteria->compare('pas_code',$this->pas_code,true);
		$criteria->compare('name',$this->name,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns an array of the service_subspecialty names - the service name plus the subspecialty name.
	 */
	public function getServiceSubspecialtyOptions()
	{
		$sql = 'SELECT
					service_subspecialty_assignment.id,
					service.name AS service_name,
					subspecialty.name AS subspecialty_name
				FROM
					service,
					subspecialty,
					service_subspecialty_assignment
				WHERE
					service.id = service_subspecialty_assignment.service_id
				AND
					subspecialty.id = service_subspecialty_assignment.subspecialty_id
				ORDER BY
					service.name,
					subspecialty.name
				';

		$connection = Yii::app()->db;
		$command = $connection->createCommand($sql);
		$results = $command->queryAll();

		$select = array();

		foreach ($results as $result) {
			$select[$result['id']] = $result['service_name'] . ' - ' . $result['subspecialty_name'];
		}

		return $select;
	}

	public function getServiceText()
	{
		return $this->serviceSubspecialtyAssignment->service->name;
	}

	public function getSubspecialtyText()
	{
		return $this->serviceSubspecialtyAssignment->subspecialty->name;
	}

	/**
	 * Fetch an array of firm IDs and names
	 * @return array
	 */
	public function getList($subspecialtyId = null)
	{
		$result = array();

		if (empty($subspecialtyId)) {
			$list = Firm::model()->findAll();
		
			foreach ($list as $firm) {
				$result[$firm->id] = $firm->name;
			}
		} else {
			$list = Yii::app()->db->createCommand()
				->select('f.id, f.name')
				->from('firm f')
				->join('service_subspecialty_assignment ssa', 'f.service_subspecialty_assignment_id = ssa.id')
				->where('ssa.subspecialty_id = :sid', array(':sid' => $subspecialtyId))
				->queryAll();

			foreach ($list as $firm) {
				$result[$firm['id']] = $firm['name'];
			}
		}

		natcasesort($result);

		return $result;
	}

	public function getListWithoutDupes() {
		$result = array();

		if (empty($subspecialtyId)) {
			$list = Firm::model()->findAll();
	 
			foreach ($list as $firm) {
				if (!in_array($firm->name,$result)) {
					$result[$firm->id] = $firm->name;
				}
			}
		}

		natcasesort($result);

		return $result;
	}

	public function getListWithSpecialties() {
		$firms = Yii::app()->db->createCommand()
			->select('f.id, f.name, s.name AS subspecialty')
			->from('firm f')
			->join('service_subspecialty_assignment ssa', 'f.service_subspecialty_assignment_id = ssa.id')
			->join('subspecialty s','ssa.subspecialty_id = s.id')
			->order('f.name, s.name')
			->queryAll();
		$data = array();
		foreach ($firms as $firm) {
			$data[$firm['id']] = $firm['name'] . ' (' . $firm['subspecialty'] . ')';
		}
		natcasesort($data);
		return $data;
	}

	public function getCataractList() {
		$specialty = Specialty::model()->find('code=?',array('OPH'));
		$subspecialty = Subspecialty::model()->find('specialty_id=? and name=?',array($specialty->id,'Cataract'));
		$ssa = ServiceSubspecialtyAssignment::model()->find('subspecialty_id=?',array($subspecialty->id));

		$criteria = new CDbCriteria;
		$criteria->compare('service_subspecialty_assignment_id',$ssa->id);
		$criteria->order = 'name';

		return CHtml::listData(Firm::model()->findAll($criteria),'id','name');
	}

	/**
	 * Returns the consultant for the firm
	 *
	 * @return object
	 */
	public function getConsultant() {
		$result = Yii::app()->db->createCommand()
			->select('cslt.id AS id')
			->from('consultant cslt')
			->join('contact c', "c.parent_id = cslt.id and c.parent_class = 'Consultant'")
			->join('user_contact_assignment uca', 'uca.contact_id = c.id')
			->join('user u', 'u.id = uca.user_id')
			->join('firm_user_assignment fua', 'fua.user_id = u.id')
			->join('firm f', 'f.id = fua.firm_id')
			->where('f.id = :fid', array(
				':fid' => $this->id
			))
			->queryRow();

		if (empty($result)) {
			return null;
		} else {
			return Consultant::model()->findByPk($result['id']);
		}
	}

	public function getConsultantName() {
		if ($consultant = $this->consultant) {
			return $consultant->contact->title . ' ' . $consultant->contact->first_name . ' ' . $consultant->contact->last_name;
		}
		return 'NO CONSULTANT';
	}

	public function getConsultantUser() {
		$result = Yii::app()->db->createCommand()
			->select('u.id as id')
			->from('consultant cslt')
			->join('contact c', "c.parent_id = cslt.id and c.parent_class = 'Consultant'")
			->join('user_contact_assignment uca', 'uca.contact_id = c.id')
			->join('user u', 'u.id = uca.user_id')
			->join('firm_user_assignment fua', 'fua.user_id = u.id')
			->join('firm f', 'f.id = fua.firm_id')
			->where('f.id = :fid', array(
				':fid' => $this->id
			))
			->queryRow();

		if (empty($result)) {
			return null;
		} else {
			return User::model()->findByPk($result['id']);
		}
	}

	public function getReportDisplay() {
		return $this->name.' ('.$this->serviceSubspecialtyAssignment->subspecialty->name.')';
	}

	public function getSpecialty() {
		$result = Yii::app()->db->createCommand()
			->select('su.specialty_id as id')
			->from('subspecialty su')
			->join('service_subspecialty_assignment svc_ass', 'svc_ass.subspecialty_id = su.id')
			->join('firm f', 'f.service_subspecialty_assignment_id = svc_ass.id')
			->where('f.id = :fid', array(
				':fid' => $this->id
			))
			->queryRow();
		
		if (empty($result)) {
			return null;
		} else {
			return Specialty::model()->findByPk($result['id']);
		}
	}

	public function beforeSave() {
		if ($this->subspecialty_id) {
			$this->service_subspecialty_assignment_id = ServiceSubspecialtyAssignment::model()->find('subspecialty_id=?',array($this->subspecialty_id))->id;
		}

		return parent::beforeSave();
	}

	protected function afterSave() {
		if ($this->consultant_id) {
			if ($user = User::model()->findByPk($this->consultant_id)) {
				if (!$this->getConsultantUser() || $this->getConsultantUser()->id != $user->id) {
					if (!$fua = FirmUserAssignment::model()->find('firm_id=?',array($this->id))) {
						$fua = new FirmUserAssignment;
						$fua->firm_id = $this->id;
					}
					$fua->user_id = $user->id;
					if (!$fua->save()) {
						throw new Exception("Unable to save firm_user_assignment: ".print_r($fua->getErrors(),true));
					}
					if (!$uca = UserContactAssignment::model()->find('user_id=?',array($user->id))) {
						$consultant = new Consultant;
						if (!$consultant->save(false)) {
							throw new Exception("Unable to create new consultant: ".print_r($consultant->getErrors(),true));
						}

						$contact = new Contact;
						$contact->title = $user->title;
						$contact->first_name = $user->first_name;
						$contact->last_name = $user->last_name;
						$contact->qualifications = $user->qualifications;
						$contact->parent_class = 'Consultant';
						$contact->parent_id = $consultant->id;
						if (!$contact->save(false)) {
							throw new Exception("Unable to create contact: ".print_r($contact->getErrors(),true));
						}

						$uca = new UserContactAssignment;
						$uca->user_id = $user->id;
						$uca->contact_id = $contact->id;
						if (!$uca->save()) {
							throw new Exception("Unable to create user_contact_assignment: ".print_r($uca->getErrors(),true));
						}
					}
					if ($uca->contact->parent_class != 'Consultant') {
						$consultant = new Consultant;
						if (!$consultant->save(false)) {
							throw new Exception("Unable to create new consultant: ".print_r($consultant->getErrors(),true));
						}

						$contact = $uca->contact;
						$contact->parent_class = 'Consultant';
						$contact->parent_id = $consultant->id;
						if (!$contact->save()) {
							throw new Exception("Unable to save contact: ".print_r($contact->getErrors(),true));
						}
					}
				}
				$this->consultant_id = null;
			} else {
				throw new Exception("User not found: $this->consultant_id");
			}
		}
	}
}
