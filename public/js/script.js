jQuery(document).ready(function ($) {
  let pluginUrl = "../wp-content/plugins/damembers";

  $('.delete_da_member').click(function () {
    $('.popup_container').fadeIn();
    let id = $(this).attr('data-del-id')
    $('.popup_container .del_popup_actions #yes').attr('del-id', id)
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

  $('.req').change(function () {
    console.log('====================================');
    console.log('hello there im a checknbox' + " " + $(this).is(':checked'));
    console.log('====================================');

  })


  // pagination highlighting
  selectedPage = $('.pagination').attr('id')
  let numberedLinks = $('.numbered_links > a')
  numberedLinks.eq(selectedPage - 1).addClass('active');

  $('.notice-dismiss').click(function () {
    $('#message').fadeOut();
    console.log('clicked');
  })

  function deleteMember(id) {
    $.ajax({
      method: 'post',
      url: `http://localhost/wordpress/wp-admin/admin.php?page=da-members`,
      contentType: 'application/json',
      Accept: 'application/json',
      data: JSON.stringify({ del: id }),
      success: function (res) {
        console.log(`response: ${res}`);
      },
      error: function (res) {
        console.log(`error: ${res.message}`);
      },
    })
  }

  $.ajax({
    method: 'get',
    url: `${pluginUrl}/db/fetch_countries.php?submit=1`,
    dataType: 'json',
    success: function (res) {
      console.log(`response: ${res}`);
    },
    error: function (res) {
    }
  })
});
