<?php  
use yii\helpers\Html;
use ant\address\widgets\Address;
$formViewAddressResetCoordinates = isset($formViewAddressResetCoordinates) ? $formViewAddressResetCoordinates : true;
?>
<div class="row">
	<?= Html::activeHiddenInput($model, 'currentForm', ['value' => Address::FORM_VIEW_SEARCH]); ?>

	<div class="col-md-6">
		<div class="form-group">
			<?= Html::label('Search Location', $for = $this->context->getId() . 'autocomplete', ['class' => 'control-label']); ?>
			<?= Html::textInput($name = null, $value = null, ['id' => $this->context->getId() . 'autocomplete', 'class' => 'form-control']); ?>
		</div>

		<div>
			<span onclick="<?=$this->context->getId(); ?>forms['<?= Address::FORM_VIEW_ADDRESS; ?>'].callback( false ,  '<?= $formViewAddressResetCoordinates ?>');" style="cursor: pointer;" id='search-button'><i class="fa fa-map-marker"></i> Enter Address</span>
		</div>
	</div>
</div>
