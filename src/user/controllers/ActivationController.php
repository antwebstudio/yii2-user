<?php
namespace ant\user\controllers;

use Yii;
use ant\user\models\ActivationForm;
use ant\user\models\UpdateEmailForm;
use ant\user\models\BasicActivationForm;
use ant\user\models\User;

class ActivationController extends \yii\web\Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return
        [
            'verbs' =>
            [
               'class' => \yii\filters\VerbFilter::className(),
               'actions' =>
               [
                   'resend-code' => ['post'],
               ],
           ],
        ];
    }

    /**
     * Activate user.
     *
     * @return mixed
     */
    public function actionActivation(){
        /**
         * Activation Form
         */
        $activationForm = new BasicActivationForm(['email' => Yii::$app->user->identity->email]);
		
		if ($activationForm->checkActivation()) {
			return $this->redirect(['/']);
		}

        /**
         * Resend Activation Code Form
         */
        $updateEmailForm = new UpdateEmailForm([
			'user' => Yii::$app->user->identity,
		]);

        if ($activationForm->load(Yii::$app->request->post()) && $activationForm->activate()) {
            return $this->render('activationSuccess');
        }

        if ($updateEmailForm->load(Yii::$app->request->post()) && $updateEmailForm->save()) {
			$notification = new \ant\user\notifications\Activation($updateEmailForm->user);
			\Yii::$app->notifier->on(\tuyakhov\notifications\Notifier::EVENT_AFTER_SEND, function($event) {
				if ($event->response === true) {
					\Yii::$app->session->setFlash('success', 'Resend activation code success.');
				} else {
					\Yii::$app->session->setFlash('error', 'Send activation code error.');
				}
			});
			\Yii::$app->notifier->send($updateEmailForm->user, $notification);
        }

        return $this->render($this->action->id, [
            'activationForm' => $activationForm,
            'updateEmailForm' => $updateEmailForm,
        ]);
    }
	
	public function actionResendCode() {
		$user = Yii::$app->user->identity;
		$notification = new \ant\user\notifications\Activation($user);
		
		\Yii::$app->notifier->on(\tuyakhov\notifications\Notifier::EVENT_AFTER_SEND, function($event) {
			if ($event->response === true) {
				\Yii::$app->session->setFlash('success', 'Resend activation code success.');
			} else {
				\Yii::$app->session->setFlash('error', 'Send activation code error.');
			}
		});
		\Yii::$app->notifier->send($user, $notification);
		
		return $this->redirect(['activation']);
	}

    /**
     * Activation a user by token.
     *
     * @param  string $token
     * @return mixed
     */
    public function actionTokenActivation($email, $code) {
        $activationForm = new BasicActivationForm(['email' => $email]);
		$activationForm->activationCode = $code;
		
		if ($activationForm->checkActivation()) {
			return $this->redirect(['/']);
		} else if ($activationForm->activate()){
            // Login to current active user
			if (!$this->module->signupNeedAdminApproval) {
				Yii::$app->user->login($activationForm->user);
			}
            return $this->render('activationSuccess');
        } else {
            throw new \yii\web\BadRequestHttpException('Token is expired or invalid token.');
        }

    }

    public function actionNewPasswordActivate($email, $code) {
        $model = new ActivationForm;
        $model->email = $email;
        $model->activationCode = $code;

        if ($model->user->isActive) {
            // login to current active user
			if (!$this->module->signupNeedAdminApproval) {
				Yii::$app->user->login($model->user);
			}
            return $this->render('activationSuccess');
        } else if ($model->load(\Yii::$app->request->post()) && $model->activate()) {
            // login to current active user
			if (!$this->module->signupNeedAdminApproval) {
				Yii::$app->user->login($model->user);
			}
            return $this->render('activationSuccess');
        } else if (!$model->isTokenValid) {
            throw new \yii\web\BadRequestHttpException('Token is expired or invalid token.');
        }

        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
		if (YII_DEBUG) throw new \Exception('DEPRECATED, use action in ActivationContoller'); // Added on 04-10-2019
		
        $model = new PasswordResetupdateEmailForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return Yii::$app->response->redirect(Yii::$app->user->loginUrl);
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($tokenkey, $email)
    {
        try {
            $model = new ResetPasswordForm($tokenkey, $email);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password was saved.');

            return Yii::$app->response->redirect(Yii::$app->user->loginUrl);
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }
}
