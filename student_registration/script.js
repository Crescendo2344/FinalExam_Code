document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const searchBy = document.getElementById('searchBy');
    const searchBtn = document.getElementById('searchBtn');
    const resetBtn = document.getElementById('resetBtn');
    const studentForm = document.getElementById('studentForm');
    const addBtn = document.getElementById('addBtn');
    const updateBtn = document.getElementById('updateBtn');
    const deleteBtn = document.getElementById('deleteBtn');
    const resetFormBtn = document.getElementById('resetFormBtn');
    const studentTable = document.getElementById('studentTable');
    
     studentTable.innerHTML = '<p class="no-results">Use the search form to find students</p>';
    
    
    searchBtn.addEventListener('click', function() {
        const searchTerm = searchInput.value.trim();
        const searchField = searchBy.value;
        
        if (searchTerm) {
            searchStudents(searchField, searchTerm);
        } else {
            studentTable.innerHTML = '<p class="no-results">Please enter a search term</p>';
        }
    });
    
    
    resetBtn.addEventListener('click', function() {
        searchInput.value = '';
        studentTable.innerHTML = '<p class="no-results">Use the search form to find students</p>';
    });
    
    
    addBtn.addEventListener('click', function() {
        if (validateForm()) {
            const formData = getFormData();
            saveStudent(formData, 'add');
        }
    });
    
    
    updateBtn.addEventListener('click', function() {
    if (validateForm()) {
        if (!confirm('Are you sure you want to update this student?')) {
            return;
        }
        const formData = getFormData();
        saveStudent(formData, 'update');
    }
});
    
    
    deleteBtn.addEventListener('click', function() {
        if (confirm('Are you sure you want to delete this student?')) {
            const studentId = document.getElementById('studentIdHidden').value;
            deleteStudent(studentId);
        }
    });
    
    
    resetFormBtn.addEventListener('click', function() {
        resetForm();
    });
    
    
    function loadStudents() {
        fetch('api.php?action=read')
            .then(response => response.json())
            .then(data => {
                displayStudents(data);
            })
            .catch(error => console.error('Error:', error));
    }
    
    
    function searchStudents(field, term) {
    const searchTerm = term.trim();
    if (!searchTerm) {
        studentTable.innerHTML = '<p class="no-results">Please enter a search term</p>';
        return;
    }

    
    studentTable.innerHTML = '<p>Searching...</p>';
    
    fetch(`api.php?action=search&field=${encodeURIComponent(field)}&term=${encodeURIComponent(searchTerm)}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (Array.isArray(data)) {
                if (data.length === 0) {
                    studentTable.innerHTML = `
                        <p class="no-results">
                            No students found matching "${searchTerm}" in ${field.replace('_', ' ')}
                        </p>`;
                } else {
                    displayStudents(data);
                }
            } else if (data.error) {
                throw new Error(data.error);
            } else {
                throw new Error('Unexpected response format');
            }
        })
        .catch(error => {
            console.error('Search error:', error);
            studentTable.innerHTML = `<p class="error">Search failed: ${error.message}</p>`;
        });
}

    
    
    function displayStudents(students) {
    let html = `
        <table>
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Last Name</th>
                    <th>First Name</th>
                    <th>Sex</th>
                    <th>Course</th>
                    <th>Class Mode</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    students.forEach(student => {
        html += `
            <tr>
                <td>${student.student_id}</td>
                <td>${student.last_name}</td>
                <td>${student.first_name}</td>
                <td>${student.sex}</td>
                <td>${student.course}</td>
                <td>${student.class_mode}</td>
                <td>
                    <button onclick="editStudent('${student.id}')">Edit</button>
                </td>
            </tr>
        `;
    });
    
    html += `
            </tbody>
        </table>
    `;
    
    studentTable.innerHTML = html;
}
    
    function validateForm() {
        const studentId = document.getElementById('student_id').value.trim();
        const lastName = document.getElementById('last_name').value.trim();
        const firstName = document.getElementById('first_name').value.trim();
        const sex = document.getElementById('sex').value;
        const course = document.getElementById('course').value.trim();
        const classMode = document.querySelector('input[name="class_mode"]:checked');
        
        if (!studentId || !lastName || !firstName || !sex || !course || !classMode) {
            alert('Please fill in all fields');
            return false;
        }
        
        return true;
    }
    
    
    function getFormData() {
        return {
            id: document.getElementById('studentIdHidden').value,
            student_id: document.getElementById('student_id').value.trim(),
            last_name: document.getElementById('last_name').value.trim(),
            first_name: document.getElementById('first_name').value.trim(),
            sex: document.getElementById('sex').value,
            course: document.getElementById('course').value.trim(),
            class_mode: document.querySelector('input[name="class_mode"]:checked').value
        };
    }
    
   
    function saveStudent(formData, action) {
    const url = 'api.php';
    const options = {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: action,
            data: formData
        })
    };
    
    fetch(url, options)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (!data) {
                throw new Error('No data received');
            }
            if (data.error) {
                alert('Error: ' + data.error);
            } else if (data.success === false) {
                alert('Operation failed: ' + data.message);
            } else {
                alert(data.message || 'Operation successful');
                loadStudents();
                resetForm();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error: ' + error.message);
        });
}
    
    function deleteStudent(studentId) {
        fetch('api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'delete',
                id: studentId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                loadStudents();
                resetForm();
            } else {
                alert(data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }
    
    
    function resetForm() {
        studentForm.reset();
        document.getElementById('studentIdHidden').value = '';
        addBtn.disabled = false;
        updateBtn.disabled = true;
        deleteBtn.disabled = true;
    }
});


function editStudent(id) {
    fetch(`api.php?action=get&id=${id}`)
        .then(response => response.json())
        .then(student => {
            document.getElementById('studentIdHidden').value = student.id;
            document.getElementById('student_id').value = student.student_id;
            document.getElementById('last_name').value = student.last_name;
            document.getElementById('first_name').value = student.first_name;
            document.getElementById('sex').value = student.sex;
            document.getElementById('course').value = student.course;
            
            const classModeRadios = document.getElementsByName('class_mode');
            for (let radio of classModeRadios) {
                if (radio.value === student.class_mode) {
                    radio.checked = true;
                    break;
                }
            }
            
            document.getElementById('addBtn').disabled = true;
            document.getElementById('updateBtn').disabled = false;
            document.getElementById('deleteBtn').disabled = false;
        })
        .catch(error => console.error('Error:', error));
}