<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/login.css">
    <title>OJT Portal</title>
</head>
<body>

    <!-- Main Container -->
    <div class="container d-flex justify-content-center align-items-center min-vh-100 custom-container">

        <!-- Login Container -->
       <div class="row border rounded-5 p-4 bg-white shadow box-area">
            <!-- Right Box -->
            <div class="col-md-12 right-box">
                <div class="row align-items-center">
                        <div class="header-text mb-5 text-center">
                            <h3>Choose your current role!</h3>
                        </div>

                        <div class="input-group mb-3">
                            <a class="btn btn-success w-100 rounded-2" href="student-register.php">Student</a>					
                        </div>

                        <div class="input-group mb-3">
                            <a class="btn btn-success w-100 rounded-2" href="register.php?role=Adviser">Adviser</a>					
                        </div>
                        
                        <div class="input-group mb-3">
                            <a class="btn btn-success w-100 rounded-2" href="register.php?role=Coordinator">Coordinator</a>					
                        </div>

                        <div class="row text-center mt-5">
                            <small>Already registered? <a href="index.php">Log in</a></small>
                        </div>
                </div>
            </div> 
        </div>
    </div>

</body>
</html>