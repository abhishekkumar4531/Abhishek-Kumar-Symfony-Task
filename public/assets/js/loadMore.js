$(document).on('click','#getStarted',function(e){
  $("#newPost").focus();
});

$( document ).ready(function() {
  alert("Start");
  $.ajax({
    type: "GET",
    url: "/home/load",
    dataType: "html",
    success: function(data){
      $("#post-display").html(data);
    }
  });
});

$(document).on('click','#loadMore',function(e){
  $.ajax({
    type: "GET",
    url: "/home/load",
    dataType: "html",
    success: function(data){
      $("#post-display").html(data);
    }
  });
});
