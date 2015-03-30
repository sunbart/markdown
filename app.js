$(document).ready(function() {
  
  $("#output").hide();

  $("#mdInput").submit(function(event) {
    event.preventDefault();
    var toSend = {'f': 'parsedown', 'input': $("#mdText").val()};
    console.log(toSend);
    $.get('api.php', toSend, function(reply) {
      //alert(typeof reply.result);
      $("#outputWindow").html(reply.result);
      console.log(reply);
      
      
    }, "json").fail(function(reply) {
      //alert(typeof reply.result);
      console.error(reply);
    });
  });
  
  $(".toggle").click(function() {
    $(".nyan").toggle();
  });

});