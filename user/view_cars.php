<?php
include"db.php";
// Fetch available cars
$query = "SELECT id, type, model, price, image FROM cars WHERE status = 'available'";
$result = mysqli_query($conn, $query);
?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Available Cars</h2>
    <div class="row">
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="../admins/uploads/<?php echo htmlspecialchars($row['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['name']); ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($row['type']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($row['model']); ?></p>
                        <p class="card-text"><strong>Price:</strong> $<?php echo number_format($row['price'], 2); ?> per day</p>
                        <a href="book_car.php?car_id=<?php echo $row['id']; ?>" class="btn btn-primary">Book Now</a>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
