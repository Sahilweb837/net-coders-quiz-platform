document.getElementById('quizForm').addEventListener('submit', function (event) {
    let isValid = true;

    // Clear previous error messages
    document.querySelectorAll('.error-message').forEach(msg => msg.textContent = '');

    // Validate Name
    const name = document.getElementById('name').value.trim();
    if (name === '') {
        document.getElementById('name-error').textContent = 'Name is required.';
        isValid = false;
    }

    // Validate College
    const college = document.getElementById('college').value.trim();
    if (college === '') {
        document.getElementById('college-error').textContent = 'College name is required.';
        isValid = false;
    }

    // Validate Course
    const course = document.getElementById('course').value.trim();
    if (course === '') {
        document.getElementById('course-error').textContent = 'Course is required.';
        isValid = false;
    }

    // Validate Semester
    const semester = document.getElementById('semester').value.trim();
    if (semester === '') {
        document.getElementById('semester-error').textContent = 'Semester is required.';
        isValid = false;
    }

    // Validate Branch
    const branch = document.getElementById('branch').value.trim();
    if (branch === '') {
        document.getElementById('branch-error').textContent = 'Branch is required.';
        isValid = false;
    }

    // Validate Contact
    const contact = document.getElementById('contact').value.trim();
    const contactPattern = /^[0-9]{10}$/;
    if (contact === '') {
        document.getElementById('contact-error').textContent = 'Contact number is required.';
        isValid = false;
    } else if (!contactPattern.test(contact)) {
        document.getElementById('contact-error').textContent = 'Contact number must be 10 digits.';
        isValid = false;
    }
    // Validate whatsapp
    const whatsapp = document.getElementById('whatsapp').value.trim();
    console.log('whatsapp',whatsapp);
    if (whatsapp === '') {
        document.getElementById('whatsapp-error').textContent = 'Whatsapp number is required.';
        isValid = false;
    } else if (!contactPattern.test(whatsapp)) {
        document.getElementById('whatsapp-error').textContent = 'Whatsapp number must be 10 digits.';
        isValid = false;
    }

    // Validate Email
    const email = document.getElementById('email').value.trim();
    const emailPattern = /^[^@\s]+@[^@\s]+\.[^@\s]+$/;
    if (email === '') {
        document.getElementById('email-error').textContent = 'Email is required.';
        isValid = false;
    } else if (!emailPattern.test(email)) {
        document.getElementById('email-error').textContent = 'Enter a valid email address.';
        isValid = false;
    }

    // Prevent form submission if validation fails
    if (!isValid) {
        event.preventDefault();
    }
});
