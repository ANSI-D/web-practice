var Constants = {
  get_api_base_url: function () {
    if(location.hostname == 'localhost'){
      return "http://localhost/final-2025-fall/backend/rest";
    } else {
      return "http://localhost/final-2025-fall/backend/rest";
    }
  }
};