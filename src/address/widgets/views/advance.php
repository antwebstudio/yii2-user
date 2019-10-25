<?php  
use yii\helpers\Html;
use yii\bootstrap\Tabs;

use ant\widgets\ActiveForm;
use ant\address\widgets\Address;
?>
<div class="row">
	<div class="col-md-12">
		<div id="<?=$this->context->getId(); ?>map_form">
			<i class="fa fa-spinner fa-spin"></i> Loading ...
		</div>
	</div>
</div>

<?php $this->registerJs($this->context->getId() . 'forms["' . ($params['model']->currentForm ? $params['model']->currentForm : Address::FORM_VIEW_SEARCH) . '"].callback(false, false);', \yii\web\View::POS_LOAD); ?>