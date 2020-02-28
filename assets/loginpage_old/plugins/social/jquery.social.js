// 
//  jQuery Social
//  A jQuery plugin for asynchronous requests of social network data.
//  Supported streams of data:
//  - Twitter
//  - Flickr
//  - Last.fm
//  - Delicious
//  - Youtube
// 
//  Future updates will include:
//	----------------
//  - Filtering out replies from Twitter
//  - Vimeo
//  
//  @author: Justin Jones (justin@jstnjns.com) http://www.jstnjns.com/
//  @version: 1.0
//  @copyright: 2010 Justin Jones. All rights reserved.
//  @requires: jQuery v1.4.x
// 

(function($){
	$.fn.social = function(options, callback) {
		
		var defaults = {
				network			: 'twitter',
				user			: 'groundctrl',
				count			: 5,
				
				wrapItemIn		: '<li class="item" />',
				wrapListIn		: '<ul class="listing" />',
				loadingText		: '<div class="loading">Loading..</div>',
			
				twitter			: {
					output			: '<span class="tweet"><span class="user"><a href="{user_url}">{user}</a></span> <span class="text">{text}</span> <span class="time"><a href="{tweet_url}">Posted {time}</a></span>',
					search			: null,
					type			: 'recent',
					hash			: null,
					mention			: null,
					list			: null
				},
				flickr			: {
					output			: '<span class="image"><a href="{url}"><img src="{image}"</a></span>',
					size			: 's'
				},
				lastfm			: {
					output			: '<a class="track" href="{url}"><span class="image"><img src="{image}" /></span> <span class="name">{track}</span> <span class="album">{album}</span> <span class="artist">{artist}</span></a>',
					defaultImage	: ''
				},
				delicious		: {
					output			: '<span class="bookmark"><a href="{url}">{name}</a></span>'
				},
				youtube			: {
					output			: '<span class="video"><a href="{url}" title="{name} - {views} views"><img src="{thumb}" /></a></span>',
					embed			: false,
					embedOutput		: '<span class="video">{video}</span>',
					playlist		: false,
					video			: {
						width			: 400,
						height			: 300,
						player			: '/engine/swf/jw/player.swf',
						skin			: '/engine/swf/jw/skins/modieus/video_player.xml',
						express_install	: '/engine/swf/expressinstall.swf'
					}
				}
			};
		
		var settings = $.extend(true, defaults, options);

		function format(text) {
			text = text.replace(/(\b(https?|ftp):\/\/[A-Z0-9+&@#\/%?=~_|!:,.;]*[A-Z0-9+&@#\/%=~_|])/gim, '<a href="$1" target="_blank">$1</a>');
			text = text.replace(/(^|[^\/])(www\.[\S]+(\b|$))/gim, '$1<a href="http://$2" target="_blank">$2</a>');
			text = text.replace(/(\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,6})/gim, '<a href="mailto:$1">$1</a>');
			text = text.replace(/(^|\s)@(\w+)/g, '<span class="mention"><a href="http://www.twitter.com/$2" target="_blank">$1@$2</a></span>');
			text = text.replace(/(^|\s)#(\w+)/g, '<span class="hash">$1#<a href="http://search.twitter.com/search?q=%23$2" target="_blank">$2</a></span>');
			return text;
		}
		
		function ago(time) {
			var parsed_date = Date.parse(time);
			var relative_to = (arguments.length > 1) ? arguments[1] : new Date();
			var delta = parseInt((relative_to.getTime() - parsed_date) / 1000, 10);
			if(delta < 60) {
				return 'less than a minute ago';
			} else if(delta < 120) {
				return 'about a minute ago';
			} else if(delta < (45*60)) {
				return (parseInt(delta / 60, 10)).toString() + ' minutes ago';
			} else if(delta < (90*60)) {
				return 'about an hour ago';
			} else if(delta < (24*60*60)) {
				return 'about ' + (parseInt(delta / 3600, 10)).toString() + ' hours ago';
			} else if(delta < (48*60*60)) {
				return '1 day ago';
			} else {
				return (parseInt(delta / 86400, 10)).toString() + ' days ago';
			}
		}

		return $(this).each(function() {
			// ----------------------------------------------------------------|| Setting Vars ||
			var $this = $(this);
			
			$this.html(settings.loadingText);
			
			// ----------------------------------------------------------------|| TWITTER ||
			if(settings.network == 'twitter') {
								
				var user = '',
					mention = '',
					hash = '',
					
					requestURL = '';
				
				// Format shit for search query
				/*
					 Clean this shit up (User, Mention, Hash)
				*/
				// User
				if($.isArray(settings.user) && settings.user.length > 0) {
					user = 'from:' + settings.user.join(' OR from:');
				} else if(settings.user.length > 0) {
					user = 'from:' + settings.user;
				}
				
				// Mention
				if(settings.twitter.mention) {
					if($.isArray(settings.twitter.mention)) {
						mention = ' OR @' + settings.twitter.mention.join(' OR @');
					} else {
						mention = ' OR @' + settings.twitter.mention;
					}
				}			
				
				// Hash
				if(settings.twitter.hash) {
					if($.isArray(settings.twitter.hash)) {
						hash = ' OR #' + settings.twitter.hash.join(' OR #');
					} else {
						hash = ' OR #' + settings.twitter.hash;
					}
				}
				
				if($.isArray(settings.user) || settings.mention || settings.hash) {
					requestURL = 'http://search.twitter.com/search.json?q=' + escape(user) + escape(mention) + escape(hash) + '&rpp=' + settings.count + '&callback=?';
				} else {
					requestURL = 'http://search.twitter.com/search.json?q=' + escape(user) + '&count=' + settings.count;
				}
				
				$.ajax({
					type		: 'GET',
					dataType	: 'jsonp',
					url			: requestURL,
					beforeSend	: function(xhr) {
						xhr.setRequestHeader('User-Agent','jquery.social.js:justin@jstnjns.com');
						xhr.setRequestHeader('Referer',document.referrer);
					},
					success		: function(data) {
						// Prepare space in which  is replacing
						$this.empty();
						
						var list = data.results.slice(0, settings.count);
						
						if(list.length < 1) {
							$this.html('<span class="error">Sorry, could not load tweets.</span>');
						} else {
							
							for(var item = 0; item < list.length; item++) {

								var output		= settings.twitter.output;

								var user		= (list[item].from_user) ? list[item].from_user : list[item].user.screen_name;

								var user_url	= 'http://www.twitter.com/' + user;
								var tweet_url	= 'http://www.twitter.com/' + user + '/status/' + list[item].id_str; /* changed 'id' to 'id_str' to retrieve the tweet url correctly */				

								var text		= format(list[item].text);
								var time		= ago(list[item].created_at);
								var source		= list[item].source.replace(/&lt;/g,'<').replace(/&gt;/g,'>');
								var avatar		= list[item].profile_image_url;

								var replace		= ['{user_url}','{tweet_url}','{text}','{time}','{user}','{source}','{avatar}'];
								var by			= [user_url,tweet_url,text,time,user,source, avatar];

								for(var i = 0; i < replace.length; i++) {
									output = output.replace(replace[i],by[i]);
								}

								if(settings.wrapItemIn !== '') {
									$(settings.wrapItemIn).append(output).appendTo($this);
								} else {
									$(output).appendTo($this);
								}
							}
							
							$this.wrapInner(settings.wrapListIn);
						}
						
						if($.isFunction(callback)) {
							callback.call(this,list);
						}
					}
				});
				
			}
			
			// ----------------------------------------------------------------|| FLICKR ||
			if(settings.network == 'flickr') {
				$.ajax({
					type	: 'GET',
					dataType: 'json',
					url		: 'http://api.flickr.com/services/feeds/photos_public.gne?id='+ settings.user +'&format=json&limit=5&jsoncallback=?',
					success	: function(data) {
						
						$this.empty();
						
						var list = data.items.slice(0, settings.count);
						
						for(var item = 0; item < list.length; item++) {
							
							var output		= settings.flickr.output;
						
							var url			= list[item].link;
						
							var size		= (settings.flickr.size !== '' && settings.flickr.size !== 'n') ? '_' + settings.flickr.size : '';
							var image		= list[item].media.m.replace('_m', size);
						
							var replace		= ['{url}','{image}'];
							var by			= [url,image];
						
							for(var i = 0; i < replace.length; i++) {
								output = output.replace(replace[i],by[i]);
							}
						
							if(settings.wrapItemIn) {
								$(settings.wrapItemIn).append(output).appendTo($this);
							} else {
								$(output).appendTo($this);
							}
						}
						
						if(settings.wrapListIn) {
							$this.wrapInner(settings.wrapListIn);
						}
						
						if($.isFunction(callback)) { callback.call(); }
					}
				});
			}
			// ----------------------------------------------------------------|| LAST.FM ||
			if(settings.network == 'lastfm') {
				$.ajax({
					type	: 'GET',
					dataType: 'json',
					url		: 'http://ws.audioscrobbler.com/2.0/?method=user.getrecenttracks&user=' + settings.user + '&api_key=2d39f166ead04801f3b9376b1e8ba17d&limit=' + settings.count + '&format=json&callback=?',
					success	: function(data) {
						$this.empty();
						
						var list = data.recenttracks.track.slice(0, settings.count);
						
						for(var item = 0; item < list.length; item++) {
							
							var output		= settings.lastfm.output;
						
							var url			= list[item].url;
							var image		= list[item].image[3]['#text'];
							var track		= list[item].name;
							var album		= list[item].album['#text'];
							var artist		= list[item].artist['#text'];
						
							var replace		= ['{url}','{image}','{track}','{album}','{artist}'];
							var by			= [url,image,track,album,artist];
						
							for(var i = 0; i < replace.length; i++) {
								output = output.replace(replace[i],by[i]);
							}
						
							$(settings.wrapItemIn).append(output).appendTo($this);
						}
						
						$this.wrapInner(settings.wrapListIn);
						
						if($.isFunction(callback)) { callback.call(); }
					}
				});
			}

			// ----------------------------------------------------------------|| Delicious ||
			if(settings.network == 'delicious') {
				$.ajax({
					type	: 'GET',
					dataType: 'jsonp',
					url		: 'http://feeds.delicious.com/v2/json/' + settings.user + '?count=' + settings.count,
					success	: function(data) {
						$this.empty();
						
						var list = data.slice(0, settings.count);
						
						for(var item = 0; item < list.length; item++) {
							
							var output		= settings.delicious.output;
						
							var linkStart	= '<a href="' + list[item].u + '" target="_blank">';
							var linkEnd		= '</a>';
							var name		= list[item].d;
						
							var replace		= ['{linkStart}','{linkEnd}','{name}'];
							var by			= [linkStart,linkEnd,name];
						
							for(var i = 0; i < replace.length; i++) {
								output = output.replace(replace[i],by[i]);
							}
						
							$(settings.wrapItemIn).append(output).appendTo($this);
						}
						
						$this.wrapInner(settings.wrapListIn);
						
						if($.isFunction(callback)) { callback.call(); }
					}
				});
			}
			
			// ----------------------------------------------------------------|| Youtube ||
			if(settings.network == 'youtube') {
				$.ajax({
					type	: 'GET',
					dataType: 'json',
					url		: 'http://gdata.youtube.com/feeds/users/' + settings.user + '/uploads?alt=json&max-results=' + settings.count,
					success	: function(data) {
						
						$this.empty();
						
						var list = data.feed.entry.slice(0, settings.count);
						
						var vars		= {};
						var params		= {};
						var attributes	= {};
						
						for(var item = 0; item < list.length; item++) {
							
							var output		= (settings.youtube.embed) ? settings.youtube.embedOutput : settings.youtube.output;
						
							var name		= list[item].title.$t;
							var url			= list[item].link[0].href;
							var thumb		= list[item].media$group.media$thumbnail[3].url;
							var views		= list[item].yt$statistics.viewCount;
							var video		= '<div id="youtube_player-' + item + '"></div>';
						
							if(typeof(swfobject) !== 'undefined') {
								vars[item]						= {};
								params[item]					= {};
								attributes[item]				= {};
							
								vars[item].file					= list[item].link[0].href;
								vars[item].skin					= settings.youtube.video.skin;
								vars[item].controlbar			= 'over';
								vars[item].stretching			= 'fill';
								vars[item].abouttext			= settings.youtube.video.about_text;
								vars[item].aboutlink			= settings.youtube.video.about_link;
								vars[item].fullscreen			= true;
								vars[item].autostart			= false;
							
								params[item].allowfullscreen	= true;
								params[item].allowscriptaccess	= 'always';
								params[item].allownetworking	= 'all';
								params[item].wmode				= 'transparent';
							} else {
								video		= 'You need \'swfobject\' to run this script';
							}
						
							var replace		= ['{name}','{url}','{thumb}','{views}','{video}'];
							var by			= [name,url,thumb,views,video];
						
							for(var i = 0; i < replace.length; i++) {
								output = output.replace(replace[i],by[i]);
							}
						
							$(settings.wrapItemIn).append(output).appendTo($this);

						}
						
						$this.wrapInner(settings.wrapListIn);
						
						for(item=0; item<list.length; item++) {
							swfobject.embedSWF('/engine/swf/jw/player.swf', 'youtube_player-' + item,settings.youtube.video.width,settings.youtube.video.height,'9.0.0','/engine/swf/expressinstall.swf',vars[item],params[item],attributes[item]);
						}
							
						if($.isFunction(callback)) { callback.call(); }
					}
				});
			}
			
		});
		
	};
})(jQuery);