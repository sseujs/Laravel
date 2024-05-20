<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Select Excel File</title>
<style>
    .popup {
  display: none;
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background-color: #ffffff;
  border: 1px solid #000000;
  border-radius: 5px;
  padding: 20px;
  z-index: 9999;
}

.popup-content {
  text-align: center;
}

.close {
  position: absolute;
  top: 10px;
  right: 10px;
  cursor: pointer;
}
</style>
</head>
<body>
<P>From Loan Transaction Report Report version c, Show Data only</p>
<button id="openPopup">Select Excel File</button>

<div id="popup" class="popup">
  <div class="popup-content">
    <span class="close">&times;</span>
    <h2>Select Excel File</h2>
    <input type="file" id="excelFile" accept=".xlsx, .xls">
  </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
  var openPopupButton = document.getElementById('openPopup');
  var popup = document.getElementById('popup');
  var closePopupButton = popup.querySelector('.close');
  var excelFileInput = document.getElementById('excelFile');

  openPopupButton.addEventListener('click', function() {
    popup.style.display = 'block';
  });

  closePopupButton.addEventListener('click', function() {
    popup.style.display = 'none';
  });

  excelFileInput.addEventListener('change', function() {
    var selectedFile = excelFileInput.files[0];
    console.log(selectedFile);
    // Do something with the selected file
  });
});
</script>
</body>
</html>
