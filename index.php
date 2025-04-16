<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal</title>
    <link rel="stylesheet" href="css/font.css">
<body>
<?php
include 'part/header.php';
?>
<div class="hero">
  <div class="overlay"></div>
  <div class="content">
    <h1 class="text">Vivekananda Quiz</h1>
    <p class="text">Participate and win prizes!</p>
  </div>
</div>
<div class="container">
  <h2>Welcome to the Student Portal</h2>
  <p>Choose one of the options below to proceed</p>


  <div class="link-section">
    <a href="instructions" class="link-button">Instructions</a>
    <a href="pay-fee" class="link-button">Fee Payment</a>
    <a href="register" class="link-button">Registration</a>
    <a href="print_reg" class="link-button">Print Registration</a>
    <a href="admit_card" class="link-button">Admit Card</a>
    <a href="check_result" class="link-button">Results</a>
  </div>
</div>

<?php
include 'part/footer.php';
?>
<style>
body {
  margin: 0;
  background: #fafafa;
}

.hero {
  display: flex;
  flex-direction: column;
  background-image: url("https://languagescout.pages.dev/files/assets/bg_edu.jpg");
  height: 80vh;
  align-self: center;
  background-repeat: no-repeat;
  background-size: cover;
  background-attachment: fixed;
  align-items: center;
  justify-content: center;
  background-position: center;
  margin: 0.5em;
  padding: 1em;
  border-radius: 10px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
  position: relative;
}

/* Dark overlay */
.overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.7);
  border-radius: 10px;
  z-index: 1;
}

/* Content (text and other elements) */
.content {
  position: relative;
  z-index: 2;
  text-align: center;
}

/* Text styles */
.text {
  font-size: 1.4rem;
  color: white;
}

@media (min-width: 600px) {
  .hero {
    max-height: 400px;
    background-repeat: no-repeat;
    background-size: contain;
    background-position: bottom;
  }
}

.container {
  width: 70%;
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
  text-align: center;
}

/* Links Section */
.link-section {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 10px;
  margin-bottom: 40px;
  max-width: 800px;
  margin: 0 auto;
  padding: 10px;

}

.link-button {
display: flex;
justify-content: center;
text-align: center;
align-items: center;
padding: 12px 20px;
background-color: #3b5998;
border: 1px solid #ddd;
color: #fff;
text-decoration: none;
font-size: 18px;
border-radius: 5px;
transition: background-color 0.3s, transform 0.3s;
height: 100px;

}

.link-button:hover {
  background-color: #0056b3;
  transform: translateY(-2px);
}

/* Mobile responsiveness */
@media (max-width: 600px) {
  .container {
      width: 100%;
  }
  h2 {
    font-size: 1.5rem;
  }

  .link-button {
    font-size: 15px;
    padding: 12px 24px;
  }

  .link-section {
    flex-direction: column;
    gap: 15px;
  }
}
</style> 
</body>
</html>
