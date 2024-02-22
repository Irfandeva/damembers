jQuery(document).ready(function ($) {
  let pluginUrl = "../wp-content/plugins/damembers";
  // console.log('====================================');
  // console.log(`plugin url ${pluginUrl}`);
  // console.log('====================================');
  $.ajax({
    method: 'get',
    url: `${pluginUrl}/db/fetch_countries.php?submit=1`,
    dataType: 'json',
    success: function (res) {
      console.log(`response: ${res}`);
    },
    error: function (res) {
      console.log('====================================');
      console.log(res);
      console.log('====================================');
    }
  })
});
