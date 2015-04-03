jQuery('.toggler:not(.active)').next('div').hide();

jQuery('.toggler').click(function() {        
    
    if ( jQuery(this).hasClass('active') ) {
        jQuery('.active').toggleClass('active').next('div').hide("fast");  
    }
    
    else {
        jQuery('.active').toggleClass('active').next('div').hide();  
        jQuery(this).toggleClass("active").next().slideToggle("fast");
    }
    
    
});
