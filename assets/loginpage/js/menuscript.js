$(".nav li").hover(function () {
	$(this).children("ul").stop().delay(200).animate({height: "toggle", opacity: "toggle"}, 200);
});