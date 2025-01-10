document.getElementById('registrationForm').addEventListener('submit', function (e) {
    let isValid = true;

    // Name validation
    const name = document.getElementById('name').value.trim();
    const namePattern = /^[A-Za-z\s]+$/; // Only allows letters and spaces
    if (name === '' || name.length < 3 || !namePattern.test(name)) {
        isValid = false;
        document.getElementById('nameError').innerText = 'Full Name must be at least 3 characters long and contain only letters.';
    } else {
        document.getElementById('nameError').innerText = '';
    }

    // Email validation
    const email = document.getElementById('email').value.trim();
    const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
    if (!emailPattern.test(email)) {
        isValid = false;
        document.getElementById('emailError').innerText = 'Please enter a valid email address.';
    } else {
        document.getElementById('emailError').innerText = '';
    }

    // Password validation
    const password = document.getElementById('password').value.trim();
    if (password.length < 6) {
        isValid = false;
        document.getElementById('passwordError').innerText = 'Password must be at least 6 characters long.';
    } else {
        document.getElementById('passwordError').innerText = '';
    }

    // Phone validation
    const phone = document.getElementById('phone').value.trim();
    const phonePattern = /^[0-9]{10}$/;
    if (!phonePattern.test(phone)) {
        isValid = false;
        document.getElementById('phoneError').innerText = 'Phone number must be 10 digits.';
    } else {
        document.getElementById('phoneError').innerText = '';
    }

    // Citizenship number validation
    const citizenship_no = document.getElementById('citizenship_no').value.trim();
    if (citizenship_no.length < 5) {
        isValid = false;
        document.getElementById('citizenshipError').innerText = 'Citizenship number must be at least 5 characters long.';
    } else {
        document.getElementById('citizenshipError').innerText = '';
    }

    // Address validation
    const address = document.getElementById('address').value.trim();
    if (address === '') {
        isValid = false;
        document.getElementById('addressError').innerText = 'Address cannot be empty.';
    } else {
        document.getElementById('addressError').innerText = '';
    }

    // Image validation
    const image = document.getElementById('image').files[0];
    if (image && !['image/jpeg', 'image/png', 'image/gif'].includes(image.type)) {
        isValid = false;
        document.getElementById('imageError').innerText = 'Profile image must be a valid image file (jpg, png, gif).';
    } else {
        document.getElementById('imageError').innerText = '';
    }

    // Service type validation
    const service_type = document.getElementById('service_type').value;
    if (service_type === '') {
        isValid = false;
        document.getElementById('serviceTypeError').innerText = 'Please select a service type.';
    } else {
        document.getElementById('serviceTypeError').innerText = '';
    }

    // Profile description validation
    const profile_description = document.getElementById('profile_description').value.trim();
    if (profile_description === '' || profile_description.length < 10) {
        isValid = false;
        document.getElementById('descriptionError').innerText = 'Profile description must be at least 10 characters long.';
    } else {
        document.getElementById('descriptionError').innerText = '';
    }

    // Latitude and Longitude validation
    const latitude = document.getElementById('latitude').value.trim();
    const longitude = document.getElementById('longitude').value.trim();
    if (latitude === '' || longitude === '') {
        isValid = false;
        alert('Please get the location before submitting the form.');
    }

    if (!isValid) {
        e.preventDefault();
    }
});

function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            document.getElementById('latitude').value = position.coords.latitude;
            document.getElementById('longitude').value = position.coords.longitude;
        }, function() {
            alert('Unable to retrieve your location.');
        });
    } else {
        alert('Geolocation is not supported by this browser.');
    }
}
