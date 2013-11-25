window.addEvent('domready', function() {
	var status = {
		'false': 'open',
		'true': 'close'
	};
	
	//-vertical

	var myVerticalSlide = new Fx.Slide('vertical_slide');

	myVerticalSlide.hide();
	$('v_toggle').addEvent('click', function(e){
		e.stop();
		myVerticalSlide.toggle();
	});

	
	// When Vertical Slide ends its transition, we check for its status
	// note that complete will not affect 'hide' and 'show' methods
	myVerticalSlide.addEvent('complete', function() {
		$('vertical_status').set('html', status[myVerticalSlide.open]);
	});


});
