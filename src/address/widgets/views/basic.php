<?php  
use ant\address\widgets\Address;
?>
<div class="row">
	<div class="col-md-12">
		<?=$this->render(Address::FORM_VIEW_ADDRESS, $params); ?>
	</div>
</div>