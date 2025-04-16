<?php
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    die("Access Denied");
}
?>

<style>
footer {
  background-color: #333;
  color: white;
  text-align: center;
  padding: 20px;
  bottom: 0;
  width: 100%;
}

footer .footer-content p {
  margin: 0;
  font-size: 14px;
}

footer .footer-content a {
  text-decoration: none;
  color: white;
}
</style>

<footer>
  <div class="footer-content">
    <p>&copy; 2025 Student Portal. Made by <a href="https://instagram.com/siddharthvishwamitra">Siddharth Vishwamitra</a></p>
  </div>
</footer>