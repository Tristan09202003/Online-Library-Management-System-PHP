<div class="navbar navbar-inverse set-radius-zero">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand">

                <img src="assets/img/0.png" />
            </a>

        </div>

        <div class="right-div">
            <a href="logout.php" class="btn btn-danger pull-right">LOG OUT</a>
        </div>
    </div>
</div>
<!-- LOGO HEADER END-->
<section class="menu-section">
    <div class="container">
        <div class="row ">
            <div class="col-md-12">
                <div class="navbar-collapse collapse ">
                    <ul id="menu-top" class="nav navbar-nav navbar-right">
                        <li><a href="dashboard.php" class="menu-top-active">DASHBOARD</a></li>

                        <li>
                            <a href="#" class="dropdown-toggle" id="ddlmenuItem" data-toggle="dropdown"> Categories <i class="fa fa-angle-down"></i></a>
                            <ul class="dropdown-menu" role="menu" aria-labelledby="ddlmenuItem">
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="add-category.php">Add Category</a></li>
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="manage-categories.php">Manage Categories</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="#" class="dropdown-toggle" id="ddlmenuItem" data-toggle="dropdown"> Authors <i class="fa fa-angle-down"></i></a>
                            <ul class="dropdown-menu" role="menu" aria-labelledby="ddlmenuItem">
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="add-author.php">Add Author</a></li>
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="manage-authors.php">Manage Authors</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="#" class="dropdown-toggle" id="ddlmenuItem" data-toggle="dropdown"> Books <i class="fa fa-angle-down"></i></a>
                            <ul class="dropdown-menu" role="menu" aria-labelledby="ddlmenuItem">
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="add-book.php">Add Book</a></li>
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="manage-books.php">Manage Books</a></li>
                            </ul>
                        </li>

                        <li>
                            <a href="#" class="dropdown-toggle" id="ddlmenuItem" data-toggle="dropdown"> Issue Books <i class="fa fa-angle-down"></i></a>
                            <ul class="dropdown-menu" role="menu" aria-labelledby="ddlmenuItem">
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="issue-book.php">Issue New Book</a></li>
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="manage-issued-books.php">Manage Issued Books</a></li>
                            </ul>
                        </li>
                        <li><a href="reg-students.php">Reg Students</a></li>

                        <?php if (isset($_SESSION['login']) && $_SESSION['login']) {

                            // Ensure session is started
                            if (session_status() == PHP_SESSION_NONE) {
                                session_start();
                            }

                            // Check if the user is logged in
                            if (isset($_SESSION['stdid'])) {
                                try {
                                    // Get user ID from session
                                    $sid = $_SESSION['stdid'];

                                    // Corrected query
                                    $sql = "SELECT * FROM admin WHERE id = :id";
                                    $query = $dbh->prepare($sql);
                                    $query->bindParam(':id', $sid, PDO::PARAM_INT);
                                    $query->execute();

                                    // Fetch results
                                    $result = $query->fetch(PDO::FETCH_OBJ);

                                    // Determine the profile image source
                                    $userImageSrc = ($result && !empty($result->ProfileImage))
                                        ? "assets/img/" . htmlspecialchars($result->ProfileImage)
                                        : "assets/img/noprofil.jpg";
                                } catch (Exception $e) {
                                    // Handle database errors gracefully
                                    $userImageSrc = "assets/img/noprofil.jpg";
                                    // Log the error for debugging
                                    error_log("Profile image query error: " . $e->getMessage());
                                }
                            } else {
                                // Default image for non-logged-in users
                                $userImageSrc = "assets/img/noprofil.jpg";
                            }
                        } ?>

                        <style>
                            .user-profile-circle {
                                width: 30px;
                                height: 30px;
                                border-radius: 50%;
                                object-fit: cover;
                                cursor: pointer;
                                border: 2px solid white;
                                transition: transform 0.2s;
                                justify-content: center;
                            }

                            .user-profile-circle:hover {
                                transform: scale(1.1);
                            }

                            .navbar-nav>li>a.profile-link {
                                padding: 5px;
                            }
                        </style>
                        <div class="right-div">
                            <a href="admin_profile.php" class="profile-link">
                                <img src="<?php echo $userImageSrc; ?>" class="user-profile-circle" alt="Profile" />
                            </a>
                        </div>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</section>