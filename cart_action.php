<?php
require_once 'includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    
    // Initialize cart if not exists
    if(!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }
    
    $action = $_POST['action'];
    
    if ($action == 'add') {
        $food_id = $_POST['food_id'];
        $qty = (int)$_POST['qty'];
        
        // Fetch food details
        $stmt = $conn->prepare("SELECT id, title, price, image_name FROM foods WHERE id = ?");
        if($stmt) {
            $stmt->bind_param("i", $food_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if($row = $result->fetch_assoc()) {
                
                // Check if already in cart
                $found = false;
                foreach($_SESSION['cart'] as &$item) {
                    if($item['id'] == $food_id) {
                        $item['qty'] += $qty;
                        $found = true;
                        break;
                    }
                }
                
                if(!$found) {
                    $_SESSION['cart'][] = array(
                        'id' => $row['id'],
                        'title' => $row['title'],
                        'price' => $row['price'],
                        'image' => $row['image_name'],
                        'qty' => $qty
                    );
                }
                
                $_SESSION['message'] = $row['title'] . " added to cart successfully!";
            }
            $stmt->close();
        }
        
        // Redirect back
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }
    
    if ($action == 'update') {
        $food_id = $_POST['food_id'];
        $qty = (int)$_POST['qty'];
        
        foreach($_SESSION['cart'] as &$item) {
            if($item['id'] == $food_id) {
                if($qty > 0) {
                    $item['qty'] = $qty;
                }
                break;
            }
        }
        header("Location: cart.php");
        exit;
    }
    
    if ($action == 'remove') {
        $food_id = $_POST['food_id'];
        foreach($_SESSION['cart'] as $key => $item) {
            if($item['id'] == $food_id) {
                unset($_SESSION['cart'][$key]);
                break;
            }
        }
        // Re-index array
        $_SESSION['cart'] = array_values($_SESSION['cart']);
        $_SESSION['message'] = "Item removed from cart.";
        header("Location: cart.php");
        exit;
    }
    
    if ($action == 'clear') {
        unset($_SESSION['cart']);
        $_SESSION['message'] = "Cart cleared.";
        header("Location: cart.php");
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}
?>
