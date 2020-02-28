/* ------------------------------------------------------------------------------- */
/* TEMPLATE
/* ------------------------------------------------------------------------------- */

var template = {

/* ========== template configuration ========== */

	config: {
		site_location			: "" , /* ex: http://www.yourdomain.com/truestory/ (don't forget the trailing slash) */
		use_page_ajax_loading	: false , /* specify whether you want to use ajax loading or not */
		error_404_page			: "404.html" ,
		showSubmenuOn			: "click" , /* click | hover */
		
		portfolio_initialCount: 20 , /* initial number of entries that should be requested by the ajax call */
		portfolio_count: 10 , /* number of entries that should be requested by the ajax call on page scroll */
		portfolio_masonry_unitWidth: 320 ,
		portfolio_grid_unitWidth: 320 ,
		portfolio_metro_unitWidth: 320 ,
		portfolio_metro_dynamic: false , /* set unit size based on the size of the image */
		portfolio_metro_pattern: false /* pattern to follow when sizing metro layout units
										
										example: 
										-------
										portfolio_metro_pattern: [
														"1-1" ,
														"2-1" ,
														"1-2" ,
														"3-1" ,
														"1-3" ,
														"2-2"
													] ,
										*/
	} ,
	
/* ========== initialize the template ========== */

	init: function(){
		this.config.showSubmenuOn = (this.config.showSubmenuOn == "hover" ? "mouseenter" : "click");
		
		/* init dock */
		this.dock.init();
		
		/* init page */
		this.page.init();
		
		/* init preloader */
		this.preloader.init();
		
		/* init background */
		this.background.init();
		
		/* handle window resize */
		$(window).resize(function(){
			clearTimeout(template.windowResizeTO);
			template.windowResizeTO = setTimeout(function(){
				template.windowResize();
			} , 200);
		});
		template.windowResize();
		
		/* handle window scroll */
		$(window).scroll(function(){
			template.windowScroll();
		});
		template.windowScroll();
		
		/* handle broken images */
		$("body").find("img").error(function(){
			$(this).data("404" , true);
		});
		
		/* init animator */
		template.animator.init();
		
		/* handle history popstate */
		$(window).bind("popstate" , function(){
			if(history.pushState){
				var url = location.href.slice(location.href.lastIndexOf("/") + 1);
				
				/* don't load the same page */
				if(url == "#" || url == "" || url.indexOf("#",0) == url.length - 1 || url == template.page.url) return false;
				
				template.page.load(url , false);
			}
		});
		
		$("a.ajax,button.ajax").click(function(){
			if(template.config.use_page_ajax_loading){
				var href = $(this).attr("href") || $(this).attr("data-href");
				template.page.load(href);
				return false;
			}
		});
	} ,
	
	windowResizeTO: false ,
	windowResize: function(){
		/* update header and footer height */
		if(!template.dock.header.menu.active){
			this.dock.header.height = this.dock.header.element.outerHeight();
			this.dock.footer.height = this.dock.footer.element.outerHeight();
		}
		
		/* update dock */
		if(this.dock.hidden){
			this.dock.hide();
			this.dock.element.stop(true , true);
		}else{
			this.dock.show();
			this.dock.element.stop(true , true);
		}
		
		/* update dock buttons */
		this.dock.toggleDock.mouseout();
		this.dock.toggleDock.element.stop(true , true);
		this.dock.pageTopLink.mouseout();
		this.dock.pageTopLink.element.stop(true , true);
		
		/* resize the background */
		this.background.resize();
		
		/* resize the preloader */
		this.preloader.resize();
		
		/* re-order portfolio items */
		this.page.components.portfolio.filterable.refresh();
		this.page.components.portfolio.layouts.order();
		
		/* resize home accordion */
		this.page.components.fullscreenComponents.homeAccordion.resize();
	} ,
	
	windowScroll: function(){
		/* handle page-top link opacity */
		this.dock.pageTopLink.updateOpacity();
		
		/* handle portfolio scroll */
		this.page.components.portfolio.layouts.scroll();
	} ,
	
/* ========== page ========== */
	
	page: {
		element: $("#page") ,
		url: false ,
		loading: false ,
		data: false ,
		onReady: false ,
		
		init: function(){
			this.components.init(this.element);
		} ,
		
		load: function(url , pushState){
			/* don't load the same page */
			if(url == "#" || url == "" || url.indexOf("#",0) == url.length - 1 || url == template.page.url) return false;
			
			this.loading = true;
			
			/* cancel any on-going requests */
			if(window.stop !== undefined){
				window.stop();
			}else
			if(document.execCommand !== undefined){
				document.execCommand("Stop", false);
			}
			
			template.page.components.portfolio.layouts.element = false;
			
			/* update the url */
			if(url.indexOf("http",0) != 0) url = template.config.site_location + url;
			if(history.pushState && pushState == undefined) history.pushState(null , null , url);
			
			template.page.element.stop(true , false).animate({"opacity": 0} , 300 , "easeInOutCubic" , function(){
				template.preloader.onStart = function(){
					template.page.startLoading(url);
				}
				
				template.preloader.onFinish = function(){
					template.page.finishLoading();
				}
				
				template.preloader.show();
			});
			
			template.background.element.stop(true , false).delay(300).animate({"opacity": 0} , 300 , "easeInOutCubic");
		} ,
		
		startLoading: function(url){			
			/* load the page data via an ajax call */
			$.ajax({
				type: "get" ,
				dataType: "html" ,
				cache: false ,
				url: url ,
				success: function(data){
					var page = template.page;
					var dataObj = $(data);
					
					page.data = {};
					
					/* set the current url */
					page.url = url;
					
					/* get page title */
					var a = data.indexOf("<title>") + "<title>".length;
					var b = data.indexOf("</title>");
					page.data.title = $("<div />").html($.trim(data.slice(a , b))).text();
					
					/* get page */
					page.data.page = dataObj.find('#page');
					
					/* get scripts */
					page.data.scripts = dataObj.filter('script');
					
					/* get page background */
					page.data.background = dataObj.find('#background');
					
					/* update page */
					if(page.onReady != false){
						page.onReady();
						page.onReady = false;
					}else{
						template.preloader.hide();
					}
				} ,
				error: function(xhr , textStatus , thrownError){
					console.log("XHR:" + xhr);
					console.log("TEXT_STATUS:" + textStatus);
					console.log("ERROR:" + thrownError);
					
					if(xhr.status == 404) {
						page.load(template.config.error_404_page);
					}
				}
			});
		} ,
		
		finishLoading: function(){
			/* update page title */
			document.title = this.data.title;
			
			/* scroll to the top */
			var dur = $(window).scrollTop() == 0 ? 0 : Math.min(Math.max(400 , $(window).scrollTop()) , 1000);
			$(window.opera ? "html" : "html, body").stop(true , false).animate({scrollTop: 0} , dur , "easeInOutCubic");
			
			setTimeout(function(){
				/* update background */
				var newBackground = template.page.data.background.clone();
				newBackground.css({"opacity": 0});
				
				template.background.element.after(newBackground);
				template.background.element.remove();
				template.background.element = newBackground;
				template.background.reset();
				template.background.init();
				
				/* update page */
				var newPage = template.page.data.page.clone();
				newPage.css({"opacity": 0});
				
				template.page.element.after(newPage);
				template.page.element.remove();
				template.page.element = newPage;
				template.page.components.init(template.page.element);
				
				template.windowResize();
				template.windowScroll();
				template.dock.stateChange();
				
				/* execute script tags */
				template.page.data.scripts.each(function(){
					$.globalEval(this.text || this.textContent || this.innerHTML || '');
				});
				
				/* show page */
				template.page.element.stop(true , false).delay(300).animate({"opacity": 1} , 300 , "easeInOutCubic" , function(){
					template.page.loading = false;
				});
				
				/* show background */
				template.background.element.stop(true , false).delay(600).animate({"opacity": 1} , 300 , "easeInOutCubic");
			} , dur);
		} ,
		
		components: {
			init: function(target){
				var components = this;
				template.imagesReady(components , function(){
					/* init components */
					components.imageCropper.init(target);
					components.textInputs.init(target);
					components.portfolio.init(target);
					components.colorbox.init(target);
					components.flexslider.init(target);
					components.mediaelement.init(target);
					components.tabs.init(target);
					components.toggle.init(target);
					components.twitter.init(target);
					components.flickr.init(target);
					components.contact.init(target);
					components.fullscreenComponents.homeAccordion.init(target);
					components.fullscreenComponents.homeSlider.init(target);
					
					components.ie_init(target);
					
					target.find("a.ajax,button.ajax").click(function(){
						if(template.config.use_page_ajax_loading){
							var href = $(this).attr("href") || $(this).attr("data-href");
							template.page.load(href);
							return false;
						}
					});
				});
			} ,
			
			ie_init: function(target){
				if(window._ie != undefined){
					window._ie.components.init(target);
				}
			} ,
			
			imageCropper: {
				init: function(target){
					target.find(".crop").each(function(){
						$(this).css({
							"overflow": "hidden" ,
							"position": "relative"
						});
						
						var cropped = $(this);
						
						$(this).data("update" , function(){
							var w = cropped.width();
							var h = cropped.height();
							
							cropped.children().each(function(){
								if($(this).is("img")){
									var w0 = this.width;
									var h0 = this.height;
									
									$(this).width(w);
									$(this).height(h0*w/w0);
									
									if($(this).height() < h){
										$(this).height(h);
										$(this).width(w0*h/h0);
									}
									
									$(this).css({
										"position": "absolute" ,
										"top": -($(this).height() - h)/2 ,
										"left": -($(this).width() - w)/2
									});
								}
							});
						});
						$(this).data("update")();
					});
				}
			} ,
			
			textInputs: {
				init: function(target){
					target.find("input[type='text'],input[type='email'],input[type='search'],input[type='tel'],input[type='url'],input[type='password'],textarea").each(function(){
						var prompt = $(this).data("prompt");
						
						if(prompt == undefined) return;
						
						$(this).val(prompt).data("original-value" , prompt);
						if($(this).val() == prompt) $(this).addClass("js-original");
						
						$(this).keyup(function(){
							if($(this).val().length == 0){
								$(this).removeClass("has-input");
							}else{
								$(this).addClass("has-input");
							}
						});
						
						$(this).focusin(function(){
							if($(this).val() == $(this).data("original-value")) $(this).val("");
							$(this).removeClass("js-original");
						});
						
						$(this).focusout(function(){
							if($(this).val() == ""){
								$(this).val($(this).data("original-value"));
								$(this).addClass("js-original");
							}
						});
					});
				}
			} ,
			
			portfolio: {
				init: function(target){
					this.layouts.initialCount = template.config.portfolio_initialCount;
					this.layouts.count = template.config.portfolio_count;
					this.layouts.masonry.unitWidth = template.config.portfolio_masonry_unitWidth;
					this.layouts.grid.unitWidth = template.config.portfolio_grid_unitWidth;
					this.layouts.metro.unitLength = template.config.portfolio_metro_unitWidth;
					this.layouts.metro.dynamic = template.config.portfolio_metro_dynamic;
					if(template.config.portfolio_metro_pattern != false) this.layouts.metro.pattern = template.config.portfolio_metro_pattern;
					
					this.filterable.init(target);
					this.layouts.init(target);
				} ,
				
				layouts: {
					element: false ,
					initialCount: 20 ,
					count: 10 ,
					height: 0 ,
					data: false ,
					interval: false ,
					loading: false ,
					type: false ,
					
					init: function(target){
						this.element = target.find(".portfolio.entries").eq(0);
						
						if(this.element.size() > 0){
							if(this.element.hasClass("single")){
								this.element = false;
								return;
							}
							
							if(this.element.hasClass("masonry")){
								this.type = "masonry";
							}else
							if(this.element.hasClass("grid")){
								this.type = "grid";
							}else
							if(this.element.hasClass("metro")){
								this.type = "metro";
							}
						}else{
							this.element = false;
						}
						
						if(this.type){
							this.load(this.initialCount);
						}
					} ,
					
					load: function(count){
						if(!this.type || !this.element) return;
						
						var layouts = this;
						layouts.loading = true;
						
						if(!count) count = layouts.count;
						
						template.preloader.onStart = function(){
							if(!layouts.element) return;
							
							$.ajax({
								type: "post" ,
								dataType: "html" ,
								url: (layouts.element.data("loader") || "ajax_portfolio_entries.php") + "?from=" + layouts.element.children().size() + "&count=" + count ,
								success: function(data){
									if(!layouts.type || !layouts.element) return;
									
									layouts.data = $("<div>" + data + "</div>");
									
									if(data == "none"){
										layouts.finish();
										return;
									}
									
									layouts.data.children().each(function(){
										$(this).appendTo(layouts.element).addClass("newitem").css({
											"opacity": 0 ,
											"visibility": "hidden"
										});
									});
									
									layouts.dataLoaded();
								} ,
								
								error: function(xhr , textStatus , thrownError){
									layouts.dataError();
								}
							});
						};
						
						template.preloader.show();
					} ,
					
					dataLoaded: function(){
						var layouts = this;
						
						template.preloader.onFinish = function(){
							/* init layout */
							layouts.typeInit();
							
							/* create layout */
							layouts.order();
							
							/* show newly added items */
							layouts.element.find(".newitem").each(function(i){
								$(this).css({
									"visibility": "visible"
								}).removeClass("newitem").delay(200 + 100*i).animate({"opacity": 1} , 600 , "easeInOutCubic");
								
								/* init components */
								template.page.components.colorbox.init($(this));
								template.page.components.ie_init($(this));
							});
						};
						
						template.imagesReady(layouts.element , function(){
							template.preloader.hide();
						});
					} ,
					
					dataError: function(){
						console.log("ajax error");
					} ,
					
					order: function(){
						if(this.type){
							this[this.type].order(this);
						}
					} ,
					
					finish: function(){
						/* no more entries */
						template.preloader.hide();
					} ,
					
					scroll: function(){
						if(!this.element) return;
						
						if($(window).scrollTop() + $(window).height() - this.height >= -500 && !this.loading){
							this.load();
						}
					} ,
					
					typeInit: function(){
						if(this.type){
							this[this.type].init(this);
						}
					} ,
					
					masonry: {
						unitWidth: 320 ,
						
						init: function(layouts){
							
						} ,
						
						order: function(layouts){
							if(!layouts.data || !layouts.element) return;
							
							layouts.height = 0;
							var count = Math.floor(layouts.element.width()/this.unitWidth);
							var w = Math.ceil(layouts.element.width()/count);
							
							/* set item width */
							layouts.element.children().each(function(){
								$(this).css({"width": Math.floor(w)}).data("resv" , false);
							});
							
							/* set item position */
							layouts.element.children().each(function(){
								var top = 0;
								var left = ($(this).index()%count)*w;
								
								if($(this).index() > count - 1){
									var item;
									top = 1.7976931348623157E+10308;
									
									layouts.element.children(":lt(" + $(this).index() + ")").each(function(){
										var t = parseInt($(this).css("top")) + $(this).outerHeight();
										
										if(t < top && !$(this).data("resv")){
											top = t;
											left = parseInt($(this).css("left"));
											item = $(this);
										}
									});
											
									if(item) item.data("resv" , true);
								}
								
								$(this).css({
									"top": Math.floor(top) ,
									"left": Math.floor(left)
								});
								
								layouts.height = Math.max(layouts.height , parseInt($(this).css("top")) + $(this).outerHeight());
							});
							
							layouts.element.height(layouts.height);
							layouts.loading = false;
						}
					} ,
					
					grid: {
						unitWidth: 320 ,
						
						init: function(layouts){
							var count = Math.floor(layouts.element.width()/this.unitWidth);
							var w = Math.ceil(layouts.element.width()/count);
							
							layouts.element.children(".newitem").each(function(){
								$(this).find("img").wrap("<div class='crop' style='width: " + w + "px; height: " + w + "px;'></div>");
								template.page.components.imageCropper.init($(this));
							});
						} ,
						
						order: function(layouts){
							if(!layouts.data || !layouts.element) return;
							
							var count = Math.floor(layouts.element.width()/this.unitWidth);
							var w = Math.ceil(layouts.element.width()/count);
							
							layouts.element.children().each(function(){
								$(this).find(".crop").each(function(){
									$(this).width(w);
									$(this).height(w);
									$(this).data("update")();
								});
							});
							
							layouts.masonry.order(layouts);
						}
					} ,
					
					metro: {
						unitLengthRef: 240 ,
						unitLength: 320 ,
						dynamic: false , /* set unit size based on the size of the image */
						count: 4 ,
						segments: [] ,
						pattern: [
								"1-1" ,
								"2-1" ,
								"1-2" ,
								"3-1" ,
								"1-3" ,
								"2-2"
								] ,
						
						init: function(layouts){
							layouts.element.children(".newitem").each(function(){
								$(this).find("img").wrap("<div class='crop' style='width: " + layouts.metro.unitLength + "px; height: " + layouts.metro.unitLength + "px;'></div>");
								template.page.components.imageCropper.init($(this));
							});
						} ,
						
						order: function(layouts){
							if(!layouts.data || !layouts.element) return;
							
							var metro = this;
							
							metro.count = Math.floor(layouts.element.outerWidth()/metro.unitLengthRef);
							metro.unitLength = Math.ceil(layouts.element.width()/metro.count);
							
							/* set unit dimensions */
							var size = 0;
							layouts.element.children().each(function(){
								var s = metro.pattern[size].split("-");
								
								if($(this).attr("data-size") != undefined){
									s = $(this).attr("data-size").split("-");
								}else{
									size++;
									if(size > metro.pattern.length - 1) size = 0;
								}
								
								$(this).addClass("size-" + metro.pattern[size]);
								
								var crop = $(this).find(".crop").eq(0);
								var img = crop.children("img").get(0);
								var w = Number(s[0])*metro.unitLength;
								var h = Number(s[1])*metro.unitLength;
								
								if(metro.dynamic){
									w = Math.floor(Math.max(metro.unitLength , img.naturalWidth*0.8)/metro.unitLength)*metro.unitLength;
									h = Math.floor(Math.max(metro.unitLength , img.naturalHeight*0.8)/metro.unitLength)*metro.unitLength;
								}
								
								crop.css("width" , w);
								crop.css("height" , h);
								if(crop.data("update")) crop.data("update")();
								$(this).css("width" , w);
								$(this).css("height" , h);
								
							});
							
							metro.segments = [];
							metro.getSegments();
							
							layouts.height = 0;
							
							/* set unit position */
							layouts.element.children().each(function(){
								var hcount = parseInt($(this).css("width"))/metro.unitLength;
								var vcount = parseInt($(this).css("height"))/metro.unitLength;
								var position = 0;
								
								hcount = Math.min(3 , hcount);
								vcount = Math.min(3 , vcount);
								
								var n = 1;
								for(var a = 0 ; a < n ; a++){
									// find the first free segment
									for(i = position ; i < metro.segments.length ; i++){
										if(metro.segments[i].free){
											position = i;
											break;
										}
									}
								
									// make sure the set of segments is available
									var base = metro.segments[position];
									for(i = 0 ; i < hcount*vcount ; i++){
										var segment = metro.segments[base.index + metro.count*Math.floor(i/hcount) + (i%hcount)];
										if(segment.free == false || segment.x < base.x){
											n++;
											position++;
											break;
										}
									}
								}
								
								// the set of segments is available, let's place the block
								$(this).css({"left":base.x , "top":base.y});
								for(i = 0 ; i < hcount*vcount ; i++){
									metro.segments[base.index + metro.count*Math.floor(i/hcount) + i%hcount].free = false;
								}
								
								layouts.height = Math.max(layouts.height , base.y + $(this).outerHeight());
							});
							
							layouts.element.height(layouts.height);
							layouts.loading = false;
						} ,
						
						getSegments: function(){
							var n = this.segments.length;
							for(var i = n ; i < n + 5000 ; i++){
								this.segments.push({index:i , x:(i%this.count)*this.unitLength , y:Math.floor(i/this.count)*this.unitLength , free: true});
							};
						}
					}
				} ,
				
				filterable: {
					gutter: 30 ,
					
					init: function(target){
						var filterable = this;
						
						target.find(".filterable").each(function(){
							var cols = 1;
							var cls = $(this).attr("class");
							var ci = cls.indexOf("classic-",0);
							
							if(ci != -1){
								var c = cls.charAt(ci + "classic-".length);
								if(!isNaN(c)){
									cols = c;
								}
							}
							
							filterable.build($(this) , Number(cols));
						});
					} ,
					
					build: function(target , cols){
						var filterable = this;
						var items = target.children();
						
						target.data("items" , items);
						
						/* auto create tags */
						items.each(function(){
							if($(this).attr("data-tags") == undefined){
								var a = [];
								$(this).find(".entry-tags > *").not(".separator").each(function(){
									a.push($(this).text());
								});
								$(this).attr("data-tags" , a.join(","));
							}
						});
						
						/* create lists */
						var itemsByTags = {};
						var nav = $(".filterable-nav[data-id=" + target.data("id") + "] > ul");
						var search = $("input[data-id=" + target.data("id") + "]");
						var allText = nav.children().eq(0).html();
						
						nav.empty();
						
						/* get items by tags */
						items.each(function(i , obj){
							if($(obj).data("tags") != undefined){
								var tags = $(obj).data("tags").split(",");
								$(obj).attr("data-id" , i);
								$.each(tags , function(key , value){
									value = $.trim(value);
									
									if(!(value in itemsByTags)){
										itemsByTags[value] = [];
									}
									
									itemsByTags[value].push($(obj));
								});
							}
						});
						
						/* create the default list which contains all items */
						filterable.createList(allText , items , nav);
						
						/* create a list for each tag */
						$.each(itemsByTags , function(k , v){
							filterable.createList(k , v , nav);
						});
						
						/* handle filter by tags */
						nav.children().on("click" , function(e){
							$(this).addClass("active").siblings().removeClass("active");
							search.val("");
							search.focusout();
							filterable.update(target , $(this).data("list").data("ids") , cols , true);
							return false;
						});
						
						/* handle filter by search */
						search.keyup(function(){
							var str = $(this).val();
							
							clearTimeout($(this).data("to"));
							$(this).data("to" , setTimeout(function(){
								var matches = [];
								
								/* search for matches */
								items.each(function(){
									if($(this).text().toLowerCase().indexOf(str.toLowerCase() , 0) != -1){
										matches.push($(this).data("id"));
									}
								});
								
								/* update active tag */
								if(str == ""){
									nav.children().eq(0).addClass("active");
								}else{
									nav.children().removeClass("active");
								}
								
								/* update items */
								filterable.update(target , matches , cols , true);
							} , 500));
						});
						
						/* show all items */
						nav.children().eq(0).addClass("active");
						this.update(target , nav.children().eq(0).data("list").data("ids") , cols , false);
					} ,
					
					createList: function(text , items , nav){
						var ul = $("<ul/>");
						var ids = [];
						
						$.each(items , function(i){
							var item = $(this).clone();
							item.appendTo(ul);
							ids.push(item.data("id"));
						});
						
						ul.data("ids" , ids);
						
						var li = $("<li>",{
							html: text ,
							data: {list:ul}
						});
						li.wrapInner("<a href='#' />").appendTo(nav);
					} ,
					
					update: function(target , list , cols , animate){
						var filterable = this;
						
						/* update items */
						target.children().not(".default").each(function(){
							if($.inArray(Number($(this).data("id")) , list) != -1){
								$(this).addClass("active");
							}else{
								$(this).removeClass("active");
							}
						});
						
						/* update positions */
						var items = target.children(".active");
						var y1 = 0;
						var y2 = 0;
						
						items.each(function(i){
							if(i%cols == 0){
								y1 = y2;
							}
							
							var w = (target.width() - filterable.gutter*(cols-1))/cols;
							$(this).width(w);
							$(this).css("position" , "absolute");
							$(this)[animate ? "data" : "css"]({
								"top": y1 ,
								"left": (i%cols)*(filterable.gutter + w)
							});
							
							y2 = Math.max(y2 , y1 + $(this).outerHeight() + filterable.gutter);
						});
						
						/* toggle default message */
						var defaultMessage = target.find(".default");
						
						if(list.length == 0){
							defaultMessage.delay(600).fadeIn(150);
							y2 = defaultMessage.outerHeight();
						}else{
							if(!defaultMessage.is(":hidden")){
								defaultMessage.fadeOut(150);
							}
						}
						
						/* --- animation --- */
						
						if(animate != true){
							target.css({"height": y2});
							return;
						}
						
						var hideDur = 600;
						var showDur = 1000;
						var interval = 0;
						
						/* hide inactive items */
						target.children().not(".default").not(".active").each(function(i){
							$(this).stop(true , false).delay(i*interval).animate({
								"opacity": 0
							} , hideDur , "easeInOutCubic" , function(){
								$(this).hide();
							});
						});
						
						/* show active items */
						items.each(function(i){
							$(this).show();
							$(this).stop(true , false).delay(i*interval).css({
								"display": "block"
							}).animate({
								"opacity": 1 ,
								"top": $(this).data("top") ,
								"left": $(this).data("left")
							} , showDur , "easeInOutCubic");
						});
						
						target.stop(true , false).animate({"height": y2} , items.size()*interval + showDur , "easeInOutCubic");
					} ,
					
					refresh: function(){
						$(".filterable").each(function(){
							var nav = $(".filterable-nav[data-id=" + $(this).data("id") + "] > ul");
							if(nav.size() > 0){
								nav.find(".active").click();
							}
						});
					}
				} ,
				
			} ,
			
			colorbox: {
				init: function(target){
					target.find(".colorbox").each(this.createColorbox);
				} ,
				
				createColorbox: function(i , obj){
					var $colorboxArgs = new Object();
					$colorboxArgs.innerWidth		= $(obj).data("width") || false;
					$colorboxArgs.innerHeight 		= $(obj).data("height") || false;
					$colorboxArgs.slideshow 		= String($(obj).data("slideshow")).toLowerCase() == "true";
					$colorboxArgs.slideshowAuto 	= String($(obj).data("slideshowauto")).toLowerCase() == "true";
					$colorboxArgs.iframe 			= String($(obj).data("iframe")).toLowerCase() == "true";
					$colorboxArgs.inline 			= String($(obj).data("inline")).toLowerCase() == "true";
					$colorboxArgs.photo  			= String($(obj).data("photo")).toLowerCase() == "true";
					$colorboxArgs.title	  			= $(obj).data("title");
					$colorboxArgs.rel	  			= $(obj).data("group");
					$colorboxArgs.fixed  			= true;
					$colorboxArgs.transition  		= "none";
					
					if(!$(obj).is("a")) $colorboxArgs.href = $(obj).data("href");
					if(!$colorboxArgs.innerWidth) $colorboxArgs.innerWidth = $colorboxArgs.iframe ? 480 : false;
					if(!$colorboxArgs.innerHeight) $colorboxArgs.innerHeight = $colorboxArgs.iframe ? 360 : false;
					
					$colorboxArgs.width = "80%";
					$colorboxArgs.height = "80%";
					$colorboxArgs.maxWidth = "100%";
					$colorboxArgs.maxHeight = "100%";
					$colorboxArgs.overlayClose = false;
					$colorboxArgs.speed = 600;
					$colorboxArgs.opacity = 1;
					$colorboxArgs.current = "{current}/{total}";
					
					if($colorboxArgs.iframe){
						$colorboxArgs.width = 640;
						$colorboxArgs.height = 480;
					}
					
					$colorboxArgs.onComplete = function(){
						if($("#cboxLoadedContent").children().eq(0).is("img")){
							$("#cboxLoadedContent").hide().delay(500).fadeIn(400);
						}
						
						if($("#colorbox").data("init")) return;
						$("#colorbox").data("init" , true);
						$("#colorbox")[$("#cboxSlideshow").css("display") != "none" ? "addClass" : "removeClass"]("cb-slideshow");
						$("#colorbox")[$("#cboxNext").css("display") != "none" ? "addClass" : "removeClass"]("cb-group");
						$("#cboxTitle").css("bottom" , "-100%").delay(200).animate({"bottom": 0} , 800 , "easeInOutCubic");
					}//end onComplete
					
					$colorboxArgs.onOpen = function(){
						$("#cboxLoadingGraphic").css("opacity" , "0").delay(200).animate({"opacity": 1} , 800 , "easeInOutCubic");
						$("#cboxClose").css("margin-right" , "-220px").delay(900).animate({"margin-right": 0} , 800 , "easeInOutCubic");
						$("#cboxNext").css("margin-right" , "-220px").delay(850).animate({"margin-right": 0} , 800 , "easeInOutCubic");
						$("#cboxPrevious").css("margin-right" , "-220px").delay(800).animate({"margin-right": 0} , 800 , "easeInOutCubic");
						$("#cboxCurrent").css("margin-right" , "-220px").delay(750).animate({"margin-right": 0} , 800 , "easeInOutCubic");
					}//end onComplete
					
					$colorboxArgs.onClose = function(){
						$("#colorbox").data("init" , false);
					}//end onComplete
					
					$(obj).colorbox($colorboxArgs);
				}
			} ,
			
			flexslider: {
				init: function(target){
					target.find(".flexslider").each(this.createFlexSlider);
				} ,
				
				createFlexSlider: function(i , obj){
					obj = $(obj);
					
					var $controls = obj.data("controls") == false ? false : true;
					var $nav = obj.data("nav") == false ? false : true;
					var $slideshow = obj.data("slideshow") == false ? false : true;
					var $animation = obj.data("animation") == "slide" ? "slide" : "fade";
					var $pauseOnHover = obj.data("pauseonhover") == false ? false : true;
					var $autohide = obj.data("autohide") == false ? false : true;
					var $slideshowSpeed = obj.data("duration") == undefined ? 5000 : Number(obj.data("duration"));
					var $animationSpeed = obj.data("speed") == undefined ? 600 : Number(obj.data("speed"));
					
					obj.flexslider({
						directionNav: false ,
						controlNav: $controls ,
						pauseOnAction: false ,
						pauseOnHover: $pauseOnHover ,
						animation: $animation ,
						easing: "easeInOutQuad" ,
						slideshow: $slideshow ,
						slideshowSpeed: $slideshowSpeed ,
						animationSpeed: $animationSpeed ,
						smoothHeight: true ,
						start: function(slider){
							slider.height(slider.children(".slides").height()); /* fix 0 height bug when images are not cached */
							
							var controlsRight = $("<div class = 'controls right-nav'></div>");
							var nextNav = $("<a class = 'next'>Next</a>");
							var prevNav = $("<a class = 'prev'>Prev</a>");
							var controlNav = slider.find(".flex-control-nav");
							
							slider.find(".flex-control-nav").css("visibility" , "visible"); /* fix for the controls glitch */
							
							if(!$nav){
								return;
							}else{
								slider.addClass("has-nav");
							}
							
							controlsRight.append(prevNav).append(nextNav);
							slider.append(controlsRight);
							
							nextNav.click(function(){
								slider.flexAnimate(slider.getTarget("next"));
							});
							
							prevNav.click(function(){
								slider.flexAnimate(slider.getTarget("prev"));
							});
							
							/* left nav */
							var controlsLeft = controlsRight.clone(true).removeClass("right-nav").addClass("left-nav").appendTo(slider);
							var nextNavLeft = controlsLeft.find("a.next");
							
							controlsRight.prepend(nextNav);
							
							/* actions */
							controlNav.appendTo(controlsRight).after(controlNav.prev());
							prevNav.stop(true , true).css({"right": -prevNav.outerWidth() - 40 , "opacity": 0});
							nextNavLeft.stop(true , true).css({"left": -prevNav.outerWidth() - 40 , "opacity": 0});
							controlNav.stop(true , true).css({"right": -controlNav.outerWidth() - 40 , "opacity": 0});
							
							if(!$controls){
								return;
							}else{
								slider.addClass("has-controls");
							}
							
							controlsRight.hover(
								function(){
									var controlNav = $(this).find(".flex-control-nav");
									
									if(slider.width() < 240) return;
									
									if(controlNav.length == 0){
										controlNav = $(this).next().find(".flex-control-nav");
										controlNav.appendTo(this).after(controlNav.prev());
									}
									
									prevNav.stop(true , true).css({"right": -controlNav.outerWidth() - prevNav.outerWidth() , "opacity": 0 , "left": "auto"}).animate({"right": 0 , "opacity": 1} , 250);
									controlNav.stop(true , true).css({"right": -controlNav.outerWidth() , "opacity": 0 , "left": "auto"}).animate({"right": 0 , "opacity": 1} , 250);
								}
								,
								function(){
									var controlNav = $(this).find(".flex-control-nav");
									prevNav.stop(true , true).animate({"right": -controlNav.outerWidth() - prevNav.outerWidth() , "opacity": 0} , 300 , "easeInOutCubic");
									controlNav.stop(true , true).css("left" , "auto").animate({"right": -controlNav.outerWidth() , "opacity": 0} , 300 , "easeInOutCubic");
								}
							);
							
							controlsLeft.hover(
								function(){
									var controlNav = $(this).find(".flex-control-nav");
									
									if(slider.width() < 240) return;
									
									if(controlNav.length == 0){
										controlNav = $(this).prev().find(".flex-control-nav");
										controlNav.appendTo(this).after(controlNav.prev());
									}
									
									nextNavLeft.stop(true , true).css({"left": -controlNav.outerWidth() - nextNavLeft.outerWidth() , "opacity": 0 , "right": "auto"}).animate({"left": 0 , "opacity": 1} , 250);
									controlNav.stop(true , true).css({"left": -controlNav.outerWidth() , "opacity": 0 , "right": "auto"}).animate({"left": 0 , "opacity": 1} , 250);
								}
								,
								function(){
									var controlNav = $(this).find(".flex-control-nav");
									nextNavLeft.stop(true , true).animate({"left": -controlNav.outerWidth() - nextNavLeft.outerWidth() , "opacity": 0} , 300 , "easeInOutCubic");
									controlNav.stop(true , true).css("right" , "auto").animate({"left": -controlNav.outerWidth() , "opacity": 0} , 300 , "easeInOutCubic");
								}
							);
							
							if($autohide){
								slider.mouseover(function(){
									clearTimeout(slider.data("to"));
									slider.find(".controls").stop(true , false).animate({"opacity": 1} , 150);
								});
								
								slider.mouseout(function(){
									clearTimeout(slider.data("to"));
									slider.data("to" , setTimeout(function(){
										slider.find(".controls").stop(true , false).animate({"opacity": 0} , 200);
									} , 1000));
								});
								
								slider.mouseout();
							}
						}
					});
				}
			} ,
			
			mediaelement: {
				init: function(target){
					/* get audio with poster */
					target.find("audio.ts-player[data-poster]").each(function(){
						var wrap = $("<div class='mejs-wrap'/>");
						var img = $("<img src='" + $(this).attr("data-poster") + "' alt='" + $(this).attr("data-poster-alt") + "'/>");
						
						$(this).wrap(wrap);
						$(this).before(img);
					});
					
					/* get media */
					target.find("video.ts-player,audio.ts-player").mediaelementplayer({
						startVolume: 1 ,
						videoWidth: "100%" ,
						videoHeight: "100%" ,
						audioWidth: "100%" ,
						audioHeight: 36 ,
						features: ['playpause','current','progress','duration','volume']
					});
				}
			} ,
			
			tabs: {
				init: function(target){
					target.find(".tabs").each(function(){
						var tabs = $(this).children("ul").children("li");
						var content = $(this).children("ol").children("li");
						
						tabs.click(function(){
							if($(this).hasClass("active")) return false;
							
							tabs.removeClass("active");
							$(this).addClass("active");
							content.hide();
							content.eq($(this).index()).fadeIn(500);
							return false;
						});
						
						content.hide();
						content.eq(tabs.filter(".active").eq(0).index()).fadeIn(500);
					});
				}
			} ,
			
			toggle: {
				init: function(target){
					target.find(".toggle").each(function(){
						var toggle = $(this);
						var a = toggle.children("a").eq(0);
						var content = toggle.children("div").eq(0);
						
						toggle.data("open" , function(){
							toggle.addClass("active");
							
							if(content.css("position") == "absolute"){
								content.css({"position": "relative" , "margin-top": -content.outerHeight()});
							}
							
							content.stop(true , false).animate({"margin-top": 0 , "opacity": 1} , 600 , "easeInOutCubic");
						});
						
						toggle.data("close" , function(){
							toggle.removeClass("active");
							content.stop(true , false).animate({"margin-top": -content.outerHeight() , "opacity": 0} , 600 , "easeInOutCubic" , function(){$(this).css({"position": "absolute"});});
						});
						
						if(!toggle.hasClass("active")){
							content.css({"position": "absolute" , "opacity": 0});
						}
						
						a.click(function(){
							if(toggle.parent().hasClass("accordion")){
								toggle.parent().find(".toggle").each(function(){
									$(this).data("close")();
								});
								toggle.data("open")();
							}else{
								if(toggle.hasClass("active")){
									toggle.data("close")();
								}else{
									toggle.data("open")();
								}
							}
							
							return false;
						});
					});
				}
			} ,
			
			twitter: {
				init: function(target){
					target.find(".twitter-feed").each(function(){
						var output = $(this).html().replace(/%7B/g , "{").replace(/%7D/g , "}");
						$(this).html("");
						$(this).social({
							network : "twitter",
							loadingText : "Loading...",
							user : $(this).attr("data-user"),
							count: $(this).attr("data-count") == undefined ? 3 : $(this).attr("data-count") ,
							twitter : {
								output : String(output)
							}
						});
					});
				}
			} ,
			
			flickr: {
				init: function(target){
					target.find(".flickr-feed").each(function(){
						var output = $(this).html().replace(/%7B/g , "{").replace(/%7D/g , "}");
						$(this).html("");
						$(this).social({
							network : "flickr",
							loadingText : "Loading...",
							user : $(this).attr("data-user"),
							count: $(this).attr("data-count") == undefined ? 6 : $(this).attr("data-count") ,
							flickr : {
								output : String(output)
							}
						});
					});
				}
			} ,
			
			contact: {
				init: function(target){
					target.find(".contact-form").each(function(){
						var form = $(this);
		
						form.find("*[type='submit']").click(function(){
							var name = form.find("input[name='name']");
							var email = form.find("input[name='email']");
							var message = form.find("textarea[name='message']");
							var website = form.find("input[name='website']");
							var to = form.find("input[name='to']");
							
							email.removeClass("invalid");
							form.parent().find(".form-failure").hide();
							
							if(name.val() == name.attr("data-prompt") || name.val() == ""){
								name.focus();
								return false;
							}
							
							if(email.val() == email.attr("data-prompt") || email.val() == ""){
								email.focus();
								return false;
							}
							
							if(email.val().indexOf("@",0) == -1 || email.val().indexOf(".",0) == -1){
								email.focus();
								email.addClass("invalid");
								return false;
							}
							
							if(message.val() == message.attr("data-prompt") || message.val() == ""){
								message.focus();
								return false;
							}
							
							var dataString = "name=" + name.val() + "&email=" + email.val() + "&message=" + message.val() + "&to=" + to.val();
							if(website != undefined) dataString += "&website=" + website.val();
							
							$.ajax({
								type: "POST" ,
								url: form.attr("action") ,
								data: dataString ,
								success: function(r){
									if(r == "missing_name"){
										name.focus();
									}else
									if(r == "missing_email"){
										email.focus();
									}else
									if(r == "missing_message"){
										message.focus();
									}else
									if(r == "invalid_email"){
										email.focus();
										email.addClass("invalid");
									}else
									if(r == "success"){
										form.children().fadeOut(150);
										form.find(".form-success").delay(300).fadeIn(300).delay(2000).fadeOut(150);
										form.children().not(".form-success,.form-failure").delay(2600).fadeIn(300);
										
										setTimeout(function(){
											form.children("input,textarea").val("").focusout();
										} , 2600);
									}else
									if(r == "failure"){
										form.children().fadeOut(150);
										form.find(".form-failure").delay(300).fadeIn(300).delay(2000).fadeOut(150);
										form.children().not(".form-success,.form-failure").delay(2600).fadeIn(300);
									}
								} ,
								error: function(r){
									form.children().fadeOut(150);
									form.find(".form-failure").delay(300).fadeIn(300).delay(2000).fadeOut(150);
									form.children().not(".form-success,.form-failure").delay(2600).fadeIn(300);
								}
							});
							
							return false;
						});
					});
				}
			} ,
			
			fullscreenComponents: {
				homeAccordion: {
					init: function(target){
						var homeAccordion = this;
						
						homeAccordion.resize();
						
						template.dock.onAfterStateChange.homeAccordion = function(){
							homeAccordion.resize();
						}
						
						template.dock.onBeforeStateChange.homeAccordion = function(){
							homeAccordion.resize();
						}
						
						target.find("#home-accordion").each(function(){
							var accordion = $(this);
							
							accordion.children().each(function(){
								/* set panel width */
								$(this).width(accordion.width());
								
								/* set panel position */
								$(this).data("p0" , (accordion.width()/accordion.children().size())*$(this).index());
								$(this).data("pbefore" , accordion.children(":lt(" + $(this).index() + ")"));
								$(this).data("pafter" , accordion.children(":gt(" + $(this).index() + ")"));
								
								var img = $(this).find("img");
								
								$(this).find(".fullscreen-caption").hide();
								
								if(Modernizr.csstransitions){
									$(this).css({
										"left": $(this).data("p0")
									});
									
									img.css(
										"transform", "translateX(" + Number(img.attr("data-pos") == undefined ? 0 : Math.min(0 , -Number(img.attr("data-pos"))*img.width() + (accordion.width()/accordion.children().size())*0.5)) + "px)"
									);
								}else{
									$(this).css({
										"left": $(this).data("p0")
									});
									
									img.css(
										"margin-left", img.attr("data-pos") == undefined ? 0 : Math.min(0 , -Number(img.attr("data-pos"))*img.width() + (accordion.width()/accordion.children().size())*0.5)
									);
								}
								
								/* handle mouse actions */
								$(this).mouseenter(function(){
									var img = $(this).find("img");
									var imgw = img.outerWidth();
									var pw = (accordion.width() - imgw)/(accordion.children().size() - 1);
									var i = $(this).index();
									var bs = $(this).data("pbefore").size();
									
									pw = Math.max(50 , pw);
									imgw = accordion.width() - pw*(accordion.children().size() - 1);
									
									if(Modernizr.csstransitions){
										$(this).data("pbefore").each(function(){
											$(this).stop(true , false).css({
												"left": Number($(this).index()*pw)
											});
										});
										
										$(this).data("pafter").each(function(){
											$(this).stop(true , false).css({
												"left": Number(bs*pw + imgw + ($(this).index() - i - 1)*pw)
											});
										});
										
										$(this).stop(true , false).css({
											"left": Number(bs*pw)
										});
										
										img.stop(true , false).css({
											"transform": "translateX(" + 0 + "px)"
										});
									}else{
										$(this).data("pbefore").each(function(){
											$(this).stop(true , false).animate({
												"left": $(this).index()*pw
											} , 600 , "easeOutCubic");
										});
																			
										$(this).data("pafter").each(function(){
											$(this).stop(true , false).animate({
												"left": bs*pw + imgw + ($(this).index() - i - 1)*pw
											} , 600 , "easeOutCubic");
										});
										
										$(this).stop(true , false).animate({
											"left": bs*pw
										} , 600 , "easeOutCubic");
										
										img.stop(true , false).animate({
											"margin-left": 0
										} , 600 , "easeOutCubic");
									}
									
									$(this).siblings().find(".fullscreen-caption").hide();
									
									$(this).children(".fullscreen-caption").show().children().each(function(){
										$(this).stop(true , false).css({
											"display": "block" ,
											"margin-left": -200 ,
											"opacity": 0
										}).delay(200 + $(this).index()*100).animate({
											"margin-left": 0 ,
											"opacity": 1
										} , 600 , "easeInOutCubic");
									});
									
									$(this).children(".fullscreen-caption").find(".slide").each(function(){
										$(this).stop(true , false).css({
											"margin-left": -200 ,
										}).delay(200 + $(this).index()*150).animate({
											"margin-left": 0 ,
										} , 600 , "easeInOutCubic");
									});
								});
								
								$(this).mouseleave(function(){
									var img = $(this).find("img");
									
									$(this).find(".fullscreen-caption").stop(true , true).hide();
									
									if(Modernizr.csstransitions){
										$(this).parent().children().each(function(){
											$(this).stop(true , false).css({
												"left": $(this).data("p0")
											});
										});
										
										img.stop(true , false).css({
											"transform": "translateX(" + Number(img.attr("data-pos") == undefined ? 0 : Math.min(0 , -Number(img.attr("data-pos"))*img.width() + (accordion.width()/accordion.children().size())*0.5)) + "px)"
										});
									}else{
										$(this).parent().children().each(function(){
											$(this).stop(true , false).animate({
												"left": $(this).data("p0")
											} , 600 , "easeOutCubic");
										});
										
										img.stop(true , false).animate({
											"margin-left": img.attr("data-pos") == undefined ? 0 : Math.min(0 , -Number(img.attr("data-pos"))*img.width() + (accordion.width()/accordion.children().size())*0.5)
										} , 600 , "easeOutCubic");
									}
								});
							});
						});
					} ,
					
					resize: function(){
						$("#main-wrap").find("#home-accordion").each(function(){
							var accordion = $(this);
							
							accordion.width($(window).width());
							
							accordion.children().each(function(){
								/* set panel width */
								$(this).width(accordion.width());
								
								/* set panel position */
								$(this).data("p0" , (accordion.width()/accordion.children().size())*$(this).index());
								
								$(this).mouseleave();
								var img = $(this).find("img");
								img.stop(true , true);
							});
						});
					}
				} ,
				
				homeSlider: {
					element: false ,
					autoplayMode: true ,
					index: -1 ,
					slides: false ,
					updating: false ,
					navPrev: $("<a href='#' class='nav-link prev'><span><span></span></span></a>") ,
					navNext: $("<a href='#' class='nav-link next'><span><span></span></span></a>") ,
					controls: false ,
					preloader: $("<div class='home-slider-preloader'></div>") ,
					
					init: function(target){
						var autoplay = this.autoplay;
						
						this.element = target.find("#home-slider");
						
						if(this.element.size() == 0) return;
						
						/* reset */
						template.background.dir = false;
						
						this.index = -1;
						this.controls = $("<div class='controls'></div>");
						this.autoplayMode = this.element.attr("data-autoplay") == "false" ? false : true;
						if(this.element.attr("data-autoplayduration") != undefined) this.autoplay.duration = this.element.attr("data-autoplayduration");
						this.autoplay._duration = this.autoplay.duration;
						
						/* grab the slides */
						this.slides = this.element.find(".slides").children();
						
						/* add nav links */
						this.element.prepend(this.navPrev).prepend(this.navNext);
						
						/* nav next */
						this.navNext.click(function(){
							template.page.components.fullscreenComponents.homeSlider.getNext();
							return false;
						});
						
						this.navNext.css({
							"opacity": 0 ,
							"margin-right": -50
						});
							
						this.navNext.mouseover(function(){
							$(this).stop(true,false).animate({
								"opacity": 1 ,
								"margin-right": 0
							} , 200 , "easeOutCubic");
							
							autoplay.stop();
							autoplay.pause = true;
						});
						
						this.navNext.mouseout(function(){
							$(this).stop(true,false).animate({
								"opacity": 0 ,
								"margin-right": -50
							} , 200 , "easeInCubic");
							
							autoplay.pause = false;
							autoplay.start();
						});
						
						this.navPrev.mouseover(function(){
							$(this).stop(true,false).animate({
								"opacity": 1 ,
								"margin-left": 0
							} , 200 , "easeOutCubic");
							
							autoplay.stop();
							autoplay.pause = true;
						});
						
						this.navPrev.mouseout(function(){
							$(this).stop(true,false).animate({
								"opacity": 0 ,
								"margin-left": -50
							} , 200 , "easeInCubic");
							
							autoplay.pause = false;
							autoplay.start();
						});
						
						/* nav prev */
						this.navPrev.click(function(){
							template.page.components.fullscreenComponents.homeSlider.getPrev();
							return false;
						});
						
						this.navPrev.css({
							"opacity": 0 ,
							"margin-left": -50
						});
						
						/* add control links */
						this.controls.prependTo(this.element).hover(function(){
							autoplay.stop();
							autoplay.pause = true;
						} , function(){
							autoplay.pause = false;
							autoplay.start();
						});
						
						for(var i = 0 ; i < this.slides.size() ; i++){
							var a = $("<a href='#'>" + (i + 1) + "</a>");
							a.data("index" , i);
							this.controls.append(a);
						}
						
						this.controls.find("a").click(function(){
							template.page.components.fullscreenComponents.homeSlider.show($(this).data("index"));
							return false;
						});
						
						/* add preloader */
						this.controls.prepend(this.preloader);
						
						/* show the first slider */
						this.show(0);
					} ,
					
					getNext: function(){
						this.show(this.index + 1);
					} ,
					
					getPrev: function(){
						this.show(this.index - 1);
					} ,
					
					show: function(index){
						if(!this.element || this.element.size() == 0 || this.updating) return;
						
						var slider = this;
						
						var dir = index > this.index ? "right" : "left";
						
						index = this.getCorrectIndex(index);
						if(index == this.index) return;
						
						template.background.dir = dir;
						
						if(this.element.attr("data-fade") == "true") template.background.dir = "fade";
						
						this.updating = true;
						
						/* stop autoplay */
						this.autoplay.stop();
						
						var slide = slider.slides.eq(index);
						
						if(!isNaN(slide.attr("data-duration"))){
							this.autoplay.duration = Number(slide.attr("data-duration"));
						}else{
							this.autoplay.duration = this.autoplay._duration;
						}
						
						/* update background */
						var nextThumbURL = this.slides.eq(this.getCorrectIndex(this.index + 1)).children().eq(0).attr("data-thumb");
						var prevThumbURL = this.slides.eq(this.getCorrectIndex(this.index - 1)).children().eq(0).attr("data-thumb");
						
						template.background.onAnimationFinish = function(){
							template.page.components.fullscreenComponents.homeSlider.updating = false;/* update autoplay */
							template.page.components.fullscreenComponents.homeSlider.autoplay.start();
							template.page.components.fullscreenComponents.homeSlider.captions.update();
						}
						
						template.background.onReady = function(){
							/* load next thumb */
							$("<img/>").load(function(){
								/* load prev thumb */
								$("<img/>").load(function(){
									slider.preloader.stop(true , false).animate({"opacity": 0} , 600 , "easeInOutCubic");
						
									/* remove old captions */
									template.page.components.fullscreenComponents.homeSlider.captions.hide();
									
									var d = 600 + 100*template.page.components.fullscreenComponents.homeSlider.element.find(".slides").children(".active").find(".fullscreen-caption").children().size();
									setTimeout(function(){
										template.page.components.fullscreenComponents.homeSlider.update();

										if(slider.element.attr("data-fade") == "true") template.background.finish();
									} , d);
								}).attr("src", prevThumbURL);
							}).attr("src", nextThumbURL);
						}
						
						slider.preloader.stop(true , false).animate({"opacity": 1} , 600 , "easeInOutCubic");
						
						var bg = this.slides.eq(index).children().eq(0);
						bg = bg.is("img") ? "<img src='" + this.slides.eq(index).children("img").eq(0).attr("data-large") + "' />" : 
											"<iframe src='" + this.slides.eq(index).children("iframe").eq(0).attr("data-large") + "'></iframe>";
						template.background.load(bg);
						
						this.controls.find("a").eq(index).addClass("active").siblings().removeClass("active");
						
						this.index = index;
					} ,
					
					update: function(){
						var slider = template.page.components.fullscreenComponents.homeSlider;
						
						/* update next thumb */
						var nextSpan = this.navNext.children("span").eq(0);
						var nextThumb = $("<img src='" + this.slides.eq(this.getCorrectIndex(this.index + 1)).children("img").eq(0).attr("data-thumb") + "' />");
						template.animator.animate(
							nextSpan ,
							"v" ,
							"easeInOutCubic" ,
							0 ,
							-nextSpan.children("img").eq(0).width() ,
							450 ,
							function(){
								nextSpan.css("margin-right" , nextSpan.data("v"));
							} ,
							function(){
								nextSpan.children("img").remove().end().css("margin-right" , 0);
								
								setTimeout(function(){
									nextSpan.prepend(nextThumb).css("margin-right" , -nextSpan.children("img").eq(0).width());
									template.animator.animate(
										nextSpan ,
										"v" ,
										"easeInOutCubic" ,
										parseInt(nextSpan.css("margin-right")) ,
										0 ,
										450 ,
										function(){
											nextSpan.css("margin-right" , nextSpan.data("v"));
										}
									);
								} , 100);
							}
						);
						
						/* update prev thumb */
						var prevSpan = this.navPrev.children("span").eq(0);
						var prevThumb = $("<img src='" + this.slides.eq(this.getCorrectIndex(this.index - 1)).children("img").eq(0).attr("data-thumb") + "' />");
						template.animator.animate(
							prevSpan ,
							"v" ,
							"easeInOutCubic" ,
							0 ,
							-prevSpan.children("img").eq(0).width() ,
							450 ,
							function(){
								prevSpan.css("margin-left" , prevSpan.data("v"));
							} ,
							function(){
								prevSpan.children("img").remove().end().css("margin-left" , 0);
								
								setTimeout(function(){
									prevSpan.prepend(prevThumb).css("margin-left" , -prevSpan.children("img").eq(0).width());
									template.animator.animate(
										prevSpan ,
										"v" ,
										"easeInOutCubic" ,
										parseInt(prevSpan.css("margin-left")) ,
										0 ,
										450 ,
										function(){
											prevSpan.css("margin-left" , prevSpan.data("v"));
										}
									);
								} , 100);
							}
						);
						
						/* update background */
						if(slider.element.attr("data-fade") != "true") template.background.finish();
						
						/* update current slides */
						var slides = slider.element.find(".slides");
						var li = slides.children().eq(slider.index);
						li.siblings().removeClass("active").end().addClass("active");
					} ,
					
					getCorrectIndex: function(index){
						if(index > this.slides.size() - 1) index = 0;
						if(index < 0) index = this.slides.size() - 1;
						return index;
					} ,
		
					autoplay: {
						to: false ,
						pause: false ,
						duration: 15000 ,
						_duration: 0 ,
						
						start: function(){
							var slider = template.page.components.fullscreenComponents.homeSlider;
							
							if(!slider.autoplayMode || this.pause || this.duration < 0) return;
							
							this.stop();
							
							this.to = setTimeout(function(){
								slider.getNext();
							} , this.duration);
						} ,
						
						stop: function(){
							clearTimeout(this.to);
						}
					} ,
		
					captions: {
						hide: function(){
							var slider = template.page.components.fullscreenComponents.homeSlider;
							var slides = slider.element.find(".slides");
							var li = slides.children();
							var captions = li.find(".fullscreen-caption").children();
													
							captions.each(function(){
								$(this).stop(true , false).delay(200 + $(this).index()*100).animate({
									"margin-left": -200 ,
									"opacity": 0
								} , 600 , "easeInOutCubic");
							});
						} ,
						
						update: function(){
							var slider = template.page.components.fullscreenComponents.homeSlider;
							var slides = slider.element.find(".slides");
							var li = slides.children().eq(slider.index);
							var captions = li.find(".fullscreen-caption").children();
							
							slides.children().fadeIn();
							li.show().siblings().hide();
							
							captions.each(function(){
								$(this).stop(true , false).css({
									"display": "block" ,
									"margin-left": -200 ,
									"opacity": 0
								}).delay(200 + $(this).index()*100).animate({
									"margin-left": 0 ,
									"opacity": 1
								} , 600 , "easeInOutCubic");
							});
							
							captions.find(".slide").each(function(){
								$(this).stop(true , false).css({
									"margin-left": -200 ,
								}).delay(200 + $(this).index()*150).animate({
									"margin-left": 0 ,
								} , 600 , "easeInOutCubic");
							});
						}
					}
				}
			}
		}
	} ,
	
/* ========== dock ========== */
	
	dock: {
		element: $("#dock") ,
		hidden: false , /* specifies whether the dock is hidden or not */
		onStateChange: {} ,
		onAfterStateChange: {} ,
		onBeforeStateChange: {} ,
		
		init: function(){
			/* initialize the menu */
			this.header.menu.init();
		
			/* init mobile menu */
			this.header.mobileMenu.init();
			
			/* init pageTopLink */
			this.pageTopLink.init();
			
			/* init toggleDock */
			this.toggleDock.init();
			
			/* set state */
			this.stateChange();
			
			/* init */
			this.hide();
			this.element.stop(true,true);
			
			if($.cookie){
				if($.cookie("ts-hide-dock") == "1"){
					this.hide();
					this.element.stop(true,true);
				}else{
					this.show();
					this.element.stop(true,true);
				}
			}
		} ,
		
	/* ----- handle state change ----- */
	
		show: function(){
			this.beforeStateChange();
			
			this.hidden = false;
			this.element.addClass("active");
			$("body").addClass("active-dock");
			this.element.stop(true , false).animate({"bottom": 0} , {duration: 600 , easing: "easeInOutCubic" , step: this.stateChange , complete: function(){
				if(template.page.element.hasClass("no-scroll")){
					template.page.element.height($(window).height() - template.dock.header.height - template.dock.footer.height);
				}
				
				template.dock.afterStateChange();
			}});
		} ,
		
		hide: function(){
			if(template.page.element.hasClass("no-scroll")){
				template.page.element.height($(window).height());
			}
			
			this.beforeStateChange();
			
			this.hidden = true;
			this.element.removeClass("active");
			$("body").removeClass("active-dock");
			this.element.stop(true , false).animate({"bottom": -(this.header.height + this.footer.height)} , {duration: 600 , easing: "easeInOutCubic" , step: this.stateChange , complete: this.afterStateChange});
		} , 
		
		stateChange: function(){
			/* update bottom margin of the page while the dock is being shown or hidden */
			template.page.element.css("padding-bottom" , template.dock.element.outerHeight() + parseInt(template.dock.element.css("bottom")) + (template.page.element.hasClass("fullscreen") ? 0 : 35));

			for(var fn in template.dock.onStateChange){
				if($.isFunction(template.dock.onStateChange[fn])) template.dock.onStateChange[fn]();
			}
		} ,
		
		afterStateChange: function(){
			for(var fn in template.dock.onAfterStateChange){
				if($.isFunction(template.dock.onAfterStateChange[fn])) template.dock.onAfterStateChange[fn]();
			}
		} ,
		
		beforeStateChange: function(){
			for(var fn in template.dock.onBeforeStateChange){
				if($.isFunction(template.dock.onBeforeStateChange[fn])) template.dock.onBeforeStateChange[fn]();
			}
		} ,
		
	/* ===== header ===== */
	
		header: {
			element: $("#header") ,
			height: $("#header").outerHeight() ,

		/* ----- logo ----- */
		
			logo: {
				element: $("#site-logo")
			} ,
			
		/* ----- menu ----- */
		
			menu: {
				element: $("#main-nav") ,
				root: $("#main-nav").children("ul") , /* menu root */
				height: 0 , /* height of the menu in active state */
				active: false , /* specifies whether the menu is opened (active) or not */
				hasSubmenus: false , /* specifies whether the menu has submenus or not */
				
				init: function(){
					var menu = this;
					
					menu.root.children("li").each(function(){
						/* check whether the menu has submenus or not */
						if($(this).children("ul").size() > 0){
							menu.hasSubmenus = true;
							
							$(this).children("ul").find("li").each(function(){
								if($(this).children("ul").size() > 0){
									/* add submenu indicator */
									$(this).addClass("has-submenu");
									
									if(template.config.showSubmenuOn == "click"){
										$(this).mouseenter(function(){
											var ul = $(this).children("ul").eq(0);
											clearTimeout(ul.data("to"));
										});
									}
									
									$(this).children("a")[template.config.showSubmenuOn](function(e){
										var ul = $(this).parent().children("ul").eq(0);
										
										if(template.config.showSubmenuOn != "click"){
											clearTimeout(ul.data("to"));
										}
										
										/* close other submenus */
										menu.root.find("li > ul > li ul").not(ul).not(ul.parentsUntil(menu.root)).hide().parent().removeClass("active");
										
										/* toggle submenu */
										if($(this).parent().hasClass("active") && template.config.showSubmenuOn == "click"){
											$(this).parent().removeClass("active");
											ul.fadeOut(200);
											return false;
										}else{
											$(this).parent().addClass("active");
										}
										
										/* fix submenu position in case it goes off the screen */
										ul.show().css("top" , 0);
										var v = template.dock.footer.element.offset().top - (ul.offset().top + ul.outerHeight() + 10);
										if(v < 0){
											ul.css("top" , v);
										}
										
										/* submenu animation */
										if(template.config.showSubmenuOn != "click") ul.css("margin", 0);
										ul.stop(true , true).css({"opacity":0 , "right": "auto" , "left": "75%"}).animate({"opacity": 1 , "left": "100%"} , 300 , "easeOutCubic");
										
										if(ul.offset().left + ul.outerWidth() > $(window).width()){
											ul.stop(true , true).css({"opacity":0 , "right": "75%" , "left": "auto"}).animate({"opacity": 1 , "right": "100%"} , 300 , "easeOutCubic");
										}
										
										return false;
									});
									
									/* hide submenu on mouse leave */
									$(this).mouseleave(function(){
										var li = $(this);
										var ul = $(this).children("ul").eq(0);
										
										clearTimeout(ul.data("to"));
										ul.data("to" , setTimeout(function(){
											li.removeClass("active");
											template.config.showSubmenuOn == "click" ? ul.fadeOut(200) : ul.hide();
										} , 400));
									});
									
									
								}
							});
						}
						
						/* handle mouse click for all child links */
						$(this).find("a").each(function(){
							$(this).click(function(){
								if(template.config.use_page_ajax_loading){
									/* return if this item has a submenu */
									if($(this).parent().hasClass("has-submenu")) return;
									
									var href = $(this).attr("href");
									if(!$(this).hasClass("external")){
										template.page.load(href);
										
										/* remove 'current' class from all menu items */
										template.dock.header.menu.element.find("li").removeClass("current");
										
										/* add 'current' class to the new item and its parents */
										$(this).parents("li").addClass("current");
										
										return false;
									}
								}
							});
						});
					});
					
					/* handle opening the menu */
					menu.element.mouseenter(function(){
						/* if there are no submenus, don't bother opening the menu */
						if(!menu.hasSubmenus) return;
						
						/* or if there are, open it */
						menu.open();
					});
					
					/* handle closing the menu */
					template.dock.element.mouseleave(function(){
						/* close the menu upon mouse leave */
						menu.close();
					});
				} ,
				
				open: function(){
					var menu = this;
					
					/* if the menu is already open, do nothing */
					if(menu.active) return;
					
					menu.root.children("li").each(function(){
						/* set the original width and height for each root item, we'll need to close the menu properly */
						$(this).data("tmpw" , $(this).width());
						$(this).css("width" , "auto");
						$(this).data("w" , $(this).width()).width($(this).data("tmpw"));
						$(this).data("h" , $(this).height()).height($(this).data("h"));
					});
					
					/* make sure the initial animation is finished */
					template.dock.header.element.css({opacity: 1 , bottom: 0});
					
					/* get width of the widest submenu */
					var submenuWidth = Math.max.apply(Math , menu.root.children("li").map(function(){ return $(this).data("w"); }).get())*1.5;
					
					/* make sure the menu doesn't expand beyond the screen width */
					if(submenuWidth*menu.root.children("li").size() > template.dock.header.element.width() - 40){
						submenuWidth = (template.dock.header.element.width() - 40)/menu.root.children("li").size();
					}
					
					/* set the menu height (in active state) equal to the highest menu item */
					menu.height = 0;
					menu.root.children("li").stop(true , false).children("ul").css({"width": submenuWidth , "margin-left": -submenuWidth*0.5});
					menu.root.children("li").each(function(){
						menu.height = Math.max(menu.height , $(this).children("a").height() + $(this).children("ul").height() + 40);
					});
					
					/* animate the menu */
					menu.root.children("li").stop(true , false).animate({width: submenuWidth , height: this.height} , 400 , "easeInOutQuad");
					template.dock.header.element.stop(true , false).delay(50).animate({height: this.height} , {duration: 600 , easing: "easeInOutCubic"});
					
					/* hide the logo if it overlaps with the menu */
					if($(window).width() - (submenuWidth + 1)*this.root.children("li").size() < template.dock.header.logo.element.outerWidth() + 50){
						template.dock.header.logo.element.fadeOut(300);
					}
					
					this.active = true;
					template.dock.header.element.addClass("active");
				} ,
				
				close: function(){
					var menu = this;
				
					menu.root.children("li").each(function(i){
						$(this).stop(true , false).animate({width: $(this).data("w") , height: $(this).data("h")} , {duration: 600 , easing: "easeInOutCubic" , complete: function(){
							$(this).css("width" , "auto");
					
							menu.active = false;
							template.dock.header.element.removeClass("active");
						}});
					});
					template.dock.header.element.stop(true , false).animate({height: template.dock.header.height} , {duration: 400 , easing: "easeOutCubic"});
					
					/* show the logo if it's hidden */
					template.dock.header.logo.element.fadeIn(600);
				}
			} ,
			
		/* ----- mobile menu ----- */
		
			mobileMenu: {
				header: $("<div id='mobile-header'/>") ,
				nav: $("<nav id='mobile-nav'/>") ,
				logo: false ,
				select: $("<select/>") ,
				
				init: function(){
					this.logo = template.dock.header.logo.element.clone().attr("id" , "mobile-site-logo");
					this.header.append(this.logo);
					this.header.append(this.nav);
					this.nav.append(this.select);
					$("#main-wrap").prepend(this.header);
					
					/* handle item select */
					this.select.change(function(){
						window.location = this.value;
					});
					
					/* populate the menu */
					this.createChildren(template.dock.header.menu.root , 0);
				} ,
				
				createChildren: function(parent , level){
					var mobileMenu = this;
					
					$(parent).children("li").each(function(i , obj){
						var label = "";
						for(var k = 0 ; k < level ; k++){
							label += "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
						}
						label += $(obj).children("a").text();
						mobileMenu.select.append("<option " + ($(obj).hasClass("current") ? "selected = 'selected' " : "") + "value = '" + $(obj).children("a").attr("href") + "'>" + label + "</option>");
						
						if($(obj).children("ul").size() == 1){
							mobileMenu.createChildren($(obj).children("ul") , level + 1);
						}
					});
				}
			} ,
		} ,
	
	/* ===== footer ===== */
	
		footer: {
			element: $("#footer") ,
			height: $("#footer").outerHeight()
		} ,

	/* ===== toggleDock ===== */
	
		toggleDock: {
			element: $("#toggle-dock") ,
			
			init: function(){
				this.element.hover(
					function(){
						template.dock.toggleDock.mouseover();
					} ,
					function(){
						template.dock.toggleDock.mouseout();
					}
				);
				
				this.element.click(this.click);
			} ,
			
			mouseover: function(){
				this.element.stop(true , false).animate({"left": 0} , 400 , "easeOutCubic");
				this.element.children("span").children("span").stop(true , false).animate({"opacity": 1 , "left": 0} , 400 , "easeOutCubic");
			} ,
			
			mouseout: function(easeOut){
				this.element.stop(true , false).animate({"left": - this.element.outerWidth() + 102} , easeOut == true ? 300 : 600 , easeOut == true ? "easeOutCubic" : "easeInOutCubic");
				this.element.children("span").children("span").stop(true , false).animate({"opacity": 0 , "left": -this.element.children("span").children("span").width() + 30} , easeOut == true ? 300 : 600 , easeOut == true ? "easeOutCubic" : "easeInOutCubic");
			} ,
			
			click: function(){
				if(template.dock.hidden){
					template.dock.toggleDock.element.removeClass("active");
					template.dock.show();
					if($.cookie) $.cookie("ts-hide-dock" , "0", {path: '/'});
				}else{
					template.dock.toggleDock.element.addClass("active");
					template.dock.hide();
					template.dock.header.menu.close();
					if($.cookie) $.cookie("ts-hide-dock" , "1", {path: '/'});
				}
				
				template.dock.toggleDock.mouseout(true);
				
				return false;
			}
		} ,
	
	/* ----- pageTopLink ----- */
		pageTopLink: {
			element: $("#page-top-link") ,
			
			init: function(){
				this.element.hover(
					function(){
						template.dock.pageTopLink.mouseover();
					} ,
					function(){
						template.dock.pageTopLink.mouseout();
					}
				);
				
				this.element.click(this.click);
			} ,
			
			mouseover: function(){
				this.element.stop(true , false).animate({"right": 0 , "opacity": 1} , 400 , "easeOutCubic");
				this.element.children("span").children("span").stop(true , false).animate({"opacity": 1 , "right": 0} , 400 , "easeOutCubic");
			} ,
			
			mouseout: function(){
				this.element.stop(true , false).animate({"right": - this.element.outerWidth() + 102 , "opacity": 1} , 600 , "easeInOutCubic");
				this.element.children("span").children("span").stop(true , false).animate({"opacity": 0 , "right": -this.element.children("span").children("span").width() + 30} , 600 , "easeInOutCubic");
			} ,
			
			click: function(){
				$(window.opera ? "html" : "html, body").stop(true , false).animate({scrollTop: 0} , 1000 , "easeInOutCubic");
				return false;
			} ,
			
			updateOpacity: function(){
				if($(window).scrollTop() == 0){
					this.element.hide();
				}else
				if($(window).scrollTop() > 200){
					this.element.fadeIn(300);
				}else{
					this.element.fadeOut(300);
				}
			}
		} ,
	} ,
	
/* ========== background ========== */
	
	background: {
		element: $("#background") ,
		dir: "right" ,
		content: false ,
		width: false , /* original width of the background */
		height: false , /* original height of the background */
		onReady: false , /* if set, a function to be excuted when the background is loaded */
		onAnimationFinish: false , /* if set, a function to be excuted when the animation is finished */
		firstLoad: true ,
		loading: false ,
		
		reset: function(){
			this.dir = "right";
			this.content = false;
			this.width = false;
			this.height = false;
			this.firstLoad = true;
			this.loading = false;
		} ,
		
		init: function(){
			this.load(this.element.children().eq(0).clone().get(0));
		} ,
		
		load: function(data){
			if(!data) return;
			
			this.content = $(data);
			this.loading = true;
			
			/* get the actual background dimensions */
			if(this.content.is("img")){
				if(this.firstLoad){
					this.empty();
				}
				
				$("<img/>").load(function(){
					template.background.width = this.width;
					template.background.height = this.height;
					template.background.complete();
				}).attr("src", this.content.attr("src"));
			}else{
				this.complete();
			}
		} ,
		
		complete: function(){
			if(this.onReady != false){
				/* do custom actions before finishing, you MUST call the 'finish' method manually */
				this.onReady();
				this.onReady = false;
			}else{
				/* update the background */
				this.finish();
			}
		} ,
		
		finish: function(){
			/* mark existing backgrounds as old */
			this.element.children().addClass("old");
			
			/* add the new background */
			this.element.append(this.content);
			
			/* resize all backgrounds */
			this.resize();
			
			/* animate backgrounds */
			if(this.firstLoad || this.dir == "fade"){
				if(this.onAnimationFinish != false){
					this.onAnimationFinish();
					this.onAnimationFinish = false;
				}
				
				this.content.css({"opacity": 0});
				
				if(Modernizr.csstransitions){
					setTimeout(function(){
						template.background.content.css({
							opacity: 1 ,
							WebkitTransition : 'opacity 0.5s ease-in-out',
							MozTransition    : 'opacity 0.5s ease-in-out',
							MsTransition     : 'opacity 0.5s ease-in-out',
							OTransition      : 'opacity 0.5s ease-in-out',
							transition       : 'opacity 0.5s ease-in-out'
						});
					} , 1000);
				}else{
					this.content.animate({"opacity": 1} , 600 , "easeInOutCubic");
				}
			}else{
				this.animate();
			}
			
			this.firstLoad = false;
			this.loading = false;
		} ,
		
		animate: function(from){
			var background = this;
				
			/* set the initial and final positions of the new background */
			var a;
			var b;
			var prop;
			
			from = from || this.dir;
			
			switch(from){
				case "right" :
				a = $(window).width() - parseInt(background.content.css("left"));
				b = 0;
				prop = "margin-left";
				break;
				
				case "left" :
				a = -$(window).width() + parseInt(background.content.css("left"));
				b = 0;
				prop = "margin-left";
				break;
				
				case "top" :
				a = -$(window).height() + parseInt(background.content.css("top"));
				b = 0;
				prop = "margin-top";
				break;
				
				case "bottom" :
				a = $(window).height() - parseInt(background.content.css("top"));
				b = 0;
				prop = "margin-top";
				break;
			}
			
			/* start the animation */
			if(Modernizr.csstransitions){
				var props = {};
				
				/* executes the step function */
				props["-webkit-transform"] = "translateX(" + a + "px)";
				props["-moz-transform"] = "translateX(" + a + "px)";
				props["-ms-transform"] = "translateX(" + a + "px)";
				props["-o-transform"] = "translateX(" + a + "px)";
				props["transform"] = "translateX(" + a + "px)";
				background.content.css(props);
				
				props["-webkit-transform"] = "translateX(0px)";
				props["-moz-transform"] = "translateX(0px)";
				props["-ms-transform"] = "translateX(0px)";
				props["-o-transform"] = "translateX(0px)";
				props["transform"] = "translateX(0px)";
				background.element.children(".old").css(props);
				
				setTimeout(function(){
					props = {
						WebkitTransition : '-webkit-transform 1000ms cubic-bezier(0.475, 0.015, 0.505, 1.000)',
						MozTransition    : '-moz-transform 1000ms cubic-bezier(0.475, 0.015, 0.505, 1.000)',
						MsTransition     : '-ms-transform 1000ms cubic-bezier(0.475, 0.015, 0.505, 1.000)',
						OTransition      : '-o-transform 1000ms cubic-bezier(0.475, 0.015, 0.505, 1.000)',
						transition       : 'transform 1000ms cubic-bezier(0.475, 0.015, 0.505, 1.000)'
					};
					props["-webkit-transform"] = "translateX(" + b + "px)";
					props["-moz-transform"] = "translateX(" + b + "px)";
					props["-ms-transform"] = "translateX(" + b + "px)";
					props["-o-transform"] = "translateX(" + b + "px)";
					props["transform"] = "translateX(" + b + "px)";
					
					background.content.bind("transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd" , function(){
						background.clear();
						
						if(background.onAnimationFinish != false){
							background.onAnimationFinish();
							background.onAnimationFinish = false;
						}
					});
					background.content.css(props);
					
					props = {
						WebkitTransition : '-webkit-transform 1000ms cubic-bezier(0.475, 0.015, 0.505, 1.000)',
						MozTransition    : '-moz-transform 1000ms cubic-bezier(0.475, 0.015, 0.505, 1.000)',
						MsTransition     : '-ms-transform 1000ms cubic-bezier(0.475, 0.015, 0.505, 1.000)',
						OTransition      : '-o-transform 1000ms cubic-bezier(0.475, 0.015, 0.505, 1.000)',
						transition       : 'transform 1000ms cubic-bezier(0.475, 0.015, 0.505, 1.000)'
					};
					props["-webkit-transform"] = "translateX(" + (b - a) + "px)";
					props["-moz-transform"] = "translateX(" + (b - a) + "px)";
					props["-ms-transform"] = "translateX(" + (b - a) + "px)";
					props["-o-transform"] = "translateX(" + (b - a) + "px)";
					props["transform"] = "translateX(" + (b - a) + "px)";
					
					background.element.children(".old").css(props);
				} , 100);
			}else{
				/* executes the step function */
				background.content.css(prop , a);
				background.element.children(".old").css(prop , 0);
			
				var props = {};
				props[prop] = b;
				
				background.content.animate(props , {
						duration: 1000 , 
						easing: "easeInOutCubic" , 
						step: function(){
							background.element.children(".old").css(prop , parseInt(background.content.css(prop)) - a);
						} , 
						complete: function(){
							background.clear();
							
							if(background.onAnimationFinish != false){
								background.onAnimationFinish();
								background.onAnimationFinish = false;
							}
						}
					}
				);
			}
		} ,
		
		resize: function(){
			if(!this.content) return;
			
			if(this.content.is("img")){
				/* fit to window width */
				this.content.width($(window).width());
				this.content.height(this.content.width()*this.height/this.width);
				
				/* if there are blank areas, fit to window height instead */
				if(this.content.height() < $(window).height()){
					this.content.height($(window).height());
					this.content.width(this.content.height()*this.width/this.height);
				}
			
				/* center the background */
				this.content.css("left" , 0.5*($(window).width() - this.content.width()));
				this.content.css("top" , 0.5*($(window).height() - this.content.height()));
			}
			
			if(this.content.is("iframe")){
				/* fit to window */
				this.content.width($(window).width());
				this.content.height($(window).height());
				this.content.css("left" , 0);
				this.content.css("top" , 0);
			}
		} ,
		
		empty: function(){
			this.element.empty();
		} ,
		
		clear: function(){
			this.element.children(".old").remove();
		}
	} ,
	
/* ========== preloader ========== */
	
	preloader: {
		element: $("#preloader") ,
		onStart: false ,
		onFinish: false ,
		
		init: function(){
			this.element.hide().children("span").css("opacity" , 0);
			
			template.dock.onStateChange.preloader = function(){
				template.preloader.resize();
			}
		} ,
		
		show: function(){
			this.element.show().children("span").stop(true , false).css({
				"opacity": 0 ,
				"width": 80*1.5 ,
				"height": 80*1.5 ,
				"margin-top": -80*0.5*1.5 ,
				"margin-left": -80*0.5*1.5
			}).animate({
				"opacity": 1 ,
				"width": 80 ,
				"height": 80 ,
				"margin-top": -80*0.5 ,
				"margin-left": -80*0.5
			} , 400 , "easeInOutCubic" , function(){
				if($.isFunction(template.preloader.onStart)){
					template.preloader.onStart();
					template.preloader.onStart = false;
				}
			});
		} ,
		
		hide: function(){
			this.element.children("span").stop(true , false).animate({
				"opacity": 0 ,
				"width": 20 ,
				"height": 20 ,
				"margin-top": -10 ,
				"margin-left": -10
			} , 400 , "easeInBack" , function(){
				template.preloader.element.hide();
				
				if($.isFunction(template.preloader.onFinish)){
					template.preloader.onFinish();
					template.preloader.onFinish = false;
				}
			});
		} ,
		
		resize: function(){
			/*
			this.element.width($(window).width());
			this.element.height($(window).height() - template.dock.header.height - template.dock.footer.height - parseInt(template.dock.element.css("bottom")));
			*/
		}
	} ,
	
/* ========== animator ========== */

	animator: {
		animations: 0 ,
		renderList: {} ,
		
		init: function(){
			window.requestAnimFrame = (function(){
				return  window.requestAnimationFrame       || 
						window.webkitRequestAnimationFrame || 
						window.mozRequestAnimationFrame    || 
						window.oRequestAnimationFrame      || 
						window.msRequestAnimationFrame     || 
						function( callback ){
						window.setTimeout(callback, 1000 / 60);
						};
			})();
			
			/* start rendering animations */
			this.render();
		} ,
		
		animate: function(target , prop , easing , from , to , duration , step , finish){
			var fn = "_anim" + this.animations;
			
			/* set initial values */
			easing = easing == undefined ? "easeInOutCubic" : easing;
			from = from == undefined ? 0 : from;
			to = to == undefined ? 1 : to;
			duration = duration == undefined ? 1000 : duration;
			
			target.data("time" , 0);
			target.data(prop , from);
			
			/* animate target.data(prop) */
			this.renderList[fn] = function(){
				var time = target.data("time");
				
				if(time < duration){
					/* animate */
					target.data(prop , $.easing[easing](null , time , from , to - from , duration));
					
					if($.isFunction(step)){
						step();
					}
					
					time += 1000/60;
				}else{
					/* stop the animation */
					delete template.animator.renderList[fn];
					
					/* on finish */
					if($.isFunction(finish)){
						finish();
					}
				}
				
				target.data("time" , time);
			}
			
			this.animations++;
		} ,
		
		render: function(){
			requestAnimFrame(template.animator.render);
			
			for(var fn in template.animator.renderList){
				if($.isFunction(template.animator.renderList[fn])) template.animator.renderList[fn]();
			}
		}
	} ,

/* ========== template functions ========== */
	
	imagesReady: function(target , callback){
		clearInterval($(target).data("tout"));
		$(target).data("tout" , setInterval(function(){
			var ready = true;
			
			$(target).find("img").each(function(){
				if((this.naturalWidth == 0 || this.naturalHeight == 0) && $(this).data("404") == undefined){
					ready = false;
				}
			});
			
			if(ready){
				clearInterval($(target).data("tout"));
				callback();
			}
		} , 100));
	}
};

/* ------------------------------------------------------------------------------- */
/* DOCUMENT READY
/* ------------------------------------------------------------------------------- */

template.preloader.element.addClass("landing");
$("#main-wrap").children().not(template.preloader.element).css("visibility" , "hidden");
$("body").css("visibility" , "visible");

$(window).bind("load", function() {
    template.preloader.onFinish = function(){
		template.init();
		
		template.dock.element.css({
			"opacity": 0 ,
			"margin-bottom": -template.dock.element.outerHeight()
		}).animate({
			"opacity": 1 ,
			"margin-bottom": 0
		} , 600 , "easeInOutCubic");
		
		template.page.element.css({
			"opacity": 0
		}).delay(600).animate({
			"opacity": 1
		} , 600 , "easeInOutCubic");
		
		$("#preloader").removeClass("landing");
		$("#main-wrap").children().css("visibility" , "visible");
	};
	
	template.preloader.hide();
});