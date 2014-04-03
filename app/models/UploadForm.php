<?php
 
class UploadForm extends CFormModel {
 
	public $image;
	public $parts;
	public $enabled;
	public $enabledArr;
 
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
			array('image', 'file', 'allowEmpty' => false, 'safe' => true, 'types' => 'jpg, jpeg, gif, png'),
			array('parts', 'in', 'range' => $this->getPartsOptions(), 'allowEmpty' => false),
			array('enabled', 'match', 'pattern' => '/^[,0-9]+$/'),
			array('enabled', 'parseEnabled'),
			array('image', 'loadValidateImage'),
		);
	}

	public function getPartsOptions() {
		return array(9 => 9, 12 => 12);
	}

	public function parseEnabled() {
		if(!$this->hasErrors()) {
			if (strlen($this->enabled) > 0) {
				$this->enabledArr = array_unique(explode(',', $this->enabled), SORT_NUMERIC);

				foreach ($this->enabledArr as $val) {
					if (($val === '') || ($val === 0) || ($val > $this->parts)) {
						$this->addError('enabled', 'Bad enabled parts.');
						return false;
					}
				}
			} else {
				$this->enabledArr = array();
			}

			return true;
		}

		return false;
	}

	public function getEnabled() {
		return implode(',', $this->enabledArr);
	}

	public function loadValidateImage() {
		if(!$this->hasErrors()) {
			// get upload
			$this->image = CUploadedFile::getInstance($this, 'image');
			if (!$this->image) goto BAIL;
			// parse image
			$path = $this->image->tempName;
			$this->image = new ImageHelper(Yii::app()->params['imageMaxDimension'], $this->parts);
			if (!$this->image->load($path)) goto BAIL;
			
			return true;
	BAIL:
		$this->addError('image', 'Bad image.');			
		}

		return false;		
	}
}
