<?php 
// Include the database connection
include 'php/config.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
</body>

<div class="container-sm mt-4">
    <div class="col-lg-8 offset-lg-2">
        <?php 
        // Check if $conn is properly set
        if (!$conn) {
            die("Database connection failed: " . mysqli_connect_error());
        }

        // Fetch posts and associated user information
        $query = $conn->query("SELECT p.*, CONCAT(u.firstname, ' ', u.middlename, ' ', u.lastname) AS name 
            FROM posts p 
            INNER JOIN uers u ON u.id = p.user_id 
            ORDER BY UNIX_TIMESTAMP(p.date_created) DESC");

        while ($row = $query->fetch_assoc()):
        ?>
            <div class="card mt-4 mb-4 posts">
                <div class="card-body" style="padding-left: unset; padding-right: unset" data-id="<?php echo $row['id'] ?>">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-sm-8">
                                <p><b><?php echo ucwords($row['name']) ?></b>
                                <small><i> <?php echo date("M d, Y h:i A", strtotime($row['date_created'])) ?></i></small>
                                </p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <p><?php echo $row['content'] ?></p>
                            </div>
                        </div>
                        <div class="row">
                            <?php
                            $_f = array();
                            if (!empty($row['file_ids'])) {
                                $files = $conn->query("SELECT * FROM file_uploads WHERE id IN (" . $row['file_ids'] . ")");
                                while ($frow = $files->fetch_assoc()) {
                                    $_f[] = $frow;
                                }
                            }
                            ?>
                            <div class="pImg-holder">
                                <div id="carousel_<?php echo $row['id'] ?>" class="carousel slide">
                                     <ol class="carousel-indicators">
                                        <?php 
                                        $active = 0;
                                        foreach ($_f as $f):
                                            $a = $active === 0 ? 'active' : '';
                                            ?>
                                            <li data-target="#carouselExampleIndicators" data-slide-to="<?php echo $active ?>" class="<?php echo $a ?>"></li>
                                        <?php $active++; endforeach; ?>
                                      </ol>
                                    <div class="carousel-inner">
                                        <?php 
                                        $active = 0;
                                        foreach ($_f as $f):
                                            $a = $active === 0 ? 'active' : '';
                                            ?>
                                            <div class="carousel-item <?php echo $a ?>">
                                                <img src="assets/<?php echo $f['file_path'] ?>" class="d-block w-100" alt="">
                                            </div>
                                        <?php $active++; endforeach; ?>
                                    </div>
                                    <a class="carousel-control-prev" href="#carousel_<?php echo $row['id'] ?>" role="button" data-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Previous</span>
                                    </a>
                                    <a class="carousel-control-next" href="#carousel_<?php echo $row['id'] ?>" role="button" data-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Next</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row comment-field">
                            <?php
                            $data = $conn->query("SELECT c.*, CONCAT(u.firstname, ' ', u.middlename, ' ', u.lastname) AS uname 
                                FROM comments c 
                                INNER JOIN uers u ON u.id = c.user_id 
                                WHERE c.post_id = " . $row['id']);
                            while ($c = $data->fetch_assoc()):
                            ?>
                            <div class="comment">
                                <div class="col-md-12">
                                    <p><b class="usr"><i><?php echo ucwords($c['uname']) ?></i></b> 
                                    <small class="dt"><?php echo date("M d, Y", strtotime($c['date_created'])) ?></small>
                                    <br><small class="cntnt"><?php echo $c['comment'] ?></small></p>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-11">
                                <textarea name="comment[<?php echo $row['id'] ?>]" cols="30" rows="1" class="form-control cmt-field" placeholder="Write a comment"></textarea>
                            </div>
                            <div class="col-md-1 text-center">
                                <button type="button" class="btn btn-primary cmt_btn" data-id="<?php echo $row['id'] ?>"><i class="fa fa-paper-plane"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<nav class="navbar">
    <ul>
        <li><a href="home.php">Home</a></li>
        <li><a href="activity.html">Activity</a></li>
        <li><a href="addNew.html">Add New</a></li>
        <li><a href="profile.html">Profile</a></li>
    </ul>
</nav>
</html>
