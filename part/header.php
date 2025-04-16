<?php
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    die("Access Denied");
}
?>
<style>
body {
    margin: 0;
}
header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 20px;
  background-color: #333;
  color: white;
}

header .logo h2 {
  margin: 0;
  color: #fff;
}

header nav ul {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
}

header nav ul li {
  margin: 0 15px;
}

header nav ul li a {
  color: white;
  text-decoration: none;
}

header nav ul li a:hover {
  text-decoration: underline;
}

/* Media query for mobile devices */
@media (max-width: 768px) {
  header {
    flex-direction: column;
    text-align: center;
  }

  header .logo {
    margin-bottom: 10px;
  }

  header nav ul {
    flex-direction: row;
    align-items: center;
  }

  header nav ul li {
    margin: 10px;
  }
}

/* Media query for very small devices like phones in portrait mode */
@media (max-width: 480px) {
  header nav ul li a {
    font-size: 14px;
  }
}
</style>
<header>
  <div class="logo">
    <h2>Quiz Master Challenge</h2>
  </div>
  <nav>
    <ul>
      <li><a href="/">Home</a></li>
      <li><a href="#about">About</a></li>
      <li><a href="#services">Services</a></li>
      <li><a href="#contact">Contact</a></li>
    </ul>
  </nav>
</header>