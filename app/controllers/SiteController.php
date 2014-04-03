<?php

class SiteController extends CController {

	public function actionIndex() {
		$model = new UploadForm;
		$uniq = false;

		if(isset($_POST['UploadForm']))
		{
			$model->attributes=$_POST['UploadForm'];
			if ($model->validate()) {

				$model->image->resize(Yii::app()->params['imageResizeTo']);
				$type = $model->image->getType();
				$uniq = uuidGenerator::gen();
				
				$horizontal = $model->image->isHorizontal();
				$i = 0;
				$partSize = $model->image->partSize();
				$x = 0;
				$y = 0;
				$xOff = ($horizontal) ? $partSize : $model->image->getWidth(); 
				$yOff = (!$horizontal) ? $partSize : $model->image->getHeight();
				do {
					$i++;
					if ($model->image->saveCrop($x, $y, $xOff, $yOff, Yii::app()->params['imageSavePath'].$uniq.$i.'.'.$type) === false) break;
					if ($horizontal) {
						$x += $partSize;
					} else {
						$y += $partSize;
					}
				} while ($i < $model->parts);

				// check for file error
				if ($i != $model->parts) {
					for ($j = $i - 1; $j > 0; $j--) {
						unlink(Yii::app()->params['imageSavePath'].$uniq.$j.'.'.$type);
					}

					Yii::app()->user->setFlash('uploaded', 'Something goes wrong.');
				} else {
					$img = new Images();
					$img->uuid = $uniq;
					$img->parts = $model->parts;
					$img->horizontal = $horizontal;
					$img->type = $type;
					$img->width = $model->image->getWidth();
					$img->height = $model->image->getHeight();
					$img->enabled = $model->getEnabled();
				 	if($img->save()) {
						Yii::app()->user->setFlash('uploaded', 'File uploaded.');
					} else {
						for ($j = $i - 1; $j > 0; $j--) {
							unlink(Yii::app()->params['imageSavePath'].$uniq.$j.'.'.$type);
						}						
						Yii::app()->user->setFlash('uploaded', 'Something goes wrong.');
					}
				}
			}
		}

		$this->render('index', array('model' => $model, 'admin' => $uniq));
	}

	public function actionLoadImageOnce($uuid, $part) {
		$image = Images::model()->find('uuid=:uuid', array(':uuid' => $uuid));

		if (($image) && 
			($part > 0) &&
			($part <= $image['parts']) &&
			(Yii::app()->session['last_viewed'] == $uuid)) { // check last user viewed image
			Yii::app()->request->xSendFile(Yii::app()->params['nginxXAccelPath'].$image['uuid'].$part.'.'.$image['type'], array(
				'xHeader' => 'X-Accel-Redirect',
				'mimeType' => 'image/'.$image['type'],
				'forceDownload' => false,
				'terminate' => true,
			));
		} else {
			throw new CHttpException(404, 'Not found');
		}
	}

	public function actionViewImage($uuid) {
		$image = Images::model()->find('uuid=:uuid', array(':uuid' => $uuid));

		if ($image) {
			Yii::app()->session['last_viewed'] = $uuid; // store last user viewed image
			$this->render('viewImage', array('image' => $image));
		} else {
			throw new CHttpException(404, 'Not found');
		}
	}

	public function actionLoadImage($uuid, $part) {
		$image = Images::model()->find('uuid=:uuid', array(':uuid' => $uuid));

		if (($image) && 
			($part > 0) &&
			($part <= $image['parts']) &&
			(in_array($part, $image['enabledArr']))) {
			Yii::app()->request->xSendFile(Yii::app()->params['nginxXAccelPath'].$image['uuid'].$part.'.'.$image['type'], array(
				'xHeader' => 'X-Accel-Redirect',
				'mimeType' => 'image/'.$image['type'],
				'forceDownload' => false,
				'terminate' => true,
			));
		} else {
			throw new CHttpException(404, 'Not found');
		}
	}	
}
