<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" href="css/transaction.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" charset="utf-8"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

  <div class="menu-btn">
    <i class="fas fa-bars"></i>
  </div>

  <div class="side-bar">
    <header>
      <div class="close-btn">
        <i class="fas fa-times"></i>
      </div>
      <img src="img/pennywise-logo.jpg" alt="pennywise logo">
      <h1>Pennywise</h1>
    </header>
    <div class="menu">

      <div class="item"><a href="dashboard.php"><i class="fas fa-desktop"></i>Dashboard</a></div>
      <div class="item">
        <a class="sub-btn"><i class="fas fa-table"></i>Revenue Collection<i class="fas fa-angle-right dropdown"></i></a>
        <div class="sub-menu">
          <a href="document.php" class="sub-item active">Document Tracking</a>
          <a href="transaction.php" class="sub-item">Transaction</a>
        </div>
      </div>

      <div class="form-item">
        <form action="logout.php" method="POST">
          <button type="submit" class="logout-btn"><i class="fas fa-info-circle"></i>Logout</button>
        </form>
      </div>
    </div>

  </div>

  <section class="main">
    <h1>Document Tracking</h1>
    <!-- Insert your code here or create another main section-->
  </section>

  <script src="js/dashboard.js"></script>
  <script src="js/chart.js"></script>
</body>

</html>