document.addEventListener("DOMContentLoaded", function () {
    loadBookings();

    document.getElementById("edit-booking-form").addEventListener("submit", function (e) {
        e.preventDefault();
        updateBooking();
    });
});

// Load bookings from the database
function loadBookings() {
    fetch("get_bookings.php")
        .then(response => response.json())
        .then(data => {
            let tableBody = document.getElementById("bookings-table-body");
            tableBody.innerHTML = "";

            if (data.length === 0) {
                document.getElementById("no-bookings").style.display = "block";
            } else {
                document.getElementById("no-bookings").style.display = "none";
                data.forEach(booking => {
                    let row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${booking.id}</td>
                        <td>${booking.user_name}</td>
                        <td>${booking.car_name}</td>
                        <td>${booking.pickup_date}</td>
                        <td>${booking.return_date}</td>
                        <td>${booking.pickup_status}</td>
                        <td>${booking.return_status}</td>
                        <td>
                            <button onclick="openEditModal(${booking.id}, '${booking.pickup_date}', '${booking.return_date}', '${booking.pickup_status}', '${booking.return_status}')">Edit</button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
            }
        })
        .catch(error => console.error("Error fetching bookings:", error));
}

// Open Edit Modal and populate fields
function openEditModal(id, pickupDate, returnDate, pickupStatus, returnStatus) {
    document.getElementById("edit-booking-id").value = id;
    document.getElementById("edit-pickup-date").value = pickupDate;
    document.getElementById("edit-return-date").value = returnDate;
    document.getElementById("edit-pickup-status").value = pickupStatus;
    document.getElementById("edit-return-status").value = returnStatus;
    document.getElementById("edit-modal").style.display = "block";
}

// Update booking data
function updateBooking() {
    let id = document.getElementById("edit-booking-id").value;
    let pickupDate = document.getElementById("edit-pickup-date").value;
    let returnDate = document.getElementById("edit-return-date").value;
    let pickupStatus = document.getElementById("edit-pickup-status").value;
    let returnStatus = document.getElementById("edit-return-status").value;

    fetch("update_booking.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id=${id}&pickup_date=${pickupDate}&return_date=${returnDate}&pickup_status=${pickupStatus}&return_status=${returnStatus}`
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            document.getElementById("edit-modal").style.display = "none";
            loadBookings(); // Reload updated bookings
        }
    })
    .catch(error => console.error("Error updating booking:", error));
}

// Close the modal
document.querySelector(".close-btn").addEventListener("click", function () {
    document.getElementById("edit-modal").style.display = "none";
});
