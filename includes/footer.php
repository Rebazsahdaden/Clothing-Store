<footer>
  <style>
    footer {
      background: #1a1a1a;
      color: #f5f5f5;
      padding: 2rem 1rem;
      margin-top: 4rem;
      border-top: 1px solid rgba(255,255,255,0.1);
      font-family: 'Poppins', sans-serif;
    }

    .footer-content {
      max-width: 1200px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 2rem;
      padding: 1rem 0;
    }

    .footer-section h4 {
      color: #ff4d5a;
      margin-bottom: 1.5rem;
      font-size: 1.2rem;
      position: relative;
      padding-bottom: 0.5rem;
    }

    .footer-section h4::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 40px;
      height: 2px;
      background: #ff4d5a;
    }

    .footer-section p {
      color: #ccc;
      line-height: 1.6;
      margin: 0.5rem 0;
    }

    .footer-bottom {
      text-align: center;
      padding-top: 2rem;
      margin-top: 2rem;
      border-top: 1px solid rgba(255,255,255,0.1);
    }

    .footer-bottom p {
      margin: 0;
      font-size: 0.9rem;
      color: #999;
    }

    .footer-links {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .footer-links a {
      color: #ccc;
      text-decoration: none;
      transition: color 0.3s ease;
    }

    .footer-links a:hover {
      color: #ff4d5a;
    }

    @media (max-width: 768px) {
      .footer-content {
        grid-template-columns: 1fr;
        text-align: center;
      }

      .footer-section h4::after {
        left: 50%;
        transform: translateX(-50%);
      }
    }
  </style>

  <div class="footer-content">
    <div class="footer-section">
      <h4>About Us</h4>
      <p>Your premier destination for fashion management solutions, offering curated collections and seamless inventory control.</p>
    </div>
    
    <div class="footer-section">
      <h4>Quick Links</h4>
      <ul class="footer-links">
        <li><a href="privacy.php">Privacy Policy</a></li>
        <li><a href="terms.php">Terms of Service</a></li>
        <li><a href="contact.php">Contact Support</a></li>
      </ul>
    </div>
    
    <div class="footer-section">
      <h4>Connect With Us</h4>
      <p>Email: <a href="mailto:support@clothhub.com">support@clothhub.com</a></p>
      <p>Phone: +1 (555) 123-4567</p>
    </div>
  </div>

  <div class="footer-bottom">
    <p>&copy; <?php echo date("Y"); ?> Clothing Store Management System. All rights reserved.</p>
  </div>
</footer>
</body>
</html>