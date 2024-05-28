$('img[data-enlargable]').addClass('img-enlargable').click(function () {
    var src = $(this).attr('src');
    var fullscreenDiv = $('<div>').css({
    background:'RGBA(0,0,0,.5)',
      width: '100%',
      height: '100%',
      position: 'fixed',
      zIndex: '10000',
      top: '0',
      left: '0',
      cursor: 'zoom-out',
      display: 'flex',
      alignItems: 'flex-start',
      justifyContent: 'center', 
      paddingBottom: '50px',
      paddingTop: '50px' 
    }).addClass('fullscreen-wrapper');

    var imageDiv = $('<div>').css({
      background: ' url(' + src + ') no-repeat center',
      backgroundSize: 'contain',
      width: '100%',
      height: '100%',
      borderRadius:'0px'
    });

    fullscreenDiv.click(function () {
      $(this).remove();
      $('body').removeClass('fullscreen-enabled');
      imageDiv.remove()
    });

    fullscreenDiv.append(imageDiv);
    fullscreenDiv.appendTo('body');

    $('body').addClass('fullscreen-enabled');
  });