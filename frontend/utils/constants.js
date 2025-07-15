var Constants = {
  get_api_base_url: function () {
    if(location.hostname == 'localhost'){
      return "/web-programming-final/";
    } else {
      return "";
    }
  }
};