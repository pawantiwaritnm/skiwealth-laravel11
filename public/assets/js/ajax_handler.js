function invalid_user_handler() {
  deleteCookie("user_logged_in");

  Swal.fire({
    title: "Heya!",

    text: "Please login first",

    icon: "warning",
  });

  setTimeout(() => {
    window.location.href = "login.php";
  }, 1000);
}

function err_handler(error_msg) {
  Swal.fire({
    title: "Heya!",

    text: error_msg,

    icon: "warning",
  });
}

function unknown_err_handler() {
  Swal.fire({
    title: "Heya!",

    text: "Something went wrong, Please try again.",

    icon: "error",
  });
}

function success_handler({ message }) {
  Swal.fire("Success", message, "success");
}

function form_validation_handler({ validation_errors }) {
  $(".validation_errors").empty();
  $(".validation_errors").append(validation_errors);
}

function fetch_post(
  url,
  form,
  cb_success,
  cb_error,
  cb_form_validator,
  unknown_handler
) {
  $(".full_loader").show();
  fetch(HOST + url, {
    method: "POST",
    mode: "cors",
    body: getObjectType(form) == "formdata" ? form : new FormData(form),
  })
    .then((res) => res.json())
    .then((response) => {
      $(".full_loader").hide();
      switch (response.status) {
        case 0: {
          if (cb_error) cb_error(response);
          else err_handler(response.error);

          break;
        }

        case 1: {
          if (cb_success) cb_success(response);
          else success_handler(response);

          break;
        }

        case -1: {
          invalid_user_handler();

          break;
        }

        case -2: {
          if (cb_form_validator) cb_form_validator(response);
          else form_validation_handler(response);

          break;
        }

        default: {
          if (unknown_handler) unknown_handler(response);
          else unknown_err_handler(response);
        }
      }
    })

    .catch((err) => {
      $(".full_loader").hide();
      console.log(err);
      err_handler(err);
    });
}

function fetch_get(url, cb_success, cb_error) {
  fetch(HOST + url)
    .then((res) => res.json())

    .then((response) => {
      switch (response.status) {
        case 0: {
          if (cb_error) cb_error(response.error);
          else err_handler(response.error);

          break;
        }

        case 1: {
          if (cb_success) cb_success(response);
          else success_handler(response.message);

          break;
        }

        case -1: {
          invalid_user_handler();

          break;
        }

        case -2: {
          form_validation_handler(response.validation_errors);

          break;
        }

        default:
          unknown_err_handler(response);
      }
    })

    .catch((err) => {
      console.log(err);

      if (cb_error) cb_error(err);
      else err_handler(err);
    });
}

function ajax(url, cb_success, type = "get", data, cb_error) {
  $.ajax({
    url: HOST + url,

    type,

    data,

    dataType: "json",

    success: function (response) {
      switch (response.status) {
        case 0: {
          if (cb_error) cb_error(response.error);
          else err_handler(response.error);

          break;
        }

        case 1: {
          if (cb_success) cb_success(response.data);
          else success_handler(response.message);

          break;
        }

        case -1: {
          invalid_user_handler();

          break;
        }

        case -2: {
          form_validation_handler(response.validation_errors);

          break;
        }

        default:
          unknown_err_handler(response);
      }
    },

    error: function (err) {
      console.log(err);

      err_handler(response);
    },
  });
}
const getObjectType = (function (global) {
  let cache = {};
  return function (obj) {
    let key;
    return obj === null
      ? "null" // null
      : obj === global
      ? "global" // window in browser or global in nodejs
      : (key = typeof obj) !== "object"
      ? key // basic: string, boolean, number, undefined, function
      : obj.nodeType
      ? "object" // DOM element
      : cache[(key = {}.toString.call(obj))] || // cached. date, regexp, error, object, array, math
        (cache[key] = key.slice(8, -1).toLowerCase()); // get XXXX from [object XXXX], and cache it
  };
})(this);
