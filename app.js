$(document).ready(function() {
  
  $("#output").hide();

  $("#mdInput").submit(function(event) {
    event.preventDefault();
    var input = encodeURIComponent($("#mdText").val());
    input = input.replace(/-/g,"%2D");
    input = input.replace(/_/g,"%5F"); 
    input = input.replace(/\*/g,"%2A"); 
    input = input.replace(/~/g,"%7E");   
    var toSend = 'f=parsedown&input=' + input;
    console.log(toSend);
    $.getJSON('api.php', toSend, function(reply) {
      $("#outputWindow").empty();
      $.each(reply, function(key, val) {
        if(key == 'result'){
          $("#outputWindow").html(val);  
        }
      });
    }).fail(function(reply) {
      console.error("error");
      $("#outputWindow").empty();
      $.each(reply, function(key, val) {
        if(key == 'result'){
          $("#outputWindow").html(val);  
        }
      });
    });
  });
  
  $(".toggle").click(function() {
    $(".nyan").toggle();
  });

});