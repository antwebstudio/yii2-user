<?php  
namespace ant\user;

use Yii;

class User extends \yii\web\User{
    /**
     * @var string|array the URL for login when [[loginRequired()]] is called.
     * If an array is given, [[UrlManager::createUrl()]] will be called to create the corresponding URL.
     * The first element of the array should be the route to the login action, and the rest of
     * the name-value pairs are GET parameters used to construct the login URL. For example,
     *
     * ```php
     * ['site/login', 'ref' => 1]
     * ```
     *
     * If this property is `null`, a 403 HTTP exception will be raised when [[loginRequired()]] is called.
     */
    public $loginUrl = ['/user/signin/login'];

    public $logoutUrl = ['/user/signin/logout'];

	/**
     * @var string|array the URL for activation when [[activateRequired()]] is called.
     * If an array is given, [[UrlManager::createUrl()]] will be called to create the corresponding URL.
     * The first element of the array should be the route to the activate action, and the rest of
     * the name-value pairs are GET parameters used to construct the activate URL. For example,
     *
     * ```php
     * ['site/activate', 'ref' => 1]
     * ```
     *
     * If this property is `null`, a 403 HTTP exception will be raised when [[activateRequired()]] is called.
     */
	public $activateUrl = ['/user/activation/activation'];

	/**
     * Initializes the application component.
     */
	public function init(){
		parent::init();
	}

	/**
     * Redirects the user browser to the activate page.
     *
     * Before the redirection, the current URL (if it's not an AJAX url) will be kept as [[returnUrl]] so that
     * the user browser may be redirected back to the current page after successful activate.
     *
     * Make sure you set [[activateUrl]] so that the user browser can be redirected to the specified activate URL after
     * calling this method.
     *
     * Note that when [[activateUrl]] is set, calling this method will NOT terminate the application execution.
     *
     * @param bool $checkAjax whether to check if the request is an AJAX request. When this is true and the request
     * is an AJAX request, the current URL (for AJAX request) will NOT be set as the return URL.
     * @param bool $checkAcceptHeader whether to check if the request accepts HTML responses. Defaults to `true`. When this is true and
     * the request does not accept HTML responses the current URL will not be SET as the return URL. Also instead of
     * redirecting the user an ForbiddenHttpException is thrown. This parameter is available since version 2.0.8.
     * @return Response the redirection response if [[activateUrl]] is set
     * @throws ForbiddenHttpException the "Access Denied" HTTP exception if [[activateUrl]] is not set or a redirect is
     * not applicable.
     */
    public function activateRequired($checkAjax = true, $checkAcceptHeader = true)
    {
        $request = Yii::$app->getRequest();
        $canRedirect = !$checkAcceptHeader || $this->checkRedirectAcceptable();
        if ($this->enableSession
            && $request->getIsGet()
            && (!$checkAjax || !$request->getIsAjax())
            && $canRedirect
        ) {
            $this->setReturnUrl($request->getUrl());
        }
        if ($this->activateUrl !== null && $canRedirect) {
            $activateUrl = (array) $this->activateUrl;
            if ($activateUrl[0] !== Yii::$app->requestedRoute) {
                return Yii::$app->getResponse()->redirect($this->activateUrl);
            }
        }
        throw new ForbiddenHttpException(Yii::t('yii', 'Activate Required'));
    }

    /**
     * Returns a value indicating whether the user is not a guest (not authenticated) and is activated.
     * @return bool whether the current user is not a guest (not authenticated) and is activated.
     * @see getIdentity()
     */
    public function getIsActivated()
    {	
    	$identity = $this->getIdentity();

        return !$this->isGuest ? $identity->status == \ant\user\models\User::STATUS_ACTIVATED : false;
    }
}
?>