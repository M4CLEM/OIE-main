<!-- Begin Page Content -->
            <div class="col-md-12 my-3">
                <div class="card shadow-sm px-5">
                    <div class="py-5">
                        <form action="deployment-process.php" method="POST" enctype="multipart/form-data">
                            <h2 class="mb-5 text-center">DEPLOYMENT INFORMATION</h2>
                            <div class="row">
                                <div class="col-6">
                                    
                                    <h4 class="mb-2">Company Info</h4>

                                    <div class="alert alert-danger mb-4 text-center" role="alert" style="display: <?php echo !empty($output) ? 'block' : 'none'; ?>;">
                                        <?php echo $output; ?>
                                    </div>

                                    <div class="pt-3 mb-2">
                                        <label for="companyName">Company Name:</label>
                                        <input type="text" class="form-control" id="companyName" name="companyName" placeholder="Enter Company Name" any value="" required>
                                    </div>
                                    <div class="pt-3 mb-2">
                                        <label for="companyAddress">Company Address:</label>
                                        <input type="text" class="form-control" id="companyAddress" name="companyAddress" placeholder="Enter Company Address" any value="" required>
                                    </div>
                                    <div class="pt-3 mb-2">
                                        <label for="trainerContact">Trainer's Contact Number:</label>
                                        <input type="text" class="form-control" id="trainerContact" name="trainerContact" placeholder="Enter Contact Number" any value="" required>
                                    </div>
                                    <div class="pt-3 mb-2">
                                        <label for="trainerEmail">Trainer's Email Address:</label>
                                        <input type="text" class="form-control" id="trainerEmail" name="trainerEmail" placeholder="Enter Trainer's Email Address" any value="" required>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h4 class="mb-4">Work Type</h4>
                                    <div class="row mb-5">
                                        <div class="col">
                                            <label for="workType">Type of work:</label>
                                            <select class="form-control" name="workType" id="select">
                                                <option hidden disable value="select">Select</option>
                                                <option value="WFH">Work from Home</option>
                                                <option value="Onsite">On site</option>
                                                <option value="PB">Project-based</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col text-right">
                                            <button type="submit" class="btn btn-success" name="submit"><span class="fas fa-save fw-fa"></span> Submit Info</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>