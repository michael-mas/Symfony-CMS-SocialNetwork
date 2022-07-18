var mapCharNames = $('#chars .panel-body > div');
var mapRaidNames = $('#widget_form_content .panel-body > div');

mapCharNames
// Assign Drag & Drop Source
.on('dragstart', function (event) {
  $('.post-content-input').addClass('text-success');
  event.originalEvent.dataTransfer.setData("text", event.target.id);
}).
each(function () {$(this).attr('draggable', 'true');}); 
$('.post-content-input')
.on('dragover', function (event) {
  event.preventDefault();
}).
on('drop', function (event) {
  event.preventDefault();
  var element = $('#' + event.originalEvent.dataTransfer.getData("text"));
  event.target.value = element.html();
})
mapRaidNames.
each(function () {
  var tok = $(this).data('token');
  $(this).attr('data-token', $(this).html()).html(tok).addClass('has-token-name');
});

//bloquer l'entré du clique du input
$(document).ready(function() {
  $('#widget_form_content').on('mousedown', function(e) {
      e.preventDefault();
  });
});

//action lors du drop
$('#widget_form_content').on('drop' , function (event) {
    $(".fa1").addClass("widget-added");
    $(".fas1").addClass("change-icon");
    $("#nmb-widget").text("Ajouté");
    alert('Composant ajouté dans le formulaire !');
});

$('#widget_form_content2').on('drop' , function (event) {
    $(".fa2").addClass("widget-added");
    $(".fas2").addClass("change-icon");
    $("#nmb-widget2").text("Ajouté");
    alert('Composant ajouté dans le formulaire !');
});

