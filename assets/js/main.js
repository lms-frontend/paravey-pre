console.log('main.js');

  function clickeffect(){
    $('.btn-ans').on('mousedown', function() {
      $(this).addClass('mover');
    }).on('mouseup mouseleave', function() {
      $(this).removeClass('mover');
    });
  }
