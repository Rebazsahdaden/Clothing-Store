
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Clothing Store Management System</title>
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    header {
      background: #1a1a1a;
      padding: 1rem 2rem;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    nav ul {
      list-style: none;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: space-between;
      align-items: center;
      max-width: 1200px;
      margin: 0 auto;
    }

    nav li {
      margin: 0 1.2rem;
    }

    nav a {
      color: #f5f5f5;
      text-decoration: none;
      font-family: 'Poppins', sans-serif;
      font-weight: 500;
      font-size: 1.1rem;
      padding: 0.5rem 1rem;
      transition: all 0.3s ease;
      position: relative;
    }

    nav a::before {
      content: '';
      position: absolute;
      width: 0;
      height: 2px;
      bottom: 0;
      left: 0;
      background-color: #ff4d5a;
      transition: width 0.3s ease;
    }

    nav a:hover::before {
      width: 100%;
    }

    nav a:hover {
      color: #ff4d5a;
    }

    .brand {
      font-size: 1.5rem;
      font-weight: 700;
      color: #ff4d5a;
      margin-right: auto;
    }

    .brand span {
      color: #f5f5f5;
    }

    @media (max-width: 768px) {
      nav ul {
        flex-wrap: wrap;
        justify-content: center;
      }
      
      .brand {
        width: 100%;
        text-align: center;
        margin-bottom: 1rem;
      }
      
      nav li {
        margin: 0 0.8rem;
      }
      
      nav a {
        font-size: 1rem;
      }
    }
  </style>
</head>
<body>
  <header>
    <nav>
      <ul>
        <li ><a class="brand" href="dashboard.php"> <span>Cloth</span>Hub</a></li>
        <li><a href="dashboard.php">Home</a></li>
        <li><a href="login.php">Logout</a></li>
      </ul>
    </nav>
  </header>