var _ie = {
	components: {
		init: function(target){
			target.find(".color-overlay").hide();
			target.find(".overlay").each(function(){
				$(this).children(".color-overlay").hide();
				$(this).children(".content-overlay").hide();
				$(this).hover(function(){
					$(this).children(".color-overlay").stop(true , false).fadeTo(300 , 0.6);
					$(this).children(".content-overlay").stop(true , false).fadeTo(300 , 1);
				} , 
				function(){
					$(this).children(".color-overlay").stop(true , false).fadeTo(300 , 0);
					$(this).children(".content-overlay").stop(true , false).fadeTo(300 , 0);
				});
			});
		}
	}
}