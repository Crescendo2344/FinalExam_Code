<?php
require_once 'db_config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Student Registration</h1>
        
        <div class="search-section">
            <h2>Search by:</h2>
            <div class="search-options">
                <input type="text" id="searchInput" placeholder="Enter search term">
                <select id="searchBy">
                    <option value="student_id">Student's ID</option>
                    <option value="last_name">Last Name</option>
                    <option value="first_name">First Name</option>
                    <option value="sex">Sex</option>
                    <option value="course">Course</option>
                    <option value="class_mode">Class Mode</option>
                </select>
                <button id="searchBtn">Search</button>
                <button id="showAllBtn">Show All Students</button>
                <button id="resetBtn">Reset</button>
            </div>
        </div>
        
         <div class="results-section">
            <h2>Student Records</h2>
            <div id="studentTable"></div>
        </div>
        
        <div class="form-section">
            <form id="studentForm">
                <input type="hidden" id="studentIdHidden" value="">
                
                <div class="form-group">
                    <label for="student_id">Student's ID:</label>
                    <input type="text" id="student_id" required>
                </div>
                
                <div class="form-group">
                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" required>
                </div>
                
                <div class="form-group">
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" required>
                </div>
                
                <div class="form-group">
                    <label for="sex">Sex:</label>
                    <select id="sex" required>
                        <option value="">Select Sex</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="course">Course:</label>
                    <input type="text" id="course" required>
                </div>
                
                <div class="form-group">
                    <label>Preferred Mode of Classes:</label>
                    <div class="radio-group">
                        <label><input type="radio" name="class_mode" value="Online" required> Online</label>
                        <label><input type="radio" name="class_mode" value="Face-to-face"> Face-to-face</label>
                        <label><input type="radio" name="class_mode" value="Hybrid"> Hybrid</label>
                    </div>
                </div>
                
                <div class="button-group">
                    <button type="button" id="addBtn">Add</button>
                    <button type="button" id="updateBtn" disabled>Update</button>
                    <button type="button" id="deleteBtn" disabled>Delete</button>
                    <button type="reset" id="resetFormBtn">Reset</button>
                </div>
            </form>
        </div>
        
    </div>

    <script src="script.js"></script>
</body>
</html>