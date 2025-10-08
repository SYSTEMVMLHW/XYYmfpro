<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// 加载数据库配置
$config = require_once 'config.php';

try {
    // 连接数据库
    $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset=utf8mb4";
    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '数据库连接失败: ' . $e->getMessage()]);
    exit;
}

$action = $_GET['action'] ?? '';
session_start();

switch ($action) {
    case 'get':
        getSidebarData($pdo);
        break;
    case 'save':
        saveSidebarData($pdo);
        break;
    case 'login':
        adminLogin($pdo);
        break;
    case 'logout':
        adminLogout();
        break;
    case 'me':
        whoAmI();
        break;
    case 'getSettings':
        getSettings($pdo);
        break;
    case 'saveSettings':
        saveSettings($pdo);
        break;
    case 'listNotifications':
        listNotifications($pdo);
        break;
    case 'saveNotification':
        saveNotification($pdo);
        break;
    case 'deleteNotification':
        deleteNotification($pdo);
        break;
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => '无效的操作']);
}

function getSidebarData($pdo) {
    try {
        $stmt = $pdo->query("
            SELECT * FROM sidebar_config 
            WHERE is_active = 1 
            ORDER BY parent_id ASC, sort_order ASC, id ASC
        ");
        
        $data = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'data' => $data
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => '获取数据失败: ' . $e->getMessage()
        ]);
    }
}

function saveSidebarData($pdo) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }
    
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input || !isset($input['data'])) {
            throw new Exception('Invalid input data');
        }
        
        $pdo->beginTransaction();
        
        // 清空现有数据
        $pdo->exec("DELETE FROM sidebar_config");
        
        // 分两步插入，保证按钮可以正确关联到父级分组/下拉分组
        $insert = $pdo->prepare("INSERT INTO sidebar_config (name, type, title, icon, url, parent_id, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $groupNameToId = [];
        
        // 第一步：先插入分组/下拉分组，并记录 name/title 到 id 的映射
        foreach ($input['data'] as $item) {
            $type = $item['type'] ?? 'button';
            if ($type === 'group' || $type === 'dropdown') {
                $insert->execute([
                    $item['name'] ?? '',
                    $type,
                    $item['title'] ?? ($item['name'] ?? ''),
                    $item['icon'] ?? '',
                    $item['url'] ?? '',
                    null,
                    $item['sort_order'] ?? 0,
                    $item['is_active'] ?? 1
                ]);
                $newId = (int)$pdo->lastInsertId();
                if (!empty($item['name'])) $groupNameToId['name:'.$item['name']] = $newId;
                if (!empty($item['title'])) $groupNameToId['title:'.$item['title']] = $newId;
            }
        }
        
        // 第二步：插入按钮，解析 parent_id（支持数字 id 或 传 name/title）
        foreach ($input['data'] as $item) {
            $type = $item['type'] ?? 'button';
            if ($type !== 'button') continue;
            $parentId = null;
            if (isset($item['parent_id']) && $item['parent_id'] !== '' && $item['parent_id'] !== null) {
                if (is_numeric($item['parent_id'])) {
                    $parentId = (int)$item['parent_id'];
                } else {
                    $key1 = 'name:'.$item['parent_id'];
                    $key2 = 'title:'.$item['parent_id'];
                    if (isset($groupNameToId[$key1])) $parentId = $groupNameToId[$key1];
                    if ($parentId === null && isset($groupNameToId[$key2])) $parentId = $groupNameToId[$key2];
                }
            }
            $insert->execute([
                $item['name'] ?? '',
                'button',
                $item['title'] ?? ($item['name'] ?? ''),
                $item['icon'] ?? '',
                $item['url'] ?? '',
                $parentId,
                $item['sort_order'] ?? 0,
                $item['is_active'] ?? 1
            ]);
        }
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => '数据保存成功'
        ]);
    } catch (Exception $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => '保存数据失败: ' . $e->getMessage()
        ]);
    }
}

// ========== 管理登录 ==========
function adminLogin($pdo) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }
    $input = json_decode(file_get_contents('php://input'), true) ?: [];
    $password = $input['password'] ?? '';
    try {
        $row = $pdo->query('SELECT admin_password_hash FROM admin_settings ORDER BY id ASC LIMIT 1')->fetch();
        if (!$row) throw new Exception('未初始化管理员密码');
        if (!password_verify($password, $row['admin_password_hash'])) {
            echo json_encode(['success' => false, 'message' => '密码错误']);
            return;
        }
        $_SESSION['admin'] = true;
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function adminLogout() {
    session_destroy();
    echo json_encode(['success' => true]);
}

function whoAmI() {
    echo json_encode(['success' => true, 'data' => ['admin' => !empty($_SESSION['admin'])]]);
}

// ========== 设置（广告URL与修改密码） ==========
function getSettings($pdo) {
    try {
        $row = $pdo->query('SELECT ad_url FROM admin_settings ORDER BY id ASC LIMIT 1')->fetch();
        echo json_encode(['success' => true, 'data' => $row ?: ['ad_url' => null]]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function saveSettings($pdo) {
    if (empty($_SESSION['admin'])) { http_response_code(403); echo json_encode(['success'=>false,'message'=>'未登录']); return; }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['success'=>false,'message'=>'Method not allowed']); return; }
    $input = json_decode(file_get_contents('php://input'), true) ?: [];
    $adUrl = $input['ad_url'] ?? null;
    $newPwd = $input['new_password'] ?? null;
    try {
        $row = $pdo->query('SELECT id FROM admin_settings ORDER BY id ASC LIMIT 1')->fetch();
        if ($row) {
            $id = intval($row['id']);
            if ($newPwd) {
                $hash = password_hash($newPwd, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('UPDATE admin_settings SET admin_password_hash=?, ad_url=? WHERE id=?');
                $stmt->execute([$hash, $adUrl, $id]);
            } else {
                $stmt = $pdo->prepare('UPDATE admin_settings SET ad_url=? WHERE id=?');
                $stmt->execute([$adUrl, $id]);
            }
        } else {
            $hash = password_hash($newPwd ?: 'XYYadmin', PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO admin_settings (admin_password_hash, ad_url) VALUES (?, ?)');
            $stmt->execute([$hash, $adUrl]);
        }
        echo json_encode(['success'=>true]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
    }
}

// ========== 公告 ==========
function listNotifications($pdo) {
    try {
        $rows = $pdo->query('SELECT * FROM notifications ORDER BY id DESC')->fetchAll();
        echo json_encode(['success'=>true,'data'=>$rows]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
    }
}

function saveNotification($pdo) {
    if (empty($_SESSION['admin'])) { http_response_code(403); echo json_encode(['success'=>false,'message'=>'未登录']); return; }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['success'=>false,'message'=>'Method not allowed']); return; }
    $input = json_decode(file_get_contents('php://input'), true) ?: [];
    $id = isset($input['id']) ? intval($input['id']) : 0;
    $title = trim($input['title'] ?? '');
    $message = trim($input['message'] ?? '');
    $isActive = isset($input['is_active']) ? intval($input['is_active']) : 1;
    try {
        if ($id > 0) {
            $stmt = $pdo->prepare('UPDATE notifications SET title=?, message=?, is_active=? WHERE id=?');
            $stmt->execute([$title, $message, $isActive, $id]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO notifications (title, message, is_active) VALUES (?, ?, ?)');
            $stmt->execute([$title, $message, $isActive]);
        }
        echo json_encode(['success'=>true]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
    }
}

function deleteNotification($pdo) {
    if (empty($_SESSION['admin'])) { http_response_code(403); echo json_encode(['success'=>false,'message'=>'未登录']); return; }
    if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') { http_response_code(405); echo json_encode(['success'=>false,'message'=>'Method not allowed']); return; }
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    try {
        $stmt = $pdo->prepare('DELETE FROM notifications WHERE id=?');
        $stmt->execute([$id]);
        echo json_encode(['success'=>true]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
    }
}
?>
