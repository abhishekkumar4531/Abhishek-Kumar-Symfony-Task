/**
 * When user clicked on started button it will focused to the post-form's textarea.
 */
$(document).on('click','#getStarted',function(e){
  $("#newPost").focus();
});

/**
 * When home page will be load then this function will be execute.
 * It will fetch the 10 post from the database and display on home page.
 */
$( document ).ready(function() {
  $.ajax({
    type: "GET",
    url: "/home/load",
    dataType: "html",
    success: function(data){
      $("#post-display").html(data);
    }
  });
});

/**
 * When user clicked on laod more button then this block will be execute.
 * On every click this block will fetch max 10 data from database and display on
 * home page.
 */
$(document).on('click', '#loadMore', function(e){
  $.ajax({
    type: "GET",
    url: "/home/loadmore",
    dataType: "html",
    success: function(data){
      $("#post-display").html(data);
    }
  });
});
