<?php
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Toqa Store | We're Here to Help!</title>
    <link
      rel="website icon"
      type="image/png"
      href="toqaStorelogo.png"
    />
  </head>
  <body>
  <?php include 'header.php'; ?>

    </blockquote>

    <main>
      <br />
      <br />
      <blockquote>
        <h3>
          Have a question or need assistance? Reach out to our community
          moderators! We're here to help.
        </h3>
        <br /><br />

        <section id="contactInfo">
          <h2>Our Contact Information</h2>
          <fieldset>
            <legend>How to Reach Us</legend>
            <address>
              <p>
                <strong>Correspondence Address:</strong><br />
                14 Salah al-Din Street, East Jerusalem, Palestine<br />
              </p>

              <p><strong>Telephone:</strong> +972 54-670-2568</p>

              <p>
                <strong>Email:</strong>
                <a href="mailto:abdeentoqa97@gmail.com"
                  >abdeentoqa97@gmail.com</a
                >
              </p>

              <p>
                <strong>Office Hours:</strong> Sunday-Thursday, 9:00 AM - 5:00
                PM
              </p>
            </address>
          </fieldset>
        </section>

        <section id="contactForm">
          <h2>Contact Form</h2>
          <form
            action="http://yhassouneh.studentprojects.ritaj.ps/util/process.php"
            method="post"
            enctype="multipart/form-data"
          >
            <fieldset>
              <legend>Send Us a Message</legend>

              <label for="senderName">Your Name:</label>
              <input
                type="text"
                id="senderName"
                name="name"
                required
              /><br /><br />

              <label for="senderEmail">Your Email:</label>
              <input
                type="email"
                id="senderEmail"
                name="email"
                required
                size="30"
              /><br /><br />

              <label for="senderCity">Your Location (City):</label>
              <input type="text" id="senderCity" name="city" /><br /><br />

              <label for="messageSubject">Subject:</label>
              <input
                type="text"
                id="messageSubject"
                name="subject"
                required
              /><br /><br />

              <label for="messageContent">Message Content:</label><br />
              <textarea
                id="messageContent"
                name="message"
                rows="6"
                cols="50"
                required
              ></textarea
              ><br /><br />

              <button type="submit">Send Message</button>
              <button type="reset">Reset Form</button>
            </fieldset>
          </form>
        </section>
        <br /><br />
      </blockquote>
    </main>

    <?php include 'footer.php'; ?>
    
  </body>
</html>
