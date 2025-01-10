function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition, showError);
    } else {
        alert("Geolocation is not supported by this browser.");
    }
}

function showPosition(position) {
    document.getElementById('latitude').value = position.coords.latitude;
    document.getElementById('longitude').value = position.coords.longitude;
}

function showError(error) {
    switch(error.code) {
        case error.PERMISSION_DENIED:
            alert("User denied the request for Geolocation.");
            break;
        case error.POSITION_UNAVAILABLE:
            alert("Location information is unavailable.");
            break;
        case error.TIMEOUT:
            alert("The request to get user location timed out.");
            break;
        case error.UNKNOWN_ERROR:
            alert("An unknown error occurred.");
            break;
    }
}


// Fetch service types from the database
document.addEventListener('DOMContentLoaded', function() {
    // getLocation(); // Get location on page load

    fetch('get_service_types.php')
        .then(response => response.json())
        .then(data => {
            const serviceTypeSelect = document.getElementById('service_type');
            let options = '<option value="">Select Service Type</option>';
            data.forEach(type => {
                options += `<option value="${type.service_name}">${type.service_name}</option>`;
            });
            serviceTypeSelect.innerHTML = options;
        })
        .catch(error => console.error('Error fetching service types:', error));
});



// Add any custom JavaScript functionality here
document.addEventListener('DOMContentLoaded', () => {
    console.log('Scripts Loaded');
});
