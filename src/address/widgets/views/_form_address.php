<?php  
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use kartik\select2\Select2;
use kartik\depdrop\DepDrop;

use ant\address\widgets\Address;
use ant\address\models\AddressCountry;

$enableVenue = isset($enableVenue) ? $enableVenue : false;
$enableCustomStateCountryIso = isset($enableCustomStateCountryIso) ? $enableCustomStateCountryIso : false;
$enableCordinate = isset($enableCordinate) ? $enableCordinate : false;
$enableMap = isset($enableMap) ? $enableMap : false;
$enableSearchLocation = isset($enableSearchLocation) ? $enableSearchLocation : false;
$enableCity = isset($enableCity) ? $enableCity : false;
$enableAddress = isset($enableAddress) ? $enableAddress : false;
$enableCustomeStateCountryId = isset($enableCustomeStateCountryId) ? $enableCustomeStateCountryId : false;
$enableCustomeStateZoneId = isset($enableCustomeStateZoneId) ? $enableCustomeStateZoneId : false;
$enableLargeGoogleMap = isset($enableLargeGoogleMap) ? $enableLargeGoogleMap : false;
?>
<div class="address-widgets-full-form">
		
	<?= Html::activeHiddenInput($model, 'currentForm', ['value' => Address::FORM_VIEW_ADDRESS]); ?>

	<?php if ($enableMap): ?>
	<div class="row">
		<div class="col-md-7" id='<?=$this->context->getId(); ?>form-row'>
	<?php endif ?>
			
	<?php if ($enableVenue): ?>
		<?= $form->field($model, 'venue')->textInput(['id' => 'venue_' . $this->context->getId()]) ?>
	<?php endif ?>
	<?php if ($enableAddress): ?>

		<?= $form->field($model, 'address_1')->textInput(['id' => 'address_1_' .  $this->context->getId()]) ?>

		<?= $form->field($model, 'address_2')->textInput(['id' => 'address_2_' . $this->context->getId()]) ?>
	<?php else: ?>
		<?= Html::hiddenInput("address_1",'',['id' => 'address_1_' .  $this->context->getId()]) ?>
		<?= Html::hiddenInput("address_2",'',['id' => 'address_2_' . $this->context->getId()]) ?>
	<?php endif ?>
		<div class="row">
		<?php if ($enableCity): ?>
			<div class="col-md-6">
				<?= $form->field($model, 'city')->textInput(['id' => 'locality_' . $this->context->getId()]) ?>
			</div>
			
			<div class="col-md-6">
				<?= $form->field($model, 'postcode')->textInput(['id' => 'postal_code_' . $this->context->getId()]) ?>
			</div>
		<?php else: ?>
			<?= Html::hiddenInput("city",'',['id' => 'locality_' . $this->context->getId()]) ?>
			<?= Html::hiddenInput("postcode",'',['id' => 'postal_code_' . $this->context->getId()]) ?>
		<?php endif ?>

		</div>

		<div class="row">
			<div class="col-md-6">
				<?php if ($enableCustomStateCountryIso): ?>

						<?= $form->field($model, 'countryIso2')->dropdownList(
							ArrayHelper::map(AddressCountry::find()->all(), 'iso_code_2', 'name'), 
							['options' => 
								[
								isset($model->country)? $model->country->iso_code_2 : '' => 
								['Selected' => true ] 
								],
							'id' => 'country_' . $this->context->getId(),
							'prompt' => 'Select ...',
							]);
						?>

				<?php elseif ($enableCustomeStateCountryId): ?>
					 <?=$form->field($model, 'country_id')->widget(Select2::classname(), [
					    'data' => ArrayHelper::map(AddressCountry::find()->all(), 'id', 'name'),
					     'options' => [
					     'prompt' => 'Select ...',
					     	'id' => 'country_' . $this->context->getId(),
					    ],
					]);; ?>
				<?php else: ?>
			<?= Html::hiddenInput("countryIso2",'',['id' => 'country_' . $this->context->getId()]) ?>
				<?php endif ?>
			</div>

			<div class="col-md-6">
				<?php if ($enableCustomStateCountryIso): ?>
					<?= $form->field($model, 'custom_state')->textInput(['id' => 'state_' . $this->context->getId()]) ?>
				<?php elseif ($enableCustomeStateZoneId): ?>
					<?= Html::hiddenInput('zone_id_hidden', $model->zone_id, ['id'=> 'zone_id_hidden']); 
					?>

					<?= $form->field($model, 'zone_id')->widget(DepDrop::classname(), [
					    'options' => [
					    	'placeholder' => 'Select ...',
					    	'id' => 'state',
					    	'required' => false,
					    ],
					    'type' => DepDrop::TYPE_SELECT2,
					    /*'select2Options'=>['pluginOptions'=>['allowClear'=>true]],*/
					    'pluginOptions'=>[
					        'depends'=>['country_' . $this->context->getId()],
					        'initialize' => true,
					        'initDepends'=>['country_' . $this->context->getId()],
					        'url' => Url::to(['/user/setting/zone-list']),
					        'params'=>['zone_id_hidden'],
					        'loadingText' => 'Loading Zone ...',
					    ]
					]); ?>
				<?php else: ?>
			<?= Html::hiddenInput("custom_state",'',['id' => 'state_' . $this->context->getId()]) ?>

					
				<?php endif ?>
			</div>
		</div>

			<?php if ($enableCordinate): ?>
				<div class="row">
					<div class="col-md-6">
						<?= $form->field($model, 'latitude')->textInput(['id' => 'latitude_' . $this->context->getId()] ) ?>
					</div>
					<div class="col-md-6">
						<?= $form->field($model, 'longitude')->textInput(['id' => 'longitude_' . $this->context->getId()]) ?>
					</div>
				</div>
			<?php endif ?>
			
			<?php if ($enableSearchLocation): ?>
			<div>
				<div class= "<?='col-md-'. $mapWidth ?>">
						<span onclick="<?=$this->context->getId(); ?>forms['<?=Address::FORM_VIEW_SEARCH; ?>'].callback()" style="cursor: pointer;"><i class="fa fa-search"></i> Search location</span>
				</div>
			</div>
			<?php endif ?>
		
	<?php if ($enableMap): ?>
		<?php if ($enableLargeGoogleMap): ?>
			<button type="button" class="btn btn-info btn-xs" data-toggle="modal" data-target="#<?=$this->context->getId(); ?>modal" id='<?=$this->context->getId(); ?>modal-button'  onclick ="appendMap()">Open Larger Map</button>
			<div class="modal fade" id="<?=$this->context->getId(); ?>modal" role="dialog">
				<div class="modal-dialog modal-lg">
					<div class="modal-content" >
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" id='header-close'>&times;</button>
							<h4 class="modal-title">Google Map</h4>
						</div>
						<div class="modal-body" id = "<?=$this->context->getId(); ?>modal-body" ></div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						</div>
					</div>
				</div>
			</div>
		<?php endif ?>
		<div style="height: 250px; background-color: #eee;" id = "<?=$this->context->getId(); ?>div-map">
			<div id="<?=$this->context->getId(); ?>map" style="height: 100%; width: 100%"></div>
			<div id="<?=$this->context->getId(); ?>no_result" class="centered-wrapper" style="height: 100%; width: 100%; display: none;">
				<div class="centered text-center">
					<i class="fa fa-map-marker" style="font-size: 50px;"></i>
					<p>No Map</p>
				</div>
			</div>
		</div>

	</div>

	<?php endif; ?>
</div>

<?php if ($enableLargeGoogleMap): ?>
	<?php $this->registerJs( '

	function appendMap()
	{
		$("#'.$this->context->getId().'div-map").css("height", "400px");
		var content = $("#'.$this->context->getId().'div-map");
		$("#'.$this->context->getId().'modal-body").append(content);
	}
	' , \yii\web\View::POS_END); ?>
<?php endif ?>


