$(document).on('click','#getStarted',function(e){
  $("#newPost").focus();
});

$( document ).ready(function() {
  $.ajax({
    type: "GET",
    url: "/afterLogin/loadInitialContent",
    dataType: "html",
    success: function(data){
      $("#post-display").html(data);
    }
  });
});

$(document).on('click','#loadMore',function(e){
  $.ajax({
    type: "GET",
    url: "/afterLogin/loadMoreContent",
    dataType: "html",
    success: function(data){
      $("#post-display").html(data);
    }
  });
});
