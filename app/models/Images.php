<?php

class Images extends CActiveRecord
{
	public $enabledArr;
	
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	protected function afterFind()
	{
		if (strlen($this->enabled) > 0) {
			$this->enabledArr = explode(',', $this->enabled);
		} else {
			$this->enabledArr = array();
		}
		return parent::afterFind();
	}
}
