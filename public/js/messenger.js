$(document).ready(function() {
    var notifSound = new Audio('/js/messenger/notif.ogg');
    var notif = 0;
    var friendListLoaded = false;
    var friendLoadTimeout = 1;
    var messagesLoaded = false;
    var skipUpdate = false;

    // init
    function init() {
        console.log('messenger.js loaded');
        getFriendList();
    } init();


    // Show messenger
    $('#nav-msg-toggler').click(function() {
        $('#nav-msg-toggler').toggleClass('active');
        if(notif == 1) $('#nav-msg-toggler').html('chat');
        $('.messenger').toggleClass('hidden');
        if(!friendListLoaded) {
            getFriendList();
        }
    });

    // Get friend list
    function getFriendList() {
        $.ajax({
            url: '/messenger/friends',
            type: 'GET'
        }).done(function(data) {
            console.log(data);
            if(data.lenght < 1) return;
            notif = 0;
            
            // Render friend list
            let friendList = $('.messenger-userlist');
            friendList.empty();
            data.forEach(function(friend) {
                uNotif = 0;
                if (friend.last_message > friend.last_checked){
                    notif=1;
                    uNotif = 1;
                }

                friendTemplate = `
                    <div class="user ${uNotif ? 'notif':''}" data-id="${friend.id}" data-name="${friend.username}">
                        <img src="${friend.pfp}" alt="${friend.username}">
                        <div class="user-notif"></div>
                    </div>
                `;
                friendList.append(friendTemplate);
            });

            if (notif == 1){
                if(!$('#nav-msg-toggler').hasClass('active')) {
                    $('#nav-msg-toggler').html('mark_chat_unread');
                }
                if(friendListLoaded) {
                    notifSound.play();
                }
            }
            friendListLoaded = true;
        }).fail(function(err) {
            console.log(err);
        });
    }

    // Reload friend list each 10 seconds if messenger is open, otherwise once a minute
    setInterval(function() {
        if($('#nav-msg-toggler').hasClass('active') || friendLoadTimeout%6 == 0) {
            getFriendList();
        }
        friendLoadTimeout++;
    }, 10000);

    // Preview friend's name on hover, and set preview's position to be next to the user
    $('.messenger-userlist').on('mouseenter', '.user', function() {
        let preview = $('.messenger .friend-name-preview');
        let userName = $(this).attr('data-name');
        $('.fnp-name').html(userName);
        preview.css('top', $(this).offset().top - preview.height() - $(this).height() - 3);
        preview.removeClass('hidden');
    }).on('mouseleave', '.user', function() {
        let preview = $('.messenger .friend-name-preview');
        let fnp = $('.fnp-name');
        preview.addClass('hidden');
        // wait 200ms before removing the preview
        let pHtml = fnp.html();
        setTimeout(function() {
            if(fnp.html() == pHtml) {
                fnp.html('');
            }
        }, 200);
    });

    // Render messages
    function renderMessages(messages) {
        let msgContainer = $('.messenger-body-messages');
        messages.forEach(function(msg) {
            msgC = msg.me ? 'me' : 'them';
            msgTemplate = `
                <div class="message ${msgC}" data-date="${msg.date}">
                    <span style="display:none">${moment.locale('fr')}</span>
                    <div class="message-time">${moment(msg.date).fromNow()}</div>
                    <div class="message-text">${msg.content}</div>
                </div>
            `;
            msgContainer.prepend(msgTemplate);
        });
    }

    // Get messages
    function getMessages(friendId) {
        $.ajax({
            url: '/messenger/messages',
            type: 'POST',
            data: {
                fid: friendId
            }
        }).done(function(data) {
            console.log(data);
            if(data.length < 1 || data["error"]){
                messagesLoaded = true;
                return;
            }

            // Render messages
            $.when(renderMessages(data)).done(function() {
                messagesLoaded = true;
                scrollToBottom();
            });
        });
    }

    // Click on friend
    $('.messenger-userlist').on('click', '.user', function() {
        let friendId = $(this).attr('data-id');
        let friendName = $(this).attr('data-name');
        
        // Render friend name
        $('.messenger-header-title').html(friendName);

        // Get messages
        $('.messenger-body-messages').empty();
        $('.messenger-chat').attr('uid', friendId);
        getMessages(friendId);

        // Hide notif badge if present
        if($(this).hasClass('notif'))
            $(this).removeClass('notif');

        // Show messages
        $('.messenger-chat').removeClass('disabled');

        // Focus on input
        $('.messenger-msg-input').focus();
    });

    // Scroll to bottom
    function scrollToBottom() {
        $('.messenger-body-messages').scrollTop($('.messenger-body-messages')[0].scrollHeight);
    }

    // Check for new messages every 3 seconds (and render, if any)
    function checkForNewMessages() {
        let friendId = $('.messenger-chat').attr('uid');
        if(!friendId || friendId==0 || !messagesLoaded) return;
        
        lastMsg = $('.messenger-body-messages').children().first().attr('data-date');
        if(!lastMsg) lastMsg = "0000-00-00 00:00:00";

        $.ajax({
            url: '/messenger/cfu',
            type: 'POST',
            data: {
                fid: friendId,
                last: lastMsg
            }
        }).done(function(data) {
            console.log(data);
            if(data.length < 1 || data["error"] || data['info']) return;

            // Render messages
            $.when(renderMessages(data)).done(function() {
                scrollToBottom();
            });
        });
    } setInterval(function() {
        if (!skipUpdate) checkForNewMessages();
        else skipUpdate = false;
    }, 3000);

    // Send message
    function sendMessage(friendId, msg) {
        if(!friendId || friendId==0) return;
        if(!msg || msg.length<1) return;
        msg = msg.replace(/\s+/g,' ').trim();
        if(msg.length<1 || msg==' ') return;
        $.ajax({
            url: '/messenger/send',
            type: 'POST',
            data: {
                fid: friendId,
                msg: msg
            }
        }).done(function(data) {
            console.log(data);
            if(data["error"]) return;
            $('.messenger-msg-input').val('');
            checkForNewMessages();
            skipUpdate = true;
        });
    }
    // Send message on enter
    $('.messenger-msg-input').keypress(function(e) {
        if(e.which == 13) {
            let friendId = $('.messenger-chat').attr('uid');
            let msg = $(this).val();
            sendMessage(friendId, msg);
        }
    });
    // Send message on btn click
    $('.messenger-msg-btn').click(function() {
        let friendId = $('.messenger-chat').attr('uid');
        let msg = $('.messenger-msg-input').val();
        sendMessage(friendId, msg);
    });

    // Update message dates every 15 seconds
    function updateMessageDates() {
        let friendId = $('.messenger-chat').attr('uid');
        if(!friendId || friendId==0 || !messagesLoaded) return;

        let messages = $('.messenger-body-messages').children();
        messages.each(function() {
            let date = $(this).attr('data-date');
            $(this).find('.message-time').html(moment(date).fromNow());
        });
    } setInterval(function() {
        updateMessageDates();
    }, 15000);

    // Close messenger's chat
    $('.messenger-header-close').click(function() {
        $('.messenger-chat').addClass('disabled');
        $('.messenger-chat').attr('uid', 0);
        messagesLoaded = false;
    });


});
