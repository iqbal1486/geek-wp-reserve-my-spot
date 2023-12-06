<?php
function wprms_shortcode_form_masterclass($atts) {   
    // Output HTML structure and JavaScript functionality for the modal
    ob_start();

    wp_enqueue_script( 'wprms-intlTelInput-js');
    wp_enqueue_script( 'wprms-intl-tel-input-utils-js');
    wp_enqueue_style( 'wprms-intlTelInput');
    wp_enqueue_style( 'wprms-style');
      ?>
      <div class="reserve_my_spot_container reserve_my_spot_wrapper">
            <button class="watch_master_class_now">Reserve My Spot</button>
            <div class="form-container reserve_my_spot_form">
                  <div class="form-container-top">
                        <div>&#9655; Playing for a limited time only</div>
                  </div>
                  <div class="form-container-bottom">
                        <p class="legend">All times are shown in your local time</p>

                        <form action="#" id="reserve_my_spot" method="post">
                              <input type="hidden" name="aff" value="<?php echo (isset($_GET['aff'])) ? $_GET['aff'] : ""; ?>">
                              <div class="form-inner">
                              <div class="form-group">
                              <div  class="form-input-container">
                                    <input type="text" id="subscriber-name" name="last_name" autocomplete="off" placeholder="" required
                                          class="with-label">
                                    <label for="subscriber-name">Name *</label>
                              </div> </div>
                              <div  class="form-group">
                                    <div  class="form-input-container">
                                    <input type="email" id="subscriber-email" name="email_address" autocomplete="off" placeholder=""
                                          required class="with-label">
                                    <label for="subscriber-email">Email *</label>
                              </div> </div>
                              <div  class="form-group">
                                    <div  class="form-input-container">
                                    <select name="date" required oninvalid="this.setCustomValidity('Please select the date')"  oninput="this.setCustomValidity('')">
                                          <!-- Sample dates; add dates as needed -->
                                          <option value="" disabled selected>Select date</option>
                                          <option value="0">Watch Now Instantly</option>
                                          <option value="1">Watch Later Today</option>
                                    </select>
                              </div> </div>
                              <div  class="form-group">
                                    <div  class="form-input-container">
                                    <select name="time" required>
                                          <!-- Sample times; add times as needed -->
                                          <option value="" disabled selected>Select time</option>
                                          <option value="8:00">8:00 AM</option>
                                          <option value="9:00">9:00 AM</option>
                                          <option value="10:00">10:00 AM</option>
                                          <option value="11:00">11:00 AM</option>
                                          <option value="12:00">12:00 PM</option>
                                          <option value="13:00">1:00 PM</option>
                                          <option value="14:00">2:00 PM</option>
                                          <option value="15:00">3:00 PM</option>
                                          <option value="16:00">4:00 PM</option>
                                          <option value="17:00">5:00 PM</option>
                                          <option value="18:00">6:00 PM</option>
                                          <option value="19:00">7:00 PM</option>
                                          <option value="20:00">8:00 PM</option>
                                          <option value="21:00">9:00 PM</option>
                                          <option value="22:00">10:00 PM</option>
                                          <option value="23:00">11:00 PM</option>
                                    </select>
                              </div> </div>
                              <div  class="form-group">
                                    <div  class="form-input-container">
                                    <select name="gender">
                                          <option value="" disabled selected>Select gender</option>
                                          <option value="man">Man</option>
                                          <option value="woman">Woman</option>
                                          <option value="other">Other</option>
                                    </select> </div>
                              </div>
                              <div  class="form-group">
                                    <div  class="form-input-container">
                                    <input type="tel" name="phone" id="phone" autocomplete="off" placeholder="Phone Number">
                              </div> </div>
                              </div>
                                    <div class="submit_button_wrapper">
                                          <button type="submit" name="reserve_my_spot">Reserve My Spot Now</button>
                                          <div id="loader" class="custom-loader custom-hidden"></div>
                                    </div>
                             
                              
                        </form>
                        <p class="disclaimer">
                              By registering for the above, you confirm that you agree to the <a href="/terms-and-conditions/">Terms of Use</a> &
                              the <a href="/privacy-policy/">Privacy Policy</a> as well as receiving notification for future events. You can
                              withdraw your consent at any time by unsubscribing.
                        </p>
                  </div>
            </div>

            <div class="masterclass-guard-overlay"
                  style="overflow: auto;position: fixed;transition: opacity 0.3s ease-in;top: 0;left: 0;right: 0;bottom: 0;display: none;flex-direction: column;align-items: center;justify-content: center;background: rgba(77,77,77,0.8);z-index: 1000002;opacity: 0;">
                  <iframe src="" style="height: 500px; max-width: 100%; width: 400px;"></iframe>
            </div>
      </div>
      <?php
      wp_enqueue_script( 'wprms-js');
      return ob_get_clean();
}
add_shortcode('form_masterclass', 'wprms_shortcode_form_masterclass');



function wprms_add_to_calendar_button_callback($atts) {
    // Output HTML structure and JavaScript functionality for the modal
    ob_start();
    
    $start_date   = isset($_REQUEST['a']) ? $_REQUEST['a'] : "";
    $end_date     = isset($_REQUEST['b']) ? $_REQUEST['b'] : "";
    $decryptor    = new SimpleEncryptor();
    $start_date   = $decryptor->decrypt($start_date);
    $end_date     = $decryptor->decrypt($end_date);

    $userTimeZone = getTimeZoneFromIpAddress();
      // Create a DateTime object with the user's input and original time zone
    $userDateTime = new DateTime($start_date, new DateTimeZone('America/New_York'));
    // Set the time zone to Eastern Standard Time (EST)
    $estTimeZone = new DateTimeZone($userTimeZone);
    $userDateTime->setTimezone($estTimeZone);
    // Format the date and time in EST
    $start_date = $userDateTime->format('Y-m-d H:i');
    

    $userDateTime = new DateTime($end_date, new DateTimeZone('America/New_York'));
    // Set the time zone to Eastern Standard Time (EST)
    $estTimeZone = new DateTimeZone($userTimeZone);
    $userDateTime->setTimezone($estTimeZone);
    // Format the date and time in EST
    $end_date = $userDateTime->format('Y-m-d H:i');
    
      ?>
      <!-- Include: AddEvent theme css -->
      <link rel="stylesheet" href="https://cdn.addevent.com/libs/atc/themes/fff-theme-8/theme.css" type="text/css" media="all" />
      <style type="text/css">
            .addeventatc{
                  background: #1a8ba1;    
                  font-weight: bold;
            }
            .addeventatc:hover{
                  background-color: #27a2ba;
            }            
      </style>
      <script type="text/javascript" src="https://cdn.addevent.com/libs/atc/1.6.1/atc.min.js" async defer></script>
      <!-- Button code -->
      <div title="Add to Calendar" class="addeventatc">
          Add to Calendar
          <span class="start"><?php echo $start_date; ?></span>
          <span class="end"><?php echo $end_date; ?></span>
          <span class="title">FREE Masterclass: How to Go From Invisible to Interesting</span>
          <span class="description">FREE Masterclass: How to Go From Invisible to Interesting (Even if You Doubt Yourself, Have a ‘Boring’ Life, or Don’t Know What to Say)<br><br>Check your email for the link to the webinar</span>
          <span class="location">Online</span>
          <span class="alarm_reminder">15</span>
          <span class="timezone"><?php echo $userTimeZone; ?></span>
          <span class="client">aUHhswgnGzACHxdjImZO254444</span>
      </div>      
      <?php
      return ob_get_clean();
}
add_shortcode('add_to_calendar_button', 'wprms_add_to_calendar_button_callback');
?>