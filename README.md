# Student Registration System

This project is an online student registration system built using PHP (PDO), MySQL, HTML, Tailwind CSS, and JavaScript. It allows users to register students and manage their information efficiently.

## Features

- **Student Registration Form**: Collects student information such as first name, last name, gender, date of birth, village, city, province, phone number, email, photo, accommodation type, previous school, major, and academic year.
- **Student List**: Displays a list of registered students with options to search by name, major, and academic year.
- **Responsive Design**: Utilizes Tailwind CSS for a modern and responsive user interface.
- **Form Validation**: JavaScript validation to ensure data integrity before submission.

## Project Structure

```
register-learning
├── config
│   ├── config.php          # Configuration settings for the application
│   └── database.php        # Database connection using PDO
├── database
│   └── register-learning.sql # SQL schema for the database
├── public
│   ├── assets
│   │   ├── css
│   │   │   └── style.css   # Custom styles using Tailwind CSS
│   │   ├── js
│   │   │   ├── form-validation.js # JavaScript for form validation
│   │   │   └── script.js    # Additional JavaScript functionalities
│   │   └── uploads
│   │       └── photos       # Directory for uploaded student photos
│   ├── favicon.ico          # Favicon for the application
│   └── index.php            # Main entry point of the application
├── src
│   ├── classes
│   │   ├── AcademicYear.php # Class for academic year operations
│   │   ├── Database.php     # Class for database management
│   │   ├── Major.php        # Class for major operations
│   │   └── Student.php      # Class for student operations
│   ├── helpers
│   │   ├── functions.php     # Helper functions
│   │   └── validation.php    # Validation functions for form inputs
│   └── lang
│       └── la.php           # Language translations in Lao
├── templates
│   ├── components
│   │   ├── footer.php       # Footer component
│   │   ├── header.php       # Header component
│   │   └── navigation.php    # Navigation component
│   ├── dashboard.php        # Dashboard for users
│   ├── includes
│   │   └── messages.php      # Success/error messages
│   ├── register.php         # Registration form template
│   ├── search.php           # Search functionality for students
│   └── students-list.php    # List of registered students
├── .gitignore               # Files to be ignored by Git
├── .htaccess                # URL rewriting and server configurations
├── composer.json            # Composer dependencies
├── package.json             # npm dependencies and scripts
├── README.md                # Project documentation
└── tailwind.config.js       # Tailwind CSS configuration
```

## Installation

1. Clone the repository to your local machine.
2. Import the `register-learning.sql` file into your MySQL database.
3. Configure the database connection in `config/database.php`.
4. Install dependencies using Composer and npm.
5. Start your local server and navigate to `public/index.php`.

## Usage

- Access the registration form to add new students.
- View the list of registered students and use the search feature to find specific records.

## Contributing

Feel free to submit issues or pull requests for improvements or bug fixes.

## License

This project is open-source and available under the MIT License.