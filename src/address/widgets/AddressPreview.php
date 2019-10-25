<?php  
namespace ant\address\widgets;

use Yii;
use yii\web\View;
use yii\bootstrap\Widget;
use yii\helpers\ArrayHelper;

use ant\helpers\ScriptHelper;

class AddressPreview extends Widget
{
	public $latitude;

	public $longitude;

	private $_googleApiKey = 'AIzaSyB2g6IpGlGR42JPUqRfaqjNvseD9VAz8sI';

	public function getId($autoGenerate = true)
	{
		return ScriptHelper::identifierSanitizer(parent::getId($autoGenerate));
	}

	/**
     * @inheritdoc
     */
	public function init()
	{
		//Parent init
		parent::init();

		Yii::$app->view->registerJs("
		function " . $this->getId() . "init(){
			var " . $this->getId() . "map; 

			var " . $this->getId() . "marker = null;
			
			" . $this->getId() . "map = new google.maps.Map(document.getElementById('" . $this->getId() . "map'), {
		        center: {lat: -33.8688, lng: 151.2195},
		        zoom: 13,
		        mapTypeId: 'roadmap',
		        scrollwheel: false,
		        animation: google.maps.Animation.DROP,
		        types: ['geocode'],
				mapTypeControl: false,
				streetViewControl: false,
				fullscreenControl: false
		    });
			
			var latitude = " . $this->latitude . ";
			var longitude = " . $this->longitude . ";

		    " . $this->getId() . "marker = new google.maps.Marker({
				map: " . $this->getId() . "map,
				animation: google.maps.Animation.DROP,
				position: {lat: latitude, lng: longitude}
		    });

		    var bounds = new google.maps.LatLngBounds();

		    var googleLatLng = new google.maps.LatLng(latitude, longitude);
		    bounds.extend(googleLatLng);

		    if (bounds.getNorthEast().equals(bounds.getSouthWest())) {
		       var extendPoint1 = new google.maps.LatLng(bounds.getNorthEast().lat() + 0.01, bounds.getNorthEast().lng() + 0.01);
		       var extendPoint2 = new google.maps.LatLng(bounds.getNorthEast().lat() - 0.01, bounds.getNorthEast().lng() - 0.01);
		       bounds.extend(extendPoint1);
		       bounds.extend(extendPoint2);
		    }
			
			" . $this->getId() . "map.fitBounds(bounds);
			    
		}
		", View::POS_BEGIN);

		Yii::$app->view->registerJsFile('https://maps.googleapis.com/maps/api/js?key=' . $this->_googleApiKey . '&libraries=places&callback=' . $this->getId() . 'init', ['position' => View::POS_END]);
	}

	/**
     * @inheritdoc
     */
	public function run()
	{

		return $this->render('AddressPreview', [
			'latitude' => $this->latitude,
			'longitude' => $this->longitude,
		]);
	}
}
?>