<?php

class AuthController extends CController
{
	public function actionLogin()
	{
		$model = new LoginForm;

		if(Yii::app()->request->isPostRequest)
		{
			$model->username = Yii::app()->request->getPost('myemail');
			$model->password = Yii::app()->request->getPost('mypassword');

			if($model->validate())
			{
				$model->login();
				$this->redirect(['index/index']);
			}
		}

		$this->render('login', ['model' => $model]);
	}

	public function actionRegister()
	{
		$user = new Users;

		if(Yii::app()->request->isPostRequest)
		{
			$user->username = Yii::app()->request->getPost('username');
			$user->email = Yii::app()->request->getPost('email');
			$user->password = Yii::app()->request->getPost('password');

			if($user->save())
				$this->redirect(['index/index']);
		}

		$this->render('register', ['user' => $user]);
	}

	public function actionPreset()
	{
		$model = new ResetPasswordForm;
		$success = false;

		if(Yii::app()->request->isPostRequest)
		{
			$model->email = Yii::app()->request->getPost('myemail');

			if($model->validate())
			{
				$success = true;

				Mail::send($model->email, 'reset your ' . Yii::app()->name . ' password', 'preset_mail', ['salt' => $model->salt]);
			}
		}

		$this->render('preset', ['model' => $model, 'success' => $success]);
	}

	public function actionPresetFinish()
	{
		$user = Users::model()->find('salt = :salt', ['salt' => Yii::app()->request->getParam('salt')]);

		if($user === null)
			$this->redirect(['index/index']);

		if(Yii::app()->request->isPostRequest)
		{
			$user->scenario = 'changePassword';
			$user->password = Yii::app()->request->getPost('password');

			$user->save();
			$this->redirect(['index/index']);
		}

		$this->render('preset_finish');
	}

	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
}