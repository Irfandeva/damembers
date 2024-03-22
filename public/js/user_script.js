jQuery(document).ready(function ($) {
  let lastOpenedPopup = null;
  $('.bio').click(function () {
    const bio = $(this).find("input").val();
    // let bio = "<p>hello <strong>theter</strong></p>"
    console.log("==============================");
    console.log(bio);
    console.log("==============================");
    const popupId = $(this).attr('data-popup')

    const popuSpan = `popup_${popupId}`;
    let popup = `<div class='popup-container'>
                  <span class='cancel' onClick='cancelPopup(${popuSpan})'>&#10005</span>
                  <div class="popup">
                  <span class='title'>Bio</span>
                  </div>
                  <div class='popup'>
                   <span class='member'>${bio}</span>
                  </div>
                </div>`
    //check if we a popup open already, close it
    if (lastOpenedPopup !== null)
      $(`#${lastOpenedPopup}`).html('');

    $(`#${popuSpan}`).html(popup);
    lastOpenedPopup = popuSpan;
  })
});
function cancelPopup(popuSpan) {
  jQuery(popuSpan).html('');
}

