$(document).ready(function() {
  
  $("#output").hide();

  $("#mdInput").submit(function(event) {
    event.preventDefault();
    var toSend = {'f': 'parsedown', 'input': $("#mdText").val()};
    console.log(toSend);
    $.get('api.php', toSend).done(function(reply) {
      $("#outputWindow").html("");
      $.each(reply, function(key, val) {
        if(key == 'result'){
          $("#outputWindow").html(val);  
        }
      });
    }).fail(function(reply) {
      console.error(reply);
    });
  });
  
  $(".toggle").click(function() {
    $(".nyan").toggle();
  });

});