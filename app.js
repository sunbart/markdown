$(document).ready(function() {
  
  $("#output").hide();

  $("#mdInput").submit(function(event) {
    event.preventDefault();
    var toSend = {'f': 'parsedown', 'input': $("#mdText").val()};
    console.log(toSend);
    $.get('api.php', toSend, function(reply) {
      $("#outputWindow").html(reply.result);
      
    }, "json").fail(function(reply) {
      console.error(reply);
    });
  });
  
  $(".toggle").click(function() {
    $(".nyan").toggle();
  });

});