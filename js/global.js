/**
 * This script adds the jquery effects to the Altitude Pro Theme.
 *
 * @package Altitude Pro\JS
 * @author StudioPress
 * @license GPL-2.0-or-later
 */

jQuery( function( $ ) {

	if ( 0 < $( document ).scrollTop() ) {
		$( '.site-header' ).addClass( 'dark' );
	}

	// Add opacity class to site header.
	$( document ).on( 'scroll', function() {

		if ( 0 < $( document ).scrollTop() ) {
			$( '.site-header' ).addClass( 'dark' );

		} else {
			$( '.site-header' ).removeClass( 'dark' );
		}

	});

});


jQuery(document).ready(function($) {
	
	var imgLi = $('.custom-form1 .initial-choices .gfield_radio li');
	$(document).on('click', '.custom-form1 .initial-choices .gfield_radio li', function(){
		imgLi.removeClass('selected');
		$(this).addClass('selected');
	});
	
	$(document).on('click', '.tg-item.honiara a', function(e){
		e.preventDefault();
	});
	
	$(document).on('click', '.tg-item.honiara', function(){
		$(this).find('.tg-media-button').trigger('click');
	});
	
	$(document).on('click', '.request-stone-quote-btn', function(){
		var stone_name = $(this).attr('data-title');
		$('.stone-name-autofill input').val(stone_name);
	});
});