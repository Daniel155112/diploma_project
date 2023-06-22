<!DOCTYPE html>
<html>
<head>
  <title>Download Page</title>
  <style>
    body {
      background-color: #f2f2f2;
      font-family: Arial, sans-serif;
    }
    
    .container {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      height: 100vh;
      background-color: #f2f2f2;
    }
    
    .download-card {
      width: 400px;
      max-width: 90%;
      background-color: #ffffff;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      border-radius: 8px;
      padding: 20px;
      text-align: center;
    }
    
    .download-title {
      font-size: 24px;
      font-weight: bold;
      margin-bottom: 20px;
      color: #333333;
    }
    
    #download-form {
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    
    .download-button {
      background-color: #4CAF50;
      color: white;
      border: none;
      padding: 10px 20px;
      font-size: 18px;
      cursor: pointer;
      border-radius: 5px;
      margin-top: 20px;
    }
    
    .download-button:hover {
      background-color: #45a049;
    }
    
    .disclaimer {
            position: fixed;
            bottom: 10px;
            right: 10px;
            font-size: 18px;
            color: blue;
        }
  </style>
</head>
<body>
  <div class="container">
    <div class="download-card">
      <div class="download-title">Download Page</div>
      <form id="download-form" action="/?route=process-generating-form" method="POST">
        <button type="submit" name="action" value="generate" class="download-button">Download</button>
      </form>
    </div>
  </div>
</body>
</html>
