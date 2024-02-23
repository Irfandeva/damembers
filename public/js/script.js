jQuery(document).ready(function ($) {
  let pluginUrl = "../wp-content/plugins/damembers";

  $('.delete_da_member').click(function () {
    $('.popup_container').fadeIn();
    let id = $(this).attr('data-del-id')
    $('.popup_container .del_popup_actions #yes').attr('del_id', id)
  })

  $('.del_popup_actions #no').click(function () {
    $('.popup_container').fadeOut();
  })
  $('.del_popup_actions #yes').click(function () {
    // $('.popup_container').fadeOut();
    let id = $(this).attr('del_id')
    if (id) {
      location.replace(`http://localhost/wordpress/wp-admin/admin.php?page=da-members&del_id=${id}`)
    }
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
