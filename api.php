<?php
require 'config.php';

$pdo->exec("
  CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  )
");

// Parse the request
$method = $_SERVER['REQUEST_METHOD'];
$path   = isset($_SERVER['PATH_INFO'])
          ? trim($_SERVER['PATH_INFO'], '/')
          : '';
$segments = explode('/', $path);

// Helper: send JSON + status code
function respond($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data);
    exit;
}

// Read input for POST/PUT
$input = json_decode(file_get_contents('php://input'), true);

// Routing
if ($segments[0] !== 'posts') {
    respond(["error" => "Not Found"], 404);
}

$id = isset($segments[1]) ? (int)$segments[1] : null;

switch ($method) {
    case 'GET':
        if ($id) {
            // GET /posts/{id}
            $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
            $stmt->execute([$id]);
            $post = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($post) {
                respond($post);
            } else {
                respond(["error" => "Post not found"], 404);
            }
        } else {
            // GET /posts
            $stmt = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC");
            respond($stmt->fetchAll(PDO::FETCH_ASSOC));
        }
        break;

    case 'POST':
        // POST /posts
        if (!isset($input['title'], $input['content'])) {
            respond(["error" => "Missing title or content"], 400);
        }
        $stmt = $pdo->prepare("
            INSERT INTO posts (title, content) 
            VALUES (:title, :content)
        ");
        $stmt->execute([
            ':title'   => filter_var($input['title'], FILTER_SANITIZE_STRING),
            ':content' => filter_var($input['content'], FILTER_SANITIZE_STRING),
        ]);
        $newId = $pdo->lastInsertId();
        respond(["message" => "Post created", "id" => $newId], 201);
        break;

    case 'PUT':
        // PUT /posts/{id}
        if (!$id) {
            respond(["error" => "Post ID required"], 400);
        }
        // Check exists
        $check = $pdo->prepare("SELECT 1 FROM posts WHERE id = ?");
        $check->execute([$id]);
        if (!$check->fetch()) {
            respond(["error" => "Post not found"], 404);
        }
        // Require fields
        if (!isset($input['title'], $input['content'])) {
            respond(["error" => "Missing title or content"], 400);
        }
        $stmt = $pdo->prepare("
            UPDATE posts 
               SET title = :title, content = :content 
             WHERE id = :id
        ");
        $stmt->execute([
            ':title'   => filter_var($input['title'], FILTER_SANITIZE_STRING),
            ':content' => filter_var($input['content'], FILTER_SANITIZE_STRING),
            ':id'      => $id,
        ]);
        respond(["message" => "Post updated"]);
        break;

    case 'DELETE':
        // DELETE /posts/{id}
        if (!$id) {
            respond(["error" => "Post ID required"], 400);
        }
        $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
        $stmt->execute([$id]);
        if ($stmt->rowCount()) {
            respond(["message" => "Post deleted"]);
        } else {
            respond(["error" => "Post not found"], 404);
        }
        break;

    default:
        respond(["error" => "Method Not Allowed"], 405);
}
