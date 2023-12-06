<?php
function wprms_shortcode_lightbox_form_masterclass($atts) {   
    // Output HTML structure and JavaScript functionality for the modal
    ob_start();

    wp_enqueue_script( 'wprms-intlTelInput-js');
    wp_enqueue_script( 'wprms-intl-tel-input-utils-js');
    wp_enqueue_style( 'wprms-bootstrap');
    wp_enqueue_style( 'wprms-intlTelInput');
    wp_enqueue_style( 'wprms-style');
      ?>

      <div class="reserve_my_spot_container reserve_my_spot_wrapper reserve_my_spot_lightbox_form">
          <button class="universalbutton webinarbutton" id="open-btn">Reserve My Spot</button>
          <div id="modal-background">
              <div id="modal">
                  <span id="close-btn">&times;</span>
                  <div class="form-containers reserve_my_spot_form">
                      <div class="form-container-bottom">
                          <form id="reserve_my_spot" class="form" method="post">
                              <input type="hidden" name="aff" value="<?php echo (isset($_GET['aff'])) ? $_GET['aff'] : ""; ?>">
                              <ul class="progress-bar">
                                  <li class="progress-bar__dot full"></li>
                                  <li class="progress-bar__connector"></li>
                                  <li class="progress-bar__dot"></li>
                                  <li class="progress-bar__connector"></li>
                                  <li class="progress-bar__dot"></li>
                                  <li class="progress-bar__connector"></li>
                                  <li class="progress-bar__dot"></li>
                              </ul>
                              <div class="step step1" id="step1">
                                  <h3>To get advice relevant to you, choose below</h3>
                                  <div class="form-inner">
                                      <div class="form-group">
                                          <div class="form-input-container">
                                              <div class="radio-group">
                                                  <input type="radio" id="man" name="gender" value="man" required>
                                                  <label for="man">Man</label>
                                                  <input type="radio" id="woman" name="gender" value="woman" required>
                                                  <label for="woman">Woman</label>
                                                  <input type="radio" id="other" name="gender" value="other" required>
                                                  <label for="other">Other</label>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                              <div class="step step2 hidden" id="step2">
                                  <h3>Choose time</h3>
                                  <div class="form-inner">
                                      <div class="form-group">
                                          <div class="form-input-container">
                                              <select name="date" required
                                                  oninvalid="this.setCustomValidity('Please select the date')"
                                                  oninput="this.setCustomValidity('')">
                                                  <!-- Sample dates; add dates as needed -->
                                                  <option value="" disabled selected>Select date</option>
                                                  <option value="0">Watch Now Instantly</option>
                                                  <option value="1">Watch Later Today</option>
                                              </select>
                                          </div>
                                      </div>
                                      <div class="form-group">
                                          <div class="form-input-container">
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
                                          </div>
                                      </div>
                                  </div>
                              </div>
                              <div class="step step3 hidden" id="step3">
                                  <h4>Get a text reminder (optional)</h4>
                                  <div class="form-inner">
                                      <div class="form-group">
                                          <div class="form-input-container">
                                              <input type="tel" name="phone" id="phone" autocomplete="off" placeholder="" required>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                              <div class="step step4 hidden" id="step4">
                                  <h3>Reserve your spot</h3>
                                  <div class="form-inner">
                                      <div class="form-group">
                                          <div class="form-input-container">
                                              <input type="text" id="subscriber-name" name="last_name" autocomplete="off"
                                                  placeholder="" required class="with-label">
                                              <label for="subscriber-name">Name *</label>
                                          </div>
                                      </div>
                                      <div class="form-group">
                                          <div class="form-input-container">
                                              <input type="email" id="subscriber-email" name="email_address" autocomplete="off"
                                                  placeholder="" required class="with-label">
                                              <label for="subscriber-email">Email *</label>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                              <div class="button-group">
                                  <button id="previous" class="disabled button" disabled>
                                  </button>
                                  <button id="next" type="button" class="button">Next</button>
                              </div>
                              <div class="button-group submit_button_wrapper">
                                  <button type="submit" id="validate" class="hidden button" name="reserve_my_spot">Reserve My Spot Now</button>
                                  <div id="loader" class="custom-loader custom-hidden"></div>
                                  <p class="disclaimer hidden" id="lightbox_disclaimer">
                                      By registering for the above, you confirm that you agree to the <a
                                          href="/terms-and-conditions/">Terms of Use</a> &
                                      the <a href="/privacy-policy/">Privacy Policy</a> as well as receiving notification for future
                                      events. You can
                                      withdraw your consent at any time by unsubscribing.
                                  </p>
                              </div>
                          </form>
                      </div>
                  </div>
              </div>
          </div>
      </div>
<?php
      wp_enqueue_script( 'wprms-bootstrap');
      wp_enqueue_script( 'wprms-js');
      wp_enqueue_script( 'wprms-lightbox-js');

      return ob_get_clean();
}
add_shortcode('lightbox_form_masterclass', 'wprms_shortcode_lightbox_form_masterclass');
?>