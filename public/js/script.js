jQuery(document).ready(function ($) {

  // show email popup on buttonn click
  $('#new-mail-submit').click(function (e) {
    e.preventDefault();
    $('#overlay').css({ 'display': 'block' })
    $('.email-popup').css({ 'display': 'block' })
  })

  //close mail popup
  $('#close-email-popup').click(function () {
    $('#overlay').css({ 'display': 'none' })
    $('#email-popup').css({ 'display': 'none' })
  })

  //delete a member
  $('.delete_da_member').click(function () {
    // $('.popup_container').fadeIn();
    let id = $(this).attr('data-del-id')
    let delUrl = $(this).attr('data-del-url')
    let memberName = $(this).attr('data-del-member')
    $('.popup_container .del_popup_actions #yes').attr('del-id', id)
    $('.member').html(`${id} . ${memberName}`)
    let popuSpan = `popup_${id}`;

    let popup = `<div class="popup_container">
                  <div class="del_popup">
                      <span>Are you sure you want to delete this member ? </span>
                    <div class="del_popup_actions">
                      <span id="no" onClick='hidePopup(${popuSpan})'>Cancel</span>
                      <span id="yes" onClick='deleteMember(${id},"${delUrl}")'>Yes</span>
                    </div>
                 </div>
                </div>`
    $('#overlay').css({ 'display': 'block' })
    $(`#${popuSpan}`).html(popup);
  })

  $('.del_popup_actions #no').click(function () {
    $('.popup_container').fadeOut();
  })

  $('#check_da_members_rows #cb-select-all-1').change(function () {
    let row_check_boxes = $('.check_memb_rows');
    let state = $(this).is(':checked') ? true : false
    row_check_boxes.each((index, checkbox) => {
      $(checkbox).prop('checked', state);
    })
  })

  $('#field-ids').change(function () {
    let checkboxes = $('.select-form-field-check');
    let state = $(this).is(':checked') ? true : false
    checkboxes.each((index, checkbox) => {
      $(checkbox).prop('checked', state);
    })
  })
  //this will make checkbox checked when a form filed gains focus
  preVal = {};
  let formFields = $('.form-field-input')
  formFields.each((index, field) => {
    //first get the previous text values from input fields, make object with key = index and value = text value
    preVal = { ...preVal, [index]: ($(field).val()).toLowerCase() }
    $(field).keyup(function () {
      //check if value changed by comparing the previous value of input field with its current value,change state variable accordingly
      let state = preVal[index] == ($(field).val()).toLowerCase() ? false : true;
      let id = $(field).attr('data-id');
      let checkbox = `#${id}`;
      $(checkbox).prop('checked', state);
    })
  });

  // pagination highlighting
  selectedPage = $('.pagination').attr('id')
  let numberedLinks = $('.numbered_links > a')
  numberedLinks.eq(selectedPage - 1).addClass('active');

  $('.notice-dismiss').click(function () {
    $('#message').fadeOut();
    console.log('clicked');
  })

});
//hide popup
function hidePopup(popuSpan) {
  jQuery('#overlay').css({ 'display': 'none' })
  jQuery(popuSpan).html('');
}
//delete member with given id
function deleteMember(id, delUrl) {
  if (id) {
    const deleteUrl = delUrl
    location.replace(`${deleteUrl}&del_id=${id}`);
  }
}
