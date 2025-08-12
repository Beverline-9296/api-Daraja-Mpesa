<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Donation Packages</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #200678ff;
            
            
        }
        h1{
            color:purple;
            size: 200px;
            text-align:center;
        }
        section{
            display:flex;
            justify-content: center;
            align-items: flex-start;
            gap: 20px;
            padding: 20px;
            flex-wrap: wrap;
        }

        .card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            width: 250px;
            text-align: center;
            padding: 20px;
        }

        .card img {
            width: 100px;
            height: 100px;
        }

        .price {
            color: dodgerblue;
            font-size: 1.5em;
            margin: 10px 0;
        }

        .package-name {
            font-weight: bold;
            color: dodgerblue;
            font-size: 1.2em;
            margin-bottom: 10px;
        }

        .card p {
            font-size: 0.9em;
            color: #555;
        }

        form {
            margin-top: 15px;
        }

        form input {
            width: 90%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        form button {
            background-color: dodgerblue;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        form button:hover {
            background-color: #0d6efd;
        }
    </style>
</head> 

<body>
       <h1>Welcome to our donation platform</h1>

       <?php
       // Display success or error messages
       if (isset($_GET['success'])) {
           echo '<div style="background: #d4edda; color: #155724; padding: 10px; margin: 20px; border-radius: 5px; text-align: center;">';
           echo '<strong>✅ ' . htmlspecialchars($_GET['success']) . '</strong>';
           echo '</div>';
       }
       
       if (isset($_GET['error'])) {
           echo '<div style="background: #f8d7da; color: #721c24; padding: 10px; margin: 20px; border-radius: 5px; text-align: center;">';
           echo '<strong>❌ ' . htmlspecialchars($_GET['error']) . '</strong>';
           echo '</div>';
       }
       ?>

<section>       
    <div class="card">
        <img src="image\bronze.jpeg" alt="Bronze Package">
        <div class="price">Ksh. 100</div>
        <div class="package-name">Bronze Package</div>
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptatum eveniet, magni modi dolorem omnis sunt.</p>
        <form action="action.php" method="POST">
            <input type="hidden" name="package" value="Bronze">
            <input type="hidden" name="amount" value="100">
            <input type="text" name="phone" placeholder="Mpesa Phone Number" required>
            <button type="submit" name="submit">Donate</button>
        </form>
    </div>

    <div class="card">
        <img src="image/silver.jpeg" alt="Silver Package">
        <div class="price">Ksh. 500</div>
        <div class="package-name">Silver Package</div>
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptatum eveniet, magni modi dolorem omnis sunt.</p>
        <form action="action.php" method="POST">
            <input type="hidden" name="package" value="Silver">
            <input type="hidden" name="amount" value="500">
            <input type="text" name="phone" placeholder="Mpesa Phone Number" required>
            <button type="submit" name="submit">Donate</button>
        </form>
    </div>

    <div class="card">
        <img src="image/gold.jpeg" alt="Gold Package">
        <div class="price">Ksh. 1000</div>
        <div class="package-name">Gold Package</div>
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptatum eveniet, magni modi dolorem omnis sunt.</p>
        <form action="action.php" method="POST">
            <input type="hidden" name="package" value="Gold">
            <input type="hidden" name="amount" value="1000">
            <input type="text" name="phone" placeholder="Mpesa Phone Number" required>
            <button type="submit" name="submit">Donate</button>
        </form>
    </div>
</section>
</body>
</html>
?>