<?php

session_start();

require('send-otp.php');

$output = "";

if (isset($_POST['verify'])) {
    
    if (isset($_SESSION['otp'])) {
        $inputotp = $_POST['inputotp'];
        echo ($inputotp);
        if ($_SESSION['otp'] == $inputotp) {
            header('Location: Fill-out-form.php');
        } else {
            $output = "<span>Invalid OTP. Please try again.</span>";
        }
    } else {
        // Handle the case where the OTP is not available
        $output = "<span>OTP not found.</span>";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/login.css">
    <title>OJT Portal</title>
</head>
<body style="background: #ececec;">


	<!-- Main Container -->

	<div class="container d-flex justify-content-center align-items-center min-vh-100 custom-container">

	<!-- Login Container -->

	<div class="row border rounded-5 p-4 bg-white shadow box-area">

	<!-- Right Box -->
		
	<div class="col-md-12 right-box">
		<div class="row align-items-center">
			
			<form method="post">
				<div class="header-text mb-4 text-center">
                    <h3 class="text-center my-3">Verify OTP</h3>
				</div>

				<div class="alert alert-danger mb-4" role="alert" style="display: <?php echo !empty($output) ? 'block' : 'none'; ?>;">
                    <?php echo $output; ?>
                </div>
				
				<div class="input-group mb-3">
					<input type="text" name="inputotp" class="form-control form-control-lg bg-light fs-6" placeholder="Enter OTP" autocomplete="off">
				</div>

                <div class="input-group mb-2">

                    <button type="submit" name="send" class="btn btn-success w-100">Send OTP</button>


				</div>

                <div class="input-group mb-2">

                    <button type="submit" name="verify" class="btn btn-success w-100">Submit OTP</button>

				</div>
				
			</form>

			
		</div>
	</div> 
	</div>
	</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center">
      <div class="modal-body py-5">
        <div class="spinner-border text-primary mb-3" role="status"></div>
        <h5>Please wait...</h5>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const form = document.querySelector('form');
  const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));

  form.addEventListener('submit', function (e) {
    const clickedButton = document.activeElement;

    if (clickedButton.name === "verify") {
      loadingModal.show(); // Show "please wait" only for Submit OTP
    }
  });
</script>


</body>
</html>