jQuery(document).ready(function ($) {
  let pluginUrl = "../wp-content/plugins/damembers";

  $('.delete_da_member').click(function () {
    // $('.popup_container').fadeIn();
    let id = $(this).attr('data-del-id')
    let memberName = $(this).attr('data-del-member')
    $('.popup_container .del_popup_actions #yes').attr('del-id', id)
    $('.member').html(`${id} . ${memberName}`)
    let popuSpan = `popup_${id}`;

    let popup = `<div class="popup_container">
<div class="del_popup">
  <span>Are you sure you want to delete this member ? </span>
  <!--- <span class="member">${id} . ${memberName}</span>--->
  <div class="del_popup_actions">
    <span id="no" onClick='hidePopup(${popuSpan})'>Cancel</span>
    <span id="yes" onClick='deleteMember(${id})'>Yes</span>
  </div>
</div>
</div>`
    $('#overlay').css({ 'display': 'block' })
    $(`#${popuSpan}`).html(popup);

  })

  $('.del_popup_actions #yes').click(function () {
    // $('.popup_container').fadeOut();
    let id = $(this).attr('del-id')
    if (id) {
      location.replace(`http://localhost/wordpress/wp-admin/admin.php?page=da-members&del_id=${id}`)
    }
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
function hidePopup(popuSpan) {
  jQuery('#overlay').css({ 'display': 'none' })
  jQuery(popuSpan).html('');
}
function deleteMember(id) {
  if (id) {
    // jQuery('#overlay').css({ 'display': 'none' })
    location.replace(`http://localhost/wordpress/wp-admin/admin.php?page=da-members&del_id=${id}`)
  }
}