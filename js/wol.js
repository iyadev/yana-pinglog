function wol(id) {
   $.ajax({
      url:'action.php?action=pinglog_wol&id='+id, // URL for the PHP file,
      complete: function (response) {
          $("#outping"+id).html("Wake on lan : OK");
      },
      error: function () {
          $("#outping"+id).html("Wake on lan : KO");
      },
  });
  return false;
}
