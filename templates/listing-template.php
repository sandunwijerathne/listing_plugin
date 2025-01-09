<!DOCTYPE html>
<?php
// Get the current post object
global $post;
$post_id = $post->ID;
$meta_title = get_post_meta($post->ID, 'meta_title', true);
$meta_description = get_post_meta($post->ID, 'meta_des', true);
// Check if the post is published or still pending
    $post_status = get_post_status($post_id);

    if ($post_status === 'pending') {
      ?>
      
        
        <p>This listing is awaiting approval.</p>
        <?php
        
    }else{
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $meta_title; ?></title>
    <meta name="description" content="<?php echo $meta_description; ?>">
<link rel="stylesheet" id="plugin-style-css" href="<?php echo plugin_dir_url( __FILE__ ); ?>css/style.css?v=0.1" type="text/css" media="all">
 <!-- Include Fancybox CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css" />

    

    <!-- Include Fancybox CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css" />

    <!-- Include jQuery (make sure jQuery is loaded before Fancybox) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Include Fancybox JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>

    <script>
        jQuery(document).ready(function($) {

            // Initialize Fancybox for gallery images as a slider
            $('[data-fancybox="gallery"]').fancybox({
                buttons: [
                    "zoom", 
                    "slideShow", 
                    "fullScreen", 
                    "download", 
                    "thumbs", 
                    "close"
                ],
                loop: true,  // Loop through images
                protect: true, // Prevent right-click
                gutter: 0, // No space between images
                animationEffect: "fade", // Optional: fade transition for images
                transitionEffect: "slide", // Optional: slide transition for images
                preload: [0, 1], // Preload previous and next images
                caption: function(instance, item) {
                    return $(this).data('caption') || '';
                }
            });

            // Slider logic
            var slideIndex = 0;
            var slides = $('.slider-wrapper .slide');
            var totalSlides = slides.length;
            var slideInterval = 3000; // Interval in milliseconds (3 seconds)
            var fadeDuration = 1000; // Duration for fade effect in milliseconds

            // Function to move to the next slide
            function showNextSlide() {
                var currentSlide = $(slides[slideIndex]);

                // Fade out current slide and fade in the next one
                currentSlide.fadeOut(fadeDuration, function() {
                    slideIndex = (slideIndex + 1) % totalSlides; // Cycle through the slides
                    var nextSlide = $(slides[slideIndex]);
                    nextSlide.fadeIn(fadeDuration); // Fade in the next slide
                });

                updateTempoBar();
                updateDotNavigation();
            }

            // Function to start the automatic slide show
            function startSlider() {
                slides.hide(); // Initially hide all slides
                $(slides[slideIndex]).fadeIn(fadeDuration); // Show the first slide
                setInterval(showNextSlide, slideInterval); // Move to the next slide every 3 seconds
            }

            // Function to update the tempo bar
            function updateTempoBar() {
                $('.tempo-bar').stop(true).animate({
                    width: '100%'
                }, slideInterval, 'linear', function() {
                    $(this).css('width', '0%');
                });
            }

            // Function to update the dot navigation
            function updateDotNavigation() {
                $('#slider-dots .dot').removeClass('active'); // Remove active class from all dots
                $('#slider-dots .dot').eq(slideIndex).addClass('active'); // Add active class to the current dot
            }

            // Dot navigation click event
            $('#slider-dots .dot').click(function() {
                slideIndex = $(this).data('slide'); // Get the index of the clicked dot
                showNextSlide(); // Show the selected slide
            });

            // Initialize the slider
            startSlider();

            // Initialize dot navigation for the first slide
            updateDotNavigation();

            // Load more images logic
            var currentIndex = 6; // Start showing from the 6th image
            var totalImages = $('#gallery-container img').length; // Total number of images

            $('#load-more').click(function() {
                // Find the next set of images to show
                $('#gallery-container img').slice(currentIndex, currentIndex + 6).fadeIn();

                // Update the current index
                currentIndex += 6;

                // If we've shown all images, hide the "Load More" button
                if (currentIndex >= totalImages) {
                    $('#load-more').hide();
                }
            });
            var currentIndex = 6; // Start showing from the 6th image
    var totalImages = $('#gallery-container img').length; // Total number of images

    // Show first 6 images
    $('#gallery-container img').slice(0, 6).show(); 

    // Click event for 'Load More' button
    $('#load-more').click(function() {
        // Show the next 6 images
        $('#gallery-container img').slice(currentIndex, currentIndex + 6).fadeIn();

        // Update the current index
        currentIndex += 6;

        // If we've shown all images, hide the "Load More" button
        if (currentIndex >= totalImages) {
            $('#load-more').hide();
        }
    });
        });
    </script>
</head>
<body>

<div class="container">

    <div class="row">
        <div class="logo">
            <a href="index.php"> <?php 
                // Get the logo URL
                $logo = get_post_meta($post->ID, 'logo', true);
                  $contact_info = get_post_meta($post->ID, 'contact_info', true) ?: array();
    $description = get_post_meta($post->ID, 'description', true);
    $slug = get_post_field($post->ID, 'slug', true);
    $meta_tags = get_post_meta($post->ID, 'meta_tags', true);
    $logo = get_post_meta($post->ID, 'logo', true);
    $slideshow_pics = get_post_meta($post->ID, 'slideshow_pics', true) ?: array();
    $gallery_pics = get_post_meta($post->ID, 'gallery_pics', true) ?: array();

    ?>
 

    <?php
                $logo_url = $logo ? wp_get_attachment_url($logo) : '';
                if ($logo_url): ?>
                    <img src="<?php echo esc_url($logo_url); ?>" alt="Logo" >
                <?php else: ?>
                    <p>No logo set.</p>
                <?php endif; ?></a>
               
        </div>

  



 <div class="row">
        <div class="ss-slides">
            <section class="slide">
                <div class="slider-container">
                    <ul class="slider-wrapper" id="slider">
                        <?php if (!empty($slideshow_pics)): ?>
                            <?php 
                            $first_slide = true; // Flag to mark the first slide
                            foreach ($slideshow_pics as $pic): 
                            ?>
                                <li class="slide <?php echo $first_slide ? 'slide-current' : ''; ?>" style="display: none;">
                                    <img src="<?php echo wp_get_attachment_url($pic); ?>" alt="Slideshow Image">
                                </li>
                                <?php $first_slide = false; ?> <!-- After the first slide, set this flag to false -->
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No slideshow pictures added.</p>
                        <?php endif; ?>
                    </ul>
<ul class="slider-controls" id="slider-controls"></ul>



                    <div class="tempo-bar" id="barra" style="width: 95.8667%; overflow: hidden;"></div>
                </div>
            </section>
        </div>
    </div>


    </div>
    <section class="contentSec">

        <div class="tabset row text-right">
            <!-- Tab 1 -->
            <input type="radio" name="tabset" id="tab1" aria-controls="about" checked="">
            <label for="tab1">ABOUT</label>
            <!-- Tab 2 -->
            <input type="radio" name="tabset" id="tab2" aria-controls="photos">
            <label for="tab2">PHOTOS</label>
            <!-- Tab 3 -->
            <input type="radio" name="tabset" id="tab3" aria-controls="contact">
            <label for="tab3" class="tab3">CONTACT US</label>

            <div class="tab-panels text-left">
              <section id="about" class="tab-panel">
    <h3><?php echo $slug; ?></h3>
<p id="description">
    <?php 
    // Split the description into paragraphs based on line breaks
    $paragraphs = explode("\n", $description);
    $trimmed_description = '';
    $total_chars = 0;

    foreach ($paragraphs as $index => $para) {
        // Accumulate paragraphs until the total length reaches 200 characters
        $trimmed_description .= $para . "\n"; // Add the paragraph
        $total_chars += strlen($para);
        
        if ($total_chars >= 200) {
            break;
        }
    }

    // Display the trimmed content (first 200 characters)
    echo nl2br(esc_html($trimmed_description));
    ?>
    
    <span id="rest" style="display:none;">
        <?php
        // Display the rest of the content after 200 characters
        $remaining_description = substr($description, strlen($trimmed_description));
        echo nl2br(esc_html($remaining_description));
        ?>
    </span>
</p>

<div class="row text-center">
    <button id="toggle" class="readBtn">Read More</button>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleButton = document.getElementById('toggle');
    const restText = document.getElementById('rest');

    toggleButton.addEventListener('click', function() {
        if (restText.style.display === 'none') {
            restText.style.display = 'inline';
            toggleButton.textContent = 'Read Less';
        } else {
            restText.style.display = 'none';
            toggleButton.textContent = 'Read More';
        }
    });
});
</script>



</section>


                <section id="photos" class="tab-panel">

<div class="row galleryImgs gallerySec text-center">
        <p class="text-center sectionHeading">PHOTOS</p>
        <div id="gallery-container" class="row galleryImgs gallerySec text-center">
            <?php if (!empty($gallery_pics)): ?>
                <?php foreach ($gallery_pics as $pic): 
                    $image_url = wp_get_attachment_url($pic); 
                    $medium_url = wp_get_attachment_image_url($pic, 'medium'); 
                ?>
                    <!-- Use the medium image for thumbnail, full image in lightbox slider -->
                 <a href="<?php echo $image_url; ?>" class="lightbox" data-fancybox="gallery" data-caption="Image Caption">
                <img class="gallery-thumbnail" src="<?php echo $medium_url; ?>" alt="Image Alt" data-full-image="<?php echo $image_url; ?>" />
            </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No gallery images available.</p>
            <?php endif; ?>
        </div>
<div class="text-center">
    <button id="load-more">Load More</button>
</div>
    </div>
                </section>
                <section id="contact" class="tab-panel" >
                                        <div class="row" style="padding-top: 40px;">
                        <div class="left half formBox" id="formBox">
                        <p class="text-left sectionHeading">The form below is a convinient way to make contact</p>
                            <div id="mail-status"></div>
                            
                             <div id="frmContact">
                                 
     <form id="contactForm" method="POST" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">                            
    <div>
        <label>Name</label><span id="userName-info" class="info"></span><br>
        <input type="text" name="userName" id="userName" class="inputBox" required>
    </div>
    <div>
        <label>Email</label><span id="userEmail-info" class="info"></span><br>
        <input type="email" name="userEmail" id="userEmail" class="inputBox" required>
    </div>
    <div>
        <label>Phone</label><span id="phone-info" class="info"></span><br>
        <input type="text" name="phone" id="userPhone" class="inputBox" required>
    </div>
    <div>
        <label>Message</label><span id="message-info" class="info"></span><br>
        <textarea name="content" id="message" class="inputBox textarea" cols="60" rows="20" required></textarea>
    </div>
    <div>
        <span></span>
        <label class="captchaText">71139</label><span id="captcha-info" class="info"></span><br>
        <input type="text" name="key" size="30" id="key" class="inputBox" placeholder="Enter the above characters..." required>
        <input type="hidden" name="captcha" size="30" id="captcha" class="inputBox" value="71139">
    </div>

       <div>
        <button type="submit"  class="btnAction button-arounder" id="submitBtn" name="submit">Send</button>
    </div>
</form>
    
    
    
    
</div>
<?php
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    // Sanitize form data
    $user_name = sanitize_text_field($_POST['userName']);
    $user_email = sanitize_email($_POST['userEmail']);
    $user_phone = sanitize_text_field($_POST['phone']);
    $message_content = sanitize_textarea_field($_POST['content']);
    $captcha_key = sanitize_text_field($_POST['key']);
    $captcha_value = $_POST['captcha']; // Assuming this value is static for now (e.g., a hardcoded number)

    // Basic captcha check (for demonstration purposes, can be improved)
    if ($captcha_key === $captcha_value) {
        // Get the email address from post meta
        $contact_email = get_post_meta($post->ID, 'contact_info', true)['email'];

        // Set email subject and message
        $subject = "New Contact Form Submission from: " . $user_name;
        $message = "Name: $user_name\n";
        $message .= "Email: $user_email\n";
        $message .= "Phone: $user_phone\n";
        $message .= "Message: \n$message_content";

        // Set the headers for the email
        $headers = array('Content-Type: text/plain; charset=UTF-8');

        // Send the email using wp_mail()
        $mail_sent = wp_mail($contact_email, $subject, $message, $headers);

        // Check if the email was sent successfully
        if ($mail_sent) {
            echo '<p class="success">Thank you for your message! We will get back to you soon.</p>';
            
            echo '<script> jQuery(document).ready(function($) {$("#contactForm").hide(); $( "#tab3" ).trigger( "click" );$("html, body").animate({scrollTop: $("#contactBtn").offset().top}, 1000); });</script>';
        } else {
            echo '<p class="error">Oops! Something went wrong, please try again later.</p>';
            echo '<script> jQuery(document).ready(function($) { $( "#tab3" ).trigger( "click" );$("html, body").animate({scrollTop: $("#contactBtn").offset().top}, 1000);</script>';
        }
    } else {
        echo '<p class="error">Incorrect captcha code. Please try again.</p>';
        echo '<script> jQuery(document).ready(function($) { $( "#tab3" ).trigger( "click" );$("html, body").animate({scrollTop: $("#contactBtn").offset().top}, 1000); });</script>';
    }
}
?>

                         </div>
                        
                         <div class="left half contactBox" id="">
                            
                            <p class="text-left sectionHeading">Contact Details</p>
                            <div class="row contactContent">
                                <p>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#b47836" class="bi bi-envelope-fill" viewBox="0 0 16 16">
                                        <path d="M.05 3.555A2 2 0 0 1 2 2h12a2 2 0 0 1 1.95 1.555L8 8.414.05 3.555ZM0 4.697v7.104l5.803-3.558L0 4.697ZM6.761 8.83l-6.57 4.027A2 2 0 0 0 2 14h12a2 2 0 0 0 1.808-1.144l-6.57-4.027L8 9.586l-1.239-.757Zm3.436-.586L16 11.801V4.697l-5.803 3.546Z"></path>
                                        </svg>
                                <a href="mailto:kevin@sansafaris.co.za?Subject=Enquiry from Africa Tourism info"><?php echo esc_attr($contact_info['email'] ?? ''); ?></a></p>

                                <p>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#b47836" class="bi bi-telephone-fill" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511z"></path>
</svg>
                                <a href="callto:+27845559600"><?php echo esc_attr($contact_info['phone'] ?? ''); ?></a></p>
                                <p>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#b47836" class="bi bi-signpost-2-fill" viewBox="0 0 16 16">
<path d="M7.293.707A1 1 0 0 0 7 1.414V2H2a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h5v1H2.5a1 1 0 0 0-.8.4L.725 8.7a.5.5 0 0 0 0 .6l.975 1.3a1 1 0 0 0 .8.4H7v5h2v-5h5a1 1 0 0 0 1-1V8a1 1 0 0 0-1-1H9V6h4.5a1 1 0 0 0 .8-.4l.975-1.3a.5.5 0 0 0 0-.6L14.3 2.4a1 1 0 0 0-.8-.4H9v-.586A1 1 0 0 0 7.293.707z"></path>
</svg>
                            <a><?php echo esc_attr($contact_info['address'] ?? ''); ?></a></p>
                            </div>
                         </div>
                    </div>
                    
                </section>
            </div>

        </div>

    </section>
<div class="row text-center">
      <button type="button" id="contactBtn" class="footerBtn">click to make quick contact</button>
        
</div>	  
        
</div>
<footer> 
	   
    <p class="copyRights text-center">Powered by <a href="https://www.africatourisminfo.com" target="_blank" style="color: #b47836; font-size: 14px;">Africa Tourism Info</a></p>
<img src="<?php echo plugin_dir_url( __FILE__ ); ?>/img/footer-image.png?v=0.1" class="footerImg">
           </footer>
</body>
</html>
<?php
}
?>