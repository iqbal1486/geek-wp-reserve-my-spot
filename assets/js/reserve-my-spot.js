$ = jQuery;
const input = document.querySelector("#phone");
const form = document.querySelector("#reserve_my_spot");

const iti = window.intlTelInput(input, {
      initialCountry: "auto",
      hiddenInput: "full_phone",
      geoIpLookup: function(callback) {
          fetch("https://ipapi.co/json")
            .then(function(res) { return res.json(); })
            .then(function(data) { callback(data.country_code); })
            .catch(function() { callback("us"); });
      },
      separateDialCode: true,
});

form.onsubmit = () => {
  if (!iti.isValidNumber()) {
    return false;
  }
};

$(document).ready(function() {

      $("button.watch_master_class_now").click(function() {
          $([document.documentElement, document.body]).animate({
              scrollTop: $("div.reserve_my_spot_form").offset().top
          }, 500);
      });

      $('.reserve_my_spot_form form#reserve_my_spot').on('submit', function(e){
            e.preventDefault();
            onJoinMasterclass(e);
            
      });
      
      //$('.reserve_my_spot_form form button[type="submit"]').on('click', onJoinMasterclass);

      //  $("input[type='tel']").intlTelInput({
      //       initialCountry: "auto",
      //       hiddenInput: "full_phone",
      //       geoIpLookup: function(callback) {
      //           fetch("https://ipapi.co/json")
      //             .then(function(res) { return res.json(); })
      //             .then(function(data) { callback(data.country_code); })
      //             .catch(function() { callback("us"); });
      //       },
      //       separateDialCode: true,
      //       //utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/11.0.4/js/utils.js"
      // });


      var tomorrow = after_days(1);
      var nextTomorrow = after_days(2);
      $('.reserve_my_spot_form select[name="date"]').append(
            `<option value="${tomorrow.dateString}">${tomorrow.formatted}</option>`);

      $('.reserve_my_spot_form select[name="date"]').append(
            `<option value="${nextTomorrow.dateString}">${nextTomorrow.formatted}</option>`);

      $(document).on('change', '.reserve_my_spot_form select[name="date"]', function(e) {
            $('.reserve_my_spot_form select[name="time"]').html(
                  '<option value="" disabled selected>Select time</option>');
            if (e.target.value == '0') {
                  $('.reserve_my_spot_form select[name="time"]').append(
                        '<option value="0:00" selected>Start Now</option>');
            } else if (e.target.value == '1') {
                  var now = new Date();
                  var hour = now.getHours();
                  if (hour < 23) {
                        if (hour < 8) hour = 7;
                        for (var i = hour + 1; i <= 23; i++) {
                              $('.reserve_my_spot_form select[name="time"]').append(
                                    `<option value="${i}:00">${i >= 13 ? i - 12 : i}:00 ${i >= 13 ? 'PM' : 'AM'}</option>`
                              );
                        }
                  }
            } else {
                  for (var i = 8; i <= 23; i++) {
                        $('.reserve_my_spot_form select[name="time"]').append(
                              `<option value="${i}:00">${i >= 13 ? i - 12 : i}:00 ${i >= 13 ? 'PM' : 'AM'}</option>`
                        );
                  }
            }
      });
});

function after_days(days) {
      var now = Date.now();
      var after = now + days * 86400 * 1000;
      var date = new Date(after);
      var dateString = date.toLocaleDateString();
      var dateComponents = dateString.split('/');
      var monthNames = [
            'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October',
            'November', 'December'
      ];
      var dayOfWeek = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"][date.getDay()];
      dateComponents[0] = monthNames[parseInt(dateComponents[0]) - 1];
      var formatted = dayOfWeek + ', ' + dateComponents.slice(0, 2).join(' ') + ', ' + date.getFullYear();
      return {
            dateString,
            formatted
      };
}

function dt() {
      var t = function t(e) {
            return e ? (e ^ 16 * Math.random() >> e / 4).toString(16) : ([1e7] + -1e3 + -4e3 + -8e3 + -1e11)
                  .replace(/[018]/g, t)
      }();
      return window.localStorage.setItem("ckid", t), t
};

function ht() {
      return window.localStorage.getItem("ckid") || dt()
}

function redirect_masterclass(data) {
      var now_or_later = data['date'];
      var gender = data['gender'];
      var url = "";
      var curPage = window.location.href;
      curPage = curPage.substring(0, curPage.length - 1);
      if (now_or_later == "0") {
            window.location.href = curPage + "-video" + (gender == "man" ? "" : "-f");
      } else {
            window.location.href = curPage + "-thank-you";
      }
}

function toESTDate(hour, watchDate) {
      var hour = hour.split(':')[0];
      // Get the current date and time
      var currentDate = watchDate != '1' ? new Date(watchDate) : new Date();
      // Add one hour to the current time
      currentDate.setHours(hour);
      // Convert the time to the EST time zone
      var estOffset = -5; // Eastern Standard Time (EST) offset is -5 hours
      currentDate.setHours(currentDate.getHours() + estOffset);
      // Format the date in the yyyy-mm-dd HH:MM format
      var formattedDate = currentDate.toISOString().slice(0, 16).replace("T", " ");
      return formattedDate;
}

function onJoinMasterclass(e) {
      e.preventDefault();
      var i = $('.reserve_my_spot_form form')[0];
      //console.log(i, i.format);
      var s = document.referrer;
      var o = new FormData(i);
      
      var formData = Object.fromEntries(o.entries());
      
      if (formData["date"] == "0") {
            webinar_date =  "watch directly";
            webinar_time =  formData["time"];
      } else {
            webinar_date =  formData["date"];
            webinar_time =  formData["time"];
      }
      var data = {
            action: 'pass_to_convertkit',
            referrer: s,
            data: formData,
            user: ht(),
            webinar_date: webinar_date,
            webinar_time: webinar_time,
            ckjs_version: 6,
      };
      //$('#submitButton').prop('disabled', true);
      $('#loader').show();

      $.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            data: data,
            success: function(response) {
                  console.log(response);
                  $('#loader').hide();
                  if(response.success){
                        window.location.href = response.data.redirect_url;     
                  }
                  //redirect_masterclass(formData);
            },
            error: function(xhr, status, error) {
                  console.log('AJAX request failed: ' + status + ', ' + error);
            }
      });
}