$(function() {
  // Get handle to the chat div
  var $chatWindow = $('#messages');

  // Our interface to the Chat service
  var chatClient;

  // A handle to the "general" chat channel - the one and only channel we
  // will have in this sample app
  var generalChannel;

  // The server will assign the client a random username - store that value
  // here
  var username;

  // Helper function to print info messages to the chat window
  function print(infoMessage, asHtml) {
    var $msg = $('<div class="info">');
    if (asHtml) {
      $msg.html(infoMessage);
    } else {
      $msg.text(infoMessage);
    }
    $chatWindow.append($msg);
  }

  // Helper function to print chat message to the chat window
  function printMessage(fromUser, message) {
    if(fromUser == 'system'){
      fromUser = "TSIC";
    }else{
      fromUser = "ME";
    }
    var $user = $('<span class="username">').text(fromUser + ':');
    if (fromUser === "ME") {
      $user.addClass('me');
    }
    var $message = $('<span class="message">').text(message);
    var $container = $('<div class="message-container">');
    $container.append($user).append($message);
    $chatWindow.append($container);
    $chatWindow.scrollTop($chatWindow[0].scrollHeight);
  }

  function makeid(length) {
    var result           = '';
    var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var charactersLength = characters.length;
    for ( var i = 0; i < length; i++ ) {
      result += characters.charAt(Math.floor(Math.random() * 
 charactersLength));
   }
   return result;
}


  // Alert the user they have been assigned a random username
  print('Initializationing ...');
  print('Wait ...');
  $('#chat-input').attr('readonly', true);
  // Get an access token for the current user, passing a username (identity)
  $.getJSON('/token', function(data) {


    // Initialize the Chat client
    Twilio.Chat.Client.create(data.token).then(client => {
      console.log('Created chat client');
      chatClient = client;
      chatClient.getSubscribedChannels().then(createOrJoinGeneralChannel);

      // when the access token is about to expire, refresh it
      chatClient.on('tokenAboutToExpire', function() {
        refreshToken(username);
      });

      // if the access token already expired, refresh it
      chatClient.on('tokenExpired', function() {
        refreshToken(username);
      });

    // Alert the user they have been assigned a random username
    username = data.identity;
    /*print('You have been assigned a random username of: '
    + '<span class="me">' + username + '</span>', true);*/

    }).catch(error => {
      console.error(error);
      print('There was an error creating the chat client:<br/>' + error, true);
      print('Please check your .env file.', false);
    });
  });

  function refreshToken(identity) {
    console.log('Token about to expire');
    // Make a secure request to your backend to retrieve a refreshed access token.
    // Use an authentication mechanism to prevent token exposure to 3rd parties.
    $.getJSON('/token/' + identity, function(data) {
      console.log('updated token for chat client');          
      chatClient.updateToken(data.token);
    });
  }

  function createOrJoinGeneralChannel() {
    // Get the general chat channel, which is where all the messages are
    // sent in this simple application
    //print('Attempting to join "general" chat channel...');
    var channel_ID = location.search.split('id=')[1];
    chatClient.getChannelByUniqueName(channel_ID)
    .then(function(channel) { console.log(channel);
      generalChannel = channel;
      console.log('Found general channel:');
      console.log(generalChannel);
      setupChannel();
    }).catch(function() {
      // If it doesn't exist, let's create it
      console.log('Creating general channel');
      chatClient.createChannel({
        uniqueName: channel_ID,
        friendlyName: 'General Chat Channel'
      }).then(function(channel) {
        console.log('Created general channel:');
        console.log(channel);
        generalChannel = channel;
        setupChannel();
      }).catch(function(channel) {
        console.log('Channel could not be created:');
        //console.log(channel);
      });
    });
    
  }

  // Set up channel after it has been found
  function setupChannel() {

    // Join the general channel
    generalChannel.join().then(function(channel) {
      /*print('Joined channel as '
      + '<span class="me">' + username + '</span>.', true);*/
      print("Please write your questions");
      $('#chat-input').attr('readonly', false);
    });

    // Listen for new messages sent to the channel
    generalChannel.on('messageAdded', function(message) { console.log("message.body");
      printMessage(message.author, message.body);
    });

    chatClient.services('IS087237a057ab427f8b075e754915b7d3')
           .channels(generalChannel.sid)
           .webhooks
           .create({
              'configuration.filters': ['onMessageSent'],
              'configuration.method': 'POST',
              'configuration.url': 'https://channels.autopilot.twilio.com/v1/AC5117a2f1b9ccea5224289cc659f37787/UA045269259fa6af5b5f0f8c1d47ef6beb/twilio-chat',
              type: 'webhook'
            })
           .then(webhook => console.log(webhook.sid));




  }

  // Send a new message to the general channel
  var $input = $('#chat-input');
  $input.on('keydown', function(e) {

    if (e.keyCode == 13) {
      if (generalChannel === undefined) {
        print('The Chat Service is not configured. Please check your .env file.', false);
        return;
      }
      generalChannel.sendMessage($input.val())
      $input.val('');
    }
  });
});
