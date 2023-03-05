$('#likeBtn').css('display', 'none');
var likeCount = $('#count').val();

$('#likeBtn').click(() => {
  $('#dislikeBtn').css('display', 'block');
  $('#likeBtn').css('display', 'none');
  likeCount--;
  $('#count').text(likeCount);
});

$('#dislikeBtn').click(() => {
  $('#dislikeBtn').css('display', 'none');
  $('#likeBtn').css('display', 'block');
  likeCount++;
  $('#count').text(likeCount);
});

$('#comment').click(() => {
  $('.commentSection').css('display', 'block');
  $('#comment').css('display', 'none');
  $('#commentHide').css('display', 'block');
  showComment();
});

$('#commentHide').click(() => {
  $('.commentSection').css('display', 'none');
  $('#comment').css('display', 'block');
  $('#commentHide').css('display', 'none');

});

$('#menu').click(() => {
  $('.subMenuForPost').css('display', 'block');
  $('#closeMenu').css('display', 'block');
  $('#menu').css('display', 'none');
});

$('#closeMenu').click(() => {
  $('.subMenuForPost').css('display', 'none');
  $('#closeMenu').css('display', 'none');
  $('#menu').css('display', 'block');
});

var flag = true;

$('.profile').click(() => {
  if (flag) {
    $('.subMenuProfile').css('display', 'block');
    flag = false;
  } else {
    $('.subMenuProfile').css('display', 'none');
    flag = true;
  }
})

/**
 *  This functions fetch comments for a particular post. 
 */
function showComment() {
  $.ajax({
    url: '/fetchComments/{postID}',
    type: 'POST',
    beforeSend: function () {
      showRightLoader();
    },
    success: function (response) {
      hideRightLoader();
    },
    error: function (jqXHR, textStatus, errorThrown) {
      hideRightLoader();
    },
    complete: function () {
      hideRightLoader();
    }
  });
}

/**
 * Post comments to the server
 */
$('#commentForm').submit(function (event) {
  event.preventDefault();
  var formData = $(this).serialize();
  $.ajax({
    url: '/postComment',
    type: 'POST',
    data: formData,
    beforeSend: function () {
      showRightLoader();
    },
    success: function (response) {
      hideRightLoader();
    },
    error: function (jqXHR, textStatus, errorThrown) {
      hideRightLoader();
    },
    complete: function () {
      hideRightLoader();
    }
  });
});

$('#deleteBtn').click(function (event) {
  event.preventDefault();
  $.ajax({
    url: '/logout',
    data: { 'flag': true },
    beforeSend: function () {
      showRightLoader();
      showLeftLoader();
    },
    success: function (response) {
      if (response.msg) {
        window.location.href = '/login';
        hideRightLoader();
        hideLeftLoader();
      }
    },
    complete: function () {
      hideRightLoader();
      hideLeftLoader();
    }
  });
});

$('.post-content').hide();
setTimeout(
  function () {
    $('.active-users-skeleton').hide();
    $('.post-skeleton').hide();

    $('.post-content').show();
    $('.active-users-content').show();
  }, 2000);

/**
 * Get the User and send it with socket
 */
$.ajax({
  url: '/active-users',
  success: function (response) {

    email = response.email;
    userImage = response.userImage;

    $('.profile').attr('src', 'http://' + location.hostname +'/userImage/' + userImage);

    if (email) {
      activeSocket(email);
    }
  }
});

/**
 * This function activate socket connections. And on message it checks what
 * Kind of messages it has received and based on that it updates the frontend.
 * 
 * @param email 
 */
function activeSocket(email) {

  const socket = new WebSocket('ws://' + location.hostname +':8080');

  socket.onopen = () => {
    setInterval(function () {
      socket.send(JSON.stringify({ "email": email }));
    }, 1000);
  };

  socket.onmessage = (event) => {
    const message = JSON.parse(event.data);

    // A socket can listen multiple requests at once, so it's important to
    // filter out the incoming messages.
    switch (message.type) {

      case 'active-users':
        message.data.users.forEach(user => {
          if (isActive(user)) {
            addActiveUsers(user);  
          }else{
           removeInactiveUsers(user);
          }
        });
        break;
      case 'posts':
        // TODO:
        // For future work.
    }
  };
}

function isActive(element) {

  const now = new Date();
  const userDate = new Date(element.lastActiveTime.date);
  const diff = now - userDate;

  // If difference between current time and user last active time is less than
  // 2 seconds, then returns true.
  if (diff < 2000) {
    return true;
  }
  return false;
}

function addActiveUsers(element) {

  // Fetch unordered list element from front-end.
  const activeUsersList = document.querySelector('.active-users-content');

  // If there is a already active user card present, in the list. Then Don't 
  // Duplicate the user.
  const activeUser = document.getElementById(element.userId);
  if(!activeUser){

    // Creating a li element and setting class name and dynamic id.
    const divItem = document.createElement('li');
    divItem.className = 'list-group-item ';
    divItem.id = element.userId;

    // Insert the card details into the li.
    divItem.innerHTML = `
    <div class="shadow cardUser">
      <div class="imgActiveUsers">
        <img src="http://${location.hostname}/userImage/${element.img}" width="30px" height="30px">
      </div>
      <div class="nameActiveUsers">
        <h4>${element.fullName}</h4>
      </div>
    </div>
  `;
  // It's important to append child each time to get multiple li inside ul.
  activeUsersList.appendChild(divItem);
  }
}

function removeInactiveUsers(element) {
  const activeUser = document.getElementById(element.userId);

  // If the user is already in the list, remove it.
  if (activeUser) {
      activeUser.parentNode.removeChild(activeUser);
  }   
}
