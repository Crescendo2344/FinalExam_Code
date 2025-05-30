<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db_config.php';


$input = file_get_contents('php://input');
$data = json_decode($input, true);

$action = $_GET['action'] ?? ($data['action'] ?? '');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    switch ($action) {
        case 'read':
            $stmt = $pdo->query("SELECT * FROM students ORDER BY last_name, first_name");
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;
            
        case 'search':
    $field = $_GET['field'] ?? '';
    $term = $_GET['term'] ?? '';
    
    
    $validFields = ['student_id', 'last_name', 'first_name', 'sex', 'course'];
    if (!in_array($field, $validFields)) {
        echo json_encode(['error' => 'Invalid search field']);
        break;
    }
    
    try {
        
        if ($field === 'sex') {
            $stmt = $pdo->prepare("SELECT * FROM students WHERE LOWER($field) = LOWER(:term) ORDER BY last_name, first_name");
            $stmt->bindParam(':term', $term, PDO::PARAM_STR);
        } 
        
        else {
            $stmt = $pdo->prepare("SELECT * FROM students WHERE $field LIKE CONCAT('%', :term, '%') ORDER BY last_name, first_name");
            $stmt->bindParam(':term', $term, PDO::PARAM_STR);
        }
        
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        
        error_log("Search executed: field=$field, term=$term, results=".count($results));
        
        echo json_encode($results);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
    break;
            
        case 'get':
            $id = $_GET['id'] ?? 0;
            $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
            break;
            
        case 'add':
            if (!isset($data['data'])) {
                echo json_encode(['success' => false, 'message' => 'Invalid data format']);
                break;
            }
            
            $studentData = $data['data'];
            
            
            $stmt = $pdo->prepare("SELECT id FROM students WHERE student_id = ?");
            $stmt->execute([$studentData['student_id']]);
            
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Student ID already exists']);
                break;
            }
            
            $stmt = $pdo->prepare("INSERT INTO students (student_id, last_name, first_name, sex, course, class_mode) 
                                  VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $studentData['student_id'],
                $studentData['last_name'],
                $studentData['first_name'],
                $studentData['sex'],
                $studentData['course'],
                $studentData['class_mode']
            ]);
            
            echo json_encode(['success' => true, 'message' => 'Student added successfully']);
            break;
            
        case 'delete':
            $id = json_decode(file_get_contents('php://input'), true)['id'];
            
            $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
            $stmt->execute([$id]);
            
            echo json_encode(['success' => true, 'message' => 'Student deleted successfully']);
            break;
        
        default:
            echo json_encode(['error' => 'Invalid action']);

        case 'update':
    if (!isset($data['data'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid data format']);
        break;
    }
    
    $studentData = $data['data'];
    
    
    $stmt = $pdo->prepare("SELECT id FROM students WHERE student_id = ? AND id != ?");
    $stmt->execute([$studentData['student_id'], $studentData['id']]);
    
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Student ID already exists']);
        break;
    }
    
    $stmt = $pdo->prepare("UPDATE students SET 
                          student_id = ?, 
                          last_name = ?, 
                          first_name = ?, 
                          sex = ?, 
                          course = ?, 
                          class_mode = ? 
                          WHERE id = ?");
    $stmt->execute([
        $studentData['student_id'],
        $studentData['last_name'],
        $studentData['first_name'],
        $studentData['sex'],
        $studentData['course'],
        $studentData['class_mode'],
        $studentData['id']
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Student updated successfully']);
    break;
    }
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>