/**
Core script to handle the entire theme and core functions
**/
var QuickSidebar = function () {
    var host = window.location.hostname;
    var protocol = window.location.protocol=='https:' ? 'wss:' : 'ws:';
    window.app = {};
    var userid = $('.userid').val();
    var username = $('.username').text();
    var avatar = $('.useravatar').val();
    var root = $('.root').val();
    avatar = avatar=="" ? "test.png" : avatar;
    var wrapper = $('.page-quick-sidebar-wrapper');
    var wrapperChat = wrapper.find('.page-quick-sidebar-chat');
    var chatContainer = wrapperChat.find(".page-quick-sidebar-chat-user-messages");
    var input = wrapperChat.find('.page-quick-sidebar-chat-user-form .form-control');
    var preparePost = function(dir, time, name, avatar, message) {
        var tpl = '';
        tpl += '<div class="post '+ dir +'">';
        tpl += '<img class="avatar" alt="" src="' + ProfilPath + avatar +'"/>';
        tpl += '<div class="message">';
        tpl += '<span class="arrow"></span>';
        tpl += '<a href="#" class="name">'+name+'</a>&nbsp;';
        tpl += '<span class="datetime">' + time + '</span>';
        tpl += '<span class="body">';
        tpl += message;
        tpl += '</span>';
        tpl += '</div>';
        tpl += '</div>';

        return tpl;
    };
    var getLastPostPos = function() {
        var height = 0;
        chatContainer.find(".post").each(function() {
            height = height + $(this).outerHeight();
        });

        return height;
    };

    /*app.BrainSocket = new BrainSocket(
        new WebSocket(protocol+'//'+host+':8081'),
        new BrainSocketPubSub()
    );

    app.BrainSocket.Event.listen('app.success',function(data){
        console.log('An app success message was sent from the ws server!');
        console.log(data);
    });

    app.BrainSocket.Event.listen('app.error',function(data){
        console.log('An app error message was sent from the ws server!');
        console.log(data);
    });

    app.BrainSocket.Event.listen('generic.event',function(msg){
        console.log(msg);
        var message="";
        var time = new Date();
        var iletiid=$('.ileti').val();
        if(msg.client.data.ileti_id == iletiid ){
            if(msg.client.data.user_id == userid)
                message = preparePost('in',(time.getHours() + ':' + time.getMinutes()), msg.client.data.user_name, msg.client.data.avatar,msg.client.data.message);
            else if(msg.client.data.alici_id == userid)
                message = preparePost('out',(time.getHours() + ':' + time.getMinutes()), msg.client.data.user_name, msg.client.data.avatar,msg.client.data.message);
            else
                message = "";
            if(message!=""){
                message = $(message);
                $('#chat-log').append(message);
            }
            chatContainer.slimScroll({
                scrollTo: getLastPostPos()
            });
        }
    }); */

    var ProfilPath = root+'/assets/images/profilresim/';
    // Handles quick sidebar toggler
    var handleQuickSidebarToggler = function () {
        // quick sidebar toggler
        $('.page-header .quick-sidebar-toggler, .page-quick-sidebar-toggler').click(function (e) {
            $('body').toggleClass('page-quick-sidebar-open'); 
        });
    };

    // Handles quick sidebar chats
    var handleQuickSidebarChat = function () {
        var wrapper = $('.page-quick-sidebar-wrapper');
        var wrapperChat = wrapper.find('.page-quick-sidebar-chat');

        var initChatSlimScroll = function () {
            var chatUsers = wrapper.find('.page-quick-sidebar-chat-users');
            var chatUsersHeight;
            var chatContainer = wrapperChat.find(".page-quick-sidebar-chat-user-messages");

            chatUsersHeight = wrapper.height() - wrapper.find('.nav-justified > .nav-tabs').outerHeight();

            var preparePost = function(dir, time, name, avatar, message) {
                var tpl = '';
                tpl += '<div class="post '+ dir +'">';
                tpl += '<img class="avatar" alt="" src="' + ProfilPath + avatar +'"/>';
                tpl += '<div class="message">';
                tpl += '<span class="arrow"></span>';
                tpl += '<a href="#" class="name">'+name+'</a>&nbsp;';
                tpl += '<span class="datetime">' + time + '</span>';
                tpl += '<span class="body">';
                tpl += message;
                tpl += '</span>';
                tpl += '</div>';
                tpl += '</div>';

                return tpl;
            };

            var getLastPostPos = function() {
                var height = 0;
                chatContainer.find(".post").each(function() {
                    height = height + $(this).outerHeight();
                });

                return height;
            };

            $('.media').on('click',function(){
                $('.ileti').val('');
                var userid = $('.userid').val();
                var className = $(this).attr('class').match(/(\d+)/);
                chatContainer.empty();
                if(className[0]!=="") {
                    var aliciid=className[0];
                    $('#alici').val(aliciid);
                    var imgsrc=$(this).find('img').attr('src');
                    $('#aliciimg').attr('src',imgsrc);
                    var aliciadi=$(this).find('.media-heading').html();
                    $('#aliciadi').html(aliciadi);
                    $.getJSON(root+"/backend/mesajlar/" + userid + "/" + aliciid, function (event) {
                        var mesajlar = event.mesajlar;
                        var ileti = event.ileti;
                        var iletidurum = event.iletidurum;
                        if(iletidurum==1){
                            var eskimesajlar='<div class="post" style="text-align:center"><a type="button default" href="./mesaj/detay/'+mesajlar[0].ileti_id+'">Tümünü Göster</a></div>';
                            chatContainer.append(eskimesajlar);
                        }
                        $('.ileti').val(ileti);
                        if(mesajlar!=""){
                            $.each(mesajlar,function(index){
                                var kullanici = mesajlar[index].kullanici;
                                var message="";
                                if(kullanici.id==userid)
                                    message = preparePost('in',mesajlar[index].time, mesajlar[index].kullanici.adi_soyadi, mesajlar[index].kullanici.avatar, mesajlar[index].icerik);
                                else
                                    message = preparePost('out',mesajlar[index].time, mesajlar[index].kullanici.adi_soyadi, mesajlar[index].kullanici.avatar, mesajlar[index].icerik);
                                 message = $(message);
                                chatContainer.append(message);
                                chatContainer.slimScroll({
                                    scrollTo: getLastPostPos()
                                });
                            });
                        }
                    });
                }
            });

            // chat user list 
            Metronic.destroySlimScroll(chatUsers);
            chatUsers.attr("data-height", chatUsersHeight);
            Metronic.initSlimScroll(chatUsers);

            var chatMessages = wrapperChat.find('.page-quick-sidebar-chat-user-messages');
            var chatMessagesHeight = chatUsersHeight - wrapperChat.find('.page-quick-sidebar-chat-user-form').outerHeight() - wrapperChat.find('.page-quick-sidebar-nav').outerHeight();

            // user chat messages 
            Metronic.destroySlimScroll(chatMessages);
            chatMessages.attr("data-height", chatMessagesHeight);
            Metronic.initSlimScroll(chatMessages);

        };

        initChatSlimScroll();
        Metronic.addResizeHandler(initChatSlimScroll); // reinitialize on window resize

        wrapper.find('.page-quick-sidebar-chat-users .media-list > .media').click(function () {
            wrapperChat.addClass("page-quick-sidebar-content-item-shown");
        });

        wrapper.find('.page-quick-sidebar-chat-user .page-quick-sidebar-back-to-list').click(function () {
            wrapperChat.removeClass("page-quick-sidebar-content-item-shown");
        });

        var handleChatMessagePost = function (e) {

            var iletiid = $('.ileti').val();
            var aliciid = $('#alici').val();
            e.preventDefault();

            var chatContainer = wrapperChat.find(".page-quick-sidebar-chat-user-messages");
            var input = wrapperChat.find('.page-quick-sidebar-chat-user-form .form-control');

            var text = input.val();
            if (text.length === 0) {
                return;
            }
            app.BrainSocket.message('generic.event',
                {
                    'message':text,
                    'user_name':username,
                    'user_id':userid,
                    'avatar':avatar,
                    'alici_id':aliciid,
                    'ileti_id':iletiid
                }
            );
            input.val('');
            $.getJSON(root+"/backend/sendmesaj",{userid:userid,iletiid:iletiid,aliciid:aliciid,text:text}, function (event) {
                var durum = event.durum;
                if(durum!=1){
                    toastr['error'](event.error, 'Mesaj Gönderimi Sırasında Hata!');
                }
            });


        };

        wrapperChat.find('.page-quick-sidebar-chat-user-form .btn').click(handleChatMessagePost);
        wrapperChat.find('.page-quick-sidebar-chat-user-form .form-control').keypress(function (e) {
            if (e.which == 13) {
                handleChatMessagePost(e);
                return false;
            }
        });
    };

    // Handles quick sidebar tasks
    var handleQuickSidebarAlerts = function () {
        var wrapper = $('.page-quick-sidebar-wrapper');
        var wrapperAlerts = wrapper.find('.page-quick-sidebar-alerts');

        var initAlertsSlimScroll = function () {
            var alertList = wrapper.find('.page-quick-sidebar-alerts-list');
            var alertListHeight;

            alertListHeight = wrapper.height() - wrapper.find('.nav-justified > .nav-tabs').outerHeight();

            // alerts list 
            Metronic.destroySlimScroll(alertList);
            alertList.attr("data-height", alertListHeight);
            Metronic.initSlimScroll(alertList);
        };

        initAlertsSlimScroll();
        Metronic.addResizeHandler(initAlertsSlimScroll); // reinitialize on window resize
    };

    // Handles quick sidebar settings
    var handleQuickSidebarSettings = function () {
        var wrapper = $('.page-quick-sidebar-wrapper');
        var wrapperAlerts = wrapper.find('.page-quick-sidebar-settings');

        var initSettingsSlimScroll = function () {
            var settingsList = wrapper.find('.page-quick-sidebar-settings-list');
            var settingsListHeight;

            settingsListHeight = wrapper.height() - wrapper.find('.nav-justified > .nav-tabs').outerHeight();

            // alerts list 
            Metronic.destroySlimScroll(settingsList);
            settingsList.attr("data-height", settingsListHeight);
            Metronic.initSlimScroll(settingsList);
        };

        initSettingsSlimScroll();
        Metronic.addResizeHandler(initSettingsSlimScroll); // reinitialize on window resize
    };

    return {

        init: function () {
            //layout handlers
            handleQuickSidebarToggler(); // handles quick sidebar's toggler
            handleQuickSidebarChat(); // handles quick sidebar's chats
            handleQuickSidebarAlerts(); // handles quick sidebar's alerts
            handleQuickSidebarSettings(); // handles quick sidebar's setting
        }
    };

}();