<?php  
namespace ant\address\widgets;

use Yii;
use yii\bootstrap\Widget;
use yii\helpers\ArrayHelper;
use yii\web\View;

use ant\helpers\ScriptHelper;

class Address extends Widget
{
	const FORM_VIEW_SEARCH = '_form_search';

	const FORM_VIEW_ADDRESS = '_form_address';

	public $model;

	public $form;

	public $mode = 'basic';

	public $_setting = [ 
		'enableMap' => true,
		'enableCordinate' => true,
		'enableCustomStateCountryIso' => true,
		'enableSearchLocation' => true,
		'enableVenue' => true,
		'enableCity' => true,
		'enableAddress' => true,
		'enableCustomeStateCountryId' => true,
		'enableCustomeStateZoneId' => true,
		'mapWidth' => 5,
		'formViewAddressResetCoordinates' => true,
		'enableLargeGoogleMap' => false,
	];

	private $_googleApiKey = 'AIzaSyB2g6IpGlGR42JPUqRfaqjNvseD9VAz8sI';

	public function setSetting($setting) {
		$this->_setting = ArrayHelper::merge( $this->_setting, $setting  );		
	}

	public function getSetting() {
		return $this->_setting;
	}

	private static function getFormViews()
	{
		return 
		[
			self::FORM_VIEW_SEARCH => '_form_search',

			self::FORM_VIEW_ADDRESS => '_form_address',
		];
	}

	public function getId($autoGenerate = true)
	{
		return ScriptHelper::identifierSanitizer(parent::getId($autoGenerate));
	}

	private function getModeSetting()
	{
		return 
		[
			'basic' => 
			[
				'view' => 'basic',
				'params' => 
				[
					'enableMap' => $this->setting['enableMap'],
					'enableCordinate' => $this->setting['enableCordinate'],
					'enableCustomStateCountryIso' => $this->setting['enableCustomStateCountryIso'],
					'enableSearchLocation' => $this->setting['enableSearchLocation'],
					'enableVenue' => $this->setting['enableVenue'],
					'enableCity' => $this->setting['enableCity'],
					'enableAddress' => $this->setting['enableAddress'],
					'enableCustomeStateCountryId' => $this->setting['enableCustomeStateCountryId'],
					'enableCustomeStateZoneId' => $this->setting['enableCustomeStateZoneId'],
					'mapWidth' => $this->setting['mapWidth'],
					'formViewAddressResetCoordinates' => $this->setting['formViewAddressResetCoordinates'],
					'enableLargeGoogleMap' => $this->setting['enableLargeGoogleMap'],
				],
				'registerJs' => false
			],
			'advance' =>
			[
				'view' => 'advance',
				'params' => 
				[
					'enableMap' => $this->setting['enableMap'],
					'enableCordinate' => $this->setting['enableCordinate'],
					'enableCustomStateCountryIso' => $this->setting['enableCustomStateCountryIso'],
					'enableSearchLocation' => $this->setting['enableSearchLocation'],
					'enableVenue' => $this->setting['enableVenue'],
					'enableCity' => $this->setting['enableCity'],
					'enableAddress' => $this->setting['enableAddress'],
					'enableCustomeStateCountryId' => $this->setting['enableCustomeStateCountryId'],
					'enableCustomeStateZoneId' => $this->setting['enableCustomeStateZoneId'],
					'mapWidth' => $this->setting['mapWidth'],
					'formViewAddressResetCoordinates' => $this->setting['formViewAddressResetCoordinates'],
					'enableLargeGoogleMap' => $this->setting['enableLargeGoogleMap'],
				],
				'registerJs' => function(){

					Yii::$app->view->registerJs("

					var " . $this->getId() . "map; 
					var " . $this->getId() . "autocomplete;
					var " . $this->getId() . "geocoder;

					var " . $this->getId() . "prevoius_search_address = '';

					var " . $this->getId() . "marker = null;

					var " . $this->getId() . "componentForm = 
					{
					    street_number				: 'short_name',
					    route						: 'long_name',
					    locality					: 'long_name',
					    administrative_area_level_1	: 'short_name',
					    country 					: 'short_name',
					    postal_code 				: 'short_name'
					};
					
					var " . $this->getId() . "fields = 
					[
						{
							id: 'venue_" . $this->getId() . "',
							key: 'venue',
							trigger: false,
							codeAddress: false,
							codeCordinate: false
						},
						{
							id: 'address_1_" . $this->getId() . "',
							key: 'address_1',
							trigger: false,
							codeAddress: true,
							codeCordinate: false
						},
						{
							id: 'address_2_" . $this->getId() . "',
							key: null,
							trigger: false,
							codeAddress: true,
							codeCordinate: false
						},
						{
							id: 'locality_" . $this->getId() . "',
							key: 'locality',
							trigger: false,
							codeAddress: true,
							codeCordinate: false
						},
						{
							id: 'state_" . $this->getId() . "',
							key: 'administrative_area_level_1',
							trigger: false,
							codeAddress: true,
							codeCordinate: false
						},
						{
							id: 'postal_code_" . $this->getId() . "',
							key: 'postal_code',
							trigger: false,
							codeAddress: true,
							codeCordinate: false
						},
						{
							id: 'country_" . $this->getId() . "',
							key: 'country',
							trigger: false /*'change'*/,
							codeAddress: true,
							codeCordinate: false
						},
						{

							id: 'longitude_" . $this->getId() . "',
							key: null,
							trigger: false,
							codeAddress: false,
							codeCordinate: true
						},
						{	
							id: 'latitude_" . $this->getId() . "',
							key: null,
							trigger: false,
							codeAddress: false,
							codeCordinate: true
						}
					];

					var " . $this->getId() . "forms = {
						" . self::FORM_VIEW_SEARCH . ": 
						{
							html : '" . ScriptHelper::jsOneLineString($this->render(self::FORM_VIEW_SEARCH, $this->_setting['params'])) . "',

							callback : function(){
								$(function(){
									$('#" . $this->getId() . "map_form').html($(" . $this->getId() . "forms['" . Address::FORM_VIEW_SEARCH . "'].html).fadeIn(300, function(){

											" . $this->getId() . "init();
									}));
								});
							}
						},
						" . self::FORM_VIEW_ADDRESS .": 
						{
							html : '" . ScriptHelper::jsOneLineString($this->render(self::FORM_VIEW_ADDRESS, $this->_setting['params'])) . "',

							callback: function(data, reset_cordinate = true){
								$(function(){

									$('#" . $this->getId() . "map_form').html($(" . $this->getId() . "forms['" . Address::FORM_VIEW_ADDRESS . "'].html).fadeIn(300, function(){

											" . $this->getId() . "init();
											
											if(data){
												$.each(" . $this->getId() . "fields, function(i, field){
													if(data[field.key]){
														var element = $('#' + field.id);

														element.val(data[field.key]);

														if(field.trigger) element.trigger(field.trigger);
													}
												});
											}
											
											" . $this->getId() . "prevoius_search_address = '';

											" . $this->getId() . "codeAddress(reset_cordinate);
									}));

									//used for enlarge map after close
									$('#" . $this->getId() . "modal').on('hide.bs.modal', function (event) {

										//change back to original size
										$('#" . $this->getId() . "div-map').css('height', '250px');
										var content = $('#" . $this->getId() . "div-map');
										$('#" . $this->getId() . "form-row').append(content);

										//recenter with new coordinates
										var latitude = $('#latitude_" . $this->getId() . "').val();
										var longitude = $('#longitude_" . $this->getId() . "').val();
										var latlng = new google.maps.LatLng(latitude, longitude);
										" . $this->getId() . "map.setCenter(latlng);
									});

								});
							}
						}
					};


					function " . $this->getId() . "init(){
						" . $this->getId() . "geocoder = new google.maps.Geocoder();
						if(!(document.getElementById('" . $this->getId() . "autocomplete') === null)){
							" . $this->getId() . "autocomplete = new google.maps.places.SearchBox((document.getElementById('" . $this->getId() . "autocomplete')), {types: ['geocode']});
					    	" . $this->getId() . "autocomplete.addListener('places_changed', " . $this->getId() . "autoComplete);
						}

						if(!(document.getElementById('" . $this->getId() . "map') === null)){
							" . $this->getId() . "map = new google.maps.Map(document.getElementById('" . $this->getId() . "map'), {
						        center: {lat: -33.8688, lng: 151.2195},
						        zoom: 13,
						        mapTypeId: 'roadmap',
						        scrollwheel: false,
						        draggable: true,
						        animation: google.maps.Animation.DROP,
						        types: ['geocode'],
								mapTypeControl: false,
								streetViewControl: false,
								fullscreenControl: false
						    });

						    " . $this->getId() . "hideMap(false);
						}

						for (var i = 0; i < "  . $this->getId() . "fields.length; i++) { 
							if(!(document.getElementById(" . $this->getId() . "fields[i].id) === null) && " . $this->getId() . "fields[i].codeAddress){
								document.getElementById(" . $this->getId() . "fields[i].id).addEventListener('change', " . $this->getId() . "codeAddress);
							}

							if(!(document.getElementById(" . $this->getId() . "fields[i].id) === null) && " . $this->getId() . "fields[i].codeCordinate){
								document.getElementById(" . $this->getId() . "fields[i].id).addEventListener('change', " . $this->getId() . "codeCordinate);
							}
						}
					}

					function " . $this->getId() . "autoComplete() {
					    var place = " . $this->getId() . "autocomplete.getPlaces();
						
						if (place.length > 0) {
							place = place[0];
						} else {
							return;
						}
						if(place.address_components){

					    	var address = {};

						    for (var i = 0; i < place.address_components.length; i++) {

						        var addressType = place.address_components[i].types[0];

						        var val = place.address_components[i][" . $this->getId() . "componentForm[addressType]];

						        //merge stree_numner and route to address_1
						        if(addressType == 'street_number') {
						            
						            address['address_1'] = val;

						        } else if(addressType == 'route') {

						            if(address['address_1']) {

						            	address['address_1'] = address['address_1'] + ' ' + val;

						            } else  {

						            	address['address_1'] = val;

						            }
						        } else {
						            address[addressType] = val;
						        }
						    }

						    address['venue'] = place.name;
							
							" . $this->getId() . "forms['" . Address::FORM_VIEW_ADDRESS . "'].callback(address);
						}
					}

					function " . $this->getId() . "hideMap(reset_cordinate = true){
						document.getElementById('" . $this->getId() . "map').style.display = 'none';
						document.getElementById('" . $this->getId() . "no_result').style.display = 'block';
						
						if(reset_cordinate){
							if(!(document.getElementById('longitude_" . $this->getId() . "') === null))
								document.getElementById('longitude_" . $this->getId() . "').value = '';
							
							if(!(document.getElementById('latitude_" . $this->getId() . "') === null))
								document.getElementById('latitude_" . $this->getId() . "').value = '';
						}
					}

					function " . $this->getId() . "showMap(){
						document.getElementById('" . $this->getId() . "map').style.display = 'block';
						document.getElementById('" . $this->getId() . "no_result').style.display = 'none';
						google.maps.event.trigger(" . $this->getId() . "map, 'resize');
					}

					function " . $this->getId() . "createMarker(latitude, longitude, viewport = null){

						if(" . $this->getId() . "marker) " . $this->getId() . "marker.setMap(null);

						" . $this->getId() . "marker = new google.maps.Marker({
							map: " . $this->getId() . "map,
							draggable: true,
							animation: google.maps.Animation.DROP,
							position: {lat: latitude, lng: longitude}
					    });

					    " . $this->getId() . "marker.addListener('dragend', " . $this->getId() . "markerDragend);

					    var bounds = new google.maps.LatLngBounds();

					    if(viewport) bounds.union(viewport);

					    else 
					    {
						    var googleLatLng = new google.maps.LatLng(latitude, longitude);
						    bounds.extend(googleLatLng);

						    if (bounds.getNorthEast().equals(bounds.getSouthWest())) {
						       var extendPoint1 = new google.maps.LatLng(bounds.getNorthEast().lat() + 0.01, bounds.getNorthEast().lng() + 0.01);
						       var extendPoint2 = new google.maps.LatLng(bounds.getNorthEast().lat() - 0.01, bounds.getNorthEast().lng() - 0.01);
						       bounds.extend(extendPoint1);
						       bounds.extend(extendPoint2);
						    }
					    }
						
					    " . $this->getId() . "fillCordinateFrom(latitude, longitude);

						" . $this->getId() . "map.fitBounds(bounds);
					}

					function " . $this->getId() . "markerDragend(evt) {
						" . $this->getId() . "fillCordinateFrom(" . $this->getId() . "marker.position.lat(), " . $this->getId() . "marker.position.lng());
					}

					function " . $this->getId() . "fillCordinateFrom(latitude, longitude){
						$(function(){
							
							$('#longitude_" . $this->getId() . "').val('');
							$('#latitude_" . $this->getId() . "').val('');

							longitude = $.isNumeric(longitude) ? longitude : 0;
							latitude = $.isNumeric(latitude) ? latitude : 0;

							$('#longitude_" . $this->getId() . "').val(longitude);
							$('#latitude_" . $this->getId() . "').val(latitude);
						});
					}

					function " . $this->getId() . "fillAddressForm(data){
						$(function(){
							$.each(" . $this->getId() . "fields, function(i, field){
								if(data[field.key]){
									var element = $('#' + field.id);

									element.val(data[field.key]);

									if(field.trigger) element.trigger(field.trigger);
								}
							});
						});
					}

					function " . $this->getId() . "codeAddress(reset_cordinate = true){
						if(reset_cordinate){
							var place = null;

							var address = '';

							$.each(" . $this->getId() . "fields, function(i, field){
								if(field.codeAddress) address += ' ' + $('#' + field.id).val();
							});

							if(address != " . $this->getId() . "prevoius_search_address){

								" . $this->getId() . "geocoder.geocode({'address': address}, function(results, status) {	
									
									place = results ? results[0] : null;
									var myLatlng = place ? place.geometry.location : null;

									" . $this->getId() . "createMarkerByCordinate(myLatlng);
								});
								

								" . $this->getId() . "prevoius_search_address = address;

							}
						} else {
							" . $this->getId() . "codeCordinate();
						}
					}

					function " . $this->getId() . "codeCordinate(){
						$(function(){
							var latitude = $('#latitude_" . $this->getId() . "').val();
							var longitude = $('#longitude_" . $this->getId() . "').val();

							latitude = latitude == '' || $.isNumeric(latitude)  ? latitude : 0;
							longitude = longitude == '' || $.isNumeric(longitude)  ? longitude : 0;

							if($.isNumeric(latitude) && $.isNumeric(longitude)){
								var myLatlng = new google.maps.LatLng(parseFloat(latitude),parseFloat(longitude));

								" . $this->getId() . "createMarkerByCordinate(myLatlng);
							} else " . $this->getId() . "hideMap(false);
						});
					}

					function " . $this->getId() . "createMarkerByCordinate(latlng){
						if(latlng){
							" . $this->getId() . "showMap();
							" . $this->getId() . "createMarker(latlng.lat(), latlng.lng());
						} else " . $this->getId() . "hideMap(false);
					}
					", View::POS_BEGIN);

					Yii::$app->view->registerJsFile('https://maps.googleapis.com/maps/api/js?key=' . $this->_googleApiKey . '&libraries=places&callback=' . $this->getId() . 'init', ['position' => View::POS_END]);
				}
			],
			'profile' => 
			[
				'view' => 'basic',
				'params' => 
				[
					'enableMap' => false,
					'enableCordinate' => false,
					'enableCustomStateCountryIso' => false,
					'enableSearchLocation' => false,
					'enableVenue' => false,
					'enableCity' => $this->setting['enableCity'],
					'enableAddress' => $this->setting['enableAddress'],
					'enableCustomeStateCountryId' => $this->setting['enableCustomeStateCountryId'],
					'enableCustomeStateZoneId' => $this->setting['enableCustomeStateZoneId'],
					'mapWidth' => $this->setting['mapWidth'],
					'formViewAddressResetCoordinates' => $this->setting['formViewAddressResetCoordinates'],
					'enableLargeGoogleMap' => $this->setting['enableLargeGoogleMap'],
				],
				'registerJs' => false
			],
		];
	}
	
	/**
     * @inheritdoc
     */
	public function init()
	{
		//Parent init
		parent::init();

		$this->_setting = $this->getModeSetting()[$this->mode];

		if(!$this->model->isNewRecord && !$this->model->currentForm ) 

			$this->model->currentForm = self::FORM_VIEW_ADDRESS;

		$this->_setting['params'] = ArrayHelper::merge($this->_setting['params'], [
			'form' => $this->form,
			'model' => $this->model	
		]);		

		if($this->_setting['registerJs']) $this->_setting['registerJs']();
	}

	/**
     * @inheritdoc
     */
	public function run()
	{
		return $this->render($this->_setting['view'], $this->_setting);
	}
}
?>