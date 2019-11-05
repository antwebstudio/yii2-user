<?php
namespace ant\user\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;

use ant\user\models\LoginForm;
use ant\user\models\SignupForm;
use ant\user\models\ActivationForm;
use ant\user\models\BasicActivationForm;
use ant\user\models\ActivationCodeRequestForm;
use ant\user\models\PasswordResetRequestForm;
use ant\user\models\CreateInviteForm;
use ant\user\models\AdvancedSignUpForm;
use ant\user\models\ResetPasswordForm;
use ant\rbac\Role;
use ant\user\models\User;
use ant\user\models\UserInvite;
use ant\user\models\InviteRequest;

use frontend\filters\AccessControl;
use frontend\filters\AccessRule;
use unclead\multipleinput\MultipleInput;


/**
 * Signin controller for the `user` module
 */
class SigninController extends Controller
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
               'class' => VerbFilter::className(),
               'actions' =>
               [
                   'logout' => ['post'],
               ],
           ],
        ];
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) return $this->goHome();

        //$model = Yii::createObject($this->module->loginFormModel);
		$model = $this->module->getFormModel('login');
		
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return Yii::$app->user->identity->isActive ? $this->goHome() : $this->redirect(Yii::$app->user->activateUrl);
        }
		
		return $this->render('login', [
			'model' => $model,
		]);
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup($signupType = 'default')
    {
        $model = $this->module->getFormModel('signup');
        $model->userIp = Yii::$app->request->userIp;

		// Need to logout first to avoid user profile is being assigned to currently logged in user
        if ($model->load(Yii::$app->request->post()) && $model->validate() && Yii::$app->user->logout() && $user = $model->signup() ) {
			Yii::$app->session->setFlash('success', 'You have successfully signed up. Thank you for signing up with us. ');
            return $this->redirect(	isset($model->redirectUrl) ? $model->redirectUrl : Yii::$app->user->loginUrl);
        }

        return $this->render('signup', [
			'model' => $model,
			'signupType' => $signupType
        ]);
    }

    public function actionPreSignup () {
        return $this->render('pre-signup', []);
    }

    /**
     * Activate user.
     *
     * @return mixed
     */
    public function actionActivation() {
		if (YII_DEBUG) throw new \Exception('DEPRECATED, use action in ActivationContoller'); // Added on 04-10-2019
		
        /**
         * Activation Form
         */
        $modelActivationForm = new ActivationForm();

        /**
         * Resend Activation Code Form
         */
        $modelActivationCodeRequestForm = new ActivationCodeRequestForm();
		$modelActivationCodeRequestForm->user = Yii::$app->user->identity;
		
		if (!Yii::$app->user->isGuest) {
			//set email to current user email
			$modelActivationForm->email = Yii::$app->user->identity->email;
			$modelActivationCodeRequestForm->email = Yii::$app->user->identity->email;
		}

        if ($modelActivationForm->load(Yii::$app->request->post()) && $modelActivationForm->activate()) {
            return $this->render('activationSuccess');
        }

        if ($modelActivationCodeRequestForm->load(Yii::$app->request->post())) {
            if($modelActivationCodeRequestForm->send())
                Yii::$app->session->setFlash('success', 'Resend validation code success.');
            else
                Yii::$app->session->setFlash('error', 'Send validate code error.');
        }

        return $this->render('activation', [
            'modelActivationForm'               => $modelActivationForm,
            'modelActivationCodeRequestForm'    => $modelActivationCodeRequestForm,
        ]);
    }

    /**
     * Activation a user by token.
     *
     * @param  string $token
     * @return mixed
     */
    public function actionTokenActivation($email, $code){
		throw new \Exception('DEPRECATED, use action in ActivationContoller'); // Added on 04-10-2019
		
        $modelActivationForm = new BasicActivationForm();
        $modelActivationForm->email = $email;
        $modelActivationForm->activationCode = $code;

        if($modelActivationForm->activate() && $modelActivationForm->user){
            
            // login to current active user
			//if (!$this->module->signupNeedAdminApproval) {
				Yii::$app->user->login($modelActivationForm->user);
			//}
            return $this->render('activationSuccess');

        } else if (User::find()->andWhere(['email' => $email])->andWhere(['status' => User::STATUS_ACTIVATED])-> count() == 1 ) {
			//if (!$this->module->signupNeedAdminApproval) {
				Yii::$app->user->login($modelActivationForm->user);
			//}
            return $this->render('activationSuccess');

        } else {
            
            throw new \yii\web\HttpException(403, 'Token is expired or invalid token.');
        }

    }

    public function actionNewPasswordActivate($email, $code) {
		if (YII_DEBUG) throw new \Exception('DEPRECATED, use action in ActivationContoller'); // Added on 04-10-2019
		
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
            throw new \yii\web\HttpException(403, 'Token is expired or invalid token.');
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
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return Yii::$app->response->redirect(Yii::$app->user->loginUrl);
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        return $this->render($this->action->id, [
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
		if (YII_DEBUG) throw new \Exception('DEPRECATED, use action in ActivationContoller'); // Added on 04-10-2019
		
        try {
            $model = new ResetPasswordForm($tokenkey, $email);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password was saved.');

            return Yii::$app->response->redirect(Yii::$app->user->loginUrl);
        }

        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }

    public function actionCreateInviteUser($tokenkey, $email, $inviteType = 'invite')
    {
		if (User::findByEmail($email)) {
			Yii::$app->session->setFlash('success', 'The account is already created.');
			return $this->redirect(Yii::$app->user->loginUrl);
		}

		$model = $this->module->getFormModel('signupInvitedUser', [
			'email' => $email, 'tokenkey' => $tokenkey, 'inviteType' => $inviteType
		]);
		$model->userIp = Yii::$app->request->userIp;
		
		if (!$model->validateToken()) throw new BadRequestHttpException('Invalid link, link is either canceled or expired. ');

        if ($model->load(Yii::$app->request->post()) && $model->signup() && Yii::$app->user->logout() )
        {
            return $this->redirect(Yii::$app->user->loginUrl);
        }
		
        return $this->render('signup', [
            'model' => $model,
            'signupType' => $inviteType

        ]);
    }

    protected function getDbFieldNameWithConfigfieldName($configFieldActualName){
		if (YII_DEBUG) throw new \Exception('DEPRECATED'); // Added on 04-10-2019
		
        // example : profile['firstname']
        $stringOfArrayFieldName = explode("[", $configFieldActualName);
        return $stringOfArrayFieldName[0];
    }

    protected function processFillDataForWidgetMultipleInput($userInvite, $inviteType, $configFieldActualName, $dbInviteConfigName){
		if (YII_DEBUG) throw new \Exception('DEPRECATED'); // Added on 04-10-2019
		
        if (isset($userInvite->data[$dbInviteConfigName][0] ) ) {
            $this->processFillDataForMultiColumn($userInvite, $inviteType, $configFieldActualName, $dbInviteConfigName);
        }
        //single column stored
        else{
            $this->processFillDataForSingleColumn($userInvite, $inviteType, $configFieldActualName, $dbInviteConfigName);
        }
    }

    protected function processFillDataForMultiColumn($userInvite, $inviteType, $configFieldActualName, $dbInviteConfigName){
		if (YII_DEBUG) throw new \Exception('DEPRECATED'); // Added on 04-10-2019
		
        foreach (Yii::$app->getModule('user')->signUpModel[$inviteType]['fields'][$configFieldActualName]['field']['options']['columns'] as $columnsIndex => $inputOptionColumnProperty) {
            foreach ($userInvite->data[$dbInviteConfigName] as $index => $dataValueArray) {
                // if config is got new column, but db of this attribute does not have, does not add this data
                if (isset($userInvite->data[$dbInviteConfigName][$index][$inputOptionColumnProperty['name']]) ) {
                    Yii::$app->getModule('user')->signUpModel[$inviteType]['fields'][$configFieldActualName]['field']['options']['data'][$index][$inputOptionColumnProperty['name']] = 
                    $dataValueArray[ $inputOptionColumnProperty['name'] ];
                }
            }
        }
    }

    protected function processFillDataForSingleColumn($userInvite, $inviteType, $configFieldActualName, $dbInviteConfigName){
		if (YII_DEBUG) throw new \Exception('DEPRECATED'); // Added on 04-10-2019
		
        $arrData = [];
        foreach ($userInvite->data[$dbInviteConfigName] as $index => $dataValueArray) {
            foreach ($dataValueArray as $dataValueArrayIndex => $dbInviteValue) {
                Yii::$app->getModule('user')->signUpModel[$inviteType]['fields'][$configFieldActualName]['field']['options']['data'][][$index] = $dbInviteValue;
            }
        }
    }

}

?>
