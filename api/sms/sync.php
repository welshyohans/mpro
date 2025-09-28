<?php
// Set the content type of the response to JSON
header('Content-Type: application/json');
error_reporting(E_ALL); // Report all errors during development
ini_set('display_errors', 1); // Display errors during development

// --- Configuration & Includes ---
// Adjust the path '../../' as needed based on your file structure
include_once '../../config/Database.php';
include_once '../../model/Shop.php'; // Assuming Shop model handles lastUpdateCode

// --- Input Handling ---
$jsonInput = file_get_contents('php://input');
$data = json_decode($jsonInput, true);

// Basic JSON validation
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid JSON received: ' . json_last_error_msg()
    ]);
    exit;
}

if ($data === null || !isset($data['admin']) || !isset($data['ordersWithGoodsAndPayments'])) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing required data (admin or ordersWithGoodsAndPayments).'
    ]);
    exit;
}

// --- Extract Data ---
$admin = $data['admin'];
$ordersWithGoodsAndPayments = $data['ordersWithGoodsAndPayments'];
$adminId = $admin['adminId'];
$shopId = $admin['shopId'];
$clientLastUpdateCode = $admin['lastUpdateCode']; // We might use this later for more complex sync checks

// --- Database Connection ---
$database = new Database();
$db = $database->connect();
if (!$db) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection failed.'
    ]);
    exit;
}

// --- Initialize Shop Model & Update Code ---
// We get the code *before* the transaction, but update it *after* successful commit
$shop = new Shop($db);
$serverLastUpdateCode = $shop->getLastUpdateCode($shopId);
if ($serverLastUpdateCode === false) {
     http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to retrieve server last update code.'
    ]);
    exit;
}
$newServerLastUpdateCode = $serverLastUpdateCode + 1;

// --- Main Processing Logic ---
$processedOrders = [];
$errors = [];
$newIdMappings = []; // Store mapping from client temporary ID to new server ID

// Use a transaction
$db->beginTransaction();

try {
    // --- Process Orders ---
    foreach ($ordersWithGoodsAndPayments as $orderData) {
        // Extract order, items, payments
        $clientOrder = $orderData['transaction'];
        $clientOrderItems = $orderData['goodsList'];
        $clientPayments = $orderData['paymentList'];

        // Filter: Only process orders where client lastUpdateCode is 0 (as per requirement)
        if ($clientOrder['lastUpdateCode'] !== 0) {
             // Optionally log or track skipped orders
             continue;
        }


        $clientOrderId = $clientOrder['id'];
        $clientOrderStatus = $clientOrder['status']; // 'saved' or 'draft'
        $clientOrderAction = $clientOrder['action']; // 'selling' or 'buying'

        // Convert ms timestamp to SQL DATETIME format (YYYY-MM-DD HH:MM:SS)
        $orderDate = date('Y-m-d H:i:s', $clientOrder['date'] / 1000);

        // --- Determine Scenario ---
        $isNewOrder = $clientOrderId > 800000;

        if ($isNewOrder) {
            // ***** Scenario 1 & 2: New Order (ID > 800000) *****
            $performCalculations = ($clientOrderStatus === 'saved');

            // 1. Insert Order
            $sqlOrder = "INSERT INTO h_orders (userId, orderDate, totalAmount, comment, shop_id, admin_id, status, last_update_code, is_deleted, client_order_id)
                         VALUES (:userId, :orderDate, :totalAmount, :comment, :shop_id, :admin_id, :status, :luc, 0, :client_order_id)";
            $stmtOrder = $db->prepare($sqlOrder);
            $stmtOrder->bindParam(':userId', $clientOrder['userId']);
            $stmtOrder->bindParam(':orderDate', $orderDate);
            $stmtOrder->bindParam(':totalAmount', $clientOrder['total']);
            $stmtOrder->bindParam(':comment', $clientOrder['comment']);
            $stmtOrder->bindParam(':shop_id', $shopId);
            $stmtOrder->bindParam(':admin_id', $adminId);
            $stmtOrder->bindParam(':status', $clientOrderStatus); // Save client status
            $stmtOrder->bindParam(':luc', $newServerLastUpdateCode);
            $stmtOrder->bindParam(':client_order_id', $clientOrderId); // Store client ID

            if (!$stmtOrder->execute()) {
                 throw new Exception("Failed to insert new order (Client ID: $clientOrderId). Error: " . implode(", ", $stmtOrder->errorInfo()));
            }
            $serverOrderId = $db->lastInsertId();
            $newIdMappings[$clientOrderId] = $serverOrderId; // Map client ID to server ID

            // 2. Insert Order Items
            foreach ($clientOrderItems as $item) {
                 // Filter: Only process items where client lastUpdateCode is 0
                if ($item['lastUpdateCode'] !== 0) continue;

                $itemDate = date('Y-m-d H:i:s', $item['date'] / 1000);
                $sqlItem = "INSERT INTO h_order_items (orderId, goodsId, quantity, priceAtOrder, last_update_code, is_deleted)
                            VALUES (:orderId, :goodsId, :quantity, :priceAtOrder, :luc, 0)";
                $stmtItem = $db->prepare($sqlItem);
                $stmtItem->bindParam(':orderId', $serverOrderId); // Use the NEW server order ID
                $stmtItem->bindParam(':goodsId', $item['goodsId']);
                $stmtItem->bindParam(':quantity', $item['quantity']);
                $stmtItem->bindParam(':priceAtOrder', $item['price']);
                $stmtItem->bindParam(':luc', $newServerLastUpdateCode);

                if (!$stmtItem->execute()) {
                    throw new Exception("Failed to insert order item for new order $serverOrderId (Client Goods ID: {$item['id']}). Error: " . implode(", ", $stmtItem->errorInfo()));
                }

                // 3. Update Stock (only if status is 'saved')
                if ($performCalculations) {
                    updateStock($db, $item['goodsId'], $item['quantity'], $clientOrderAction, $newServerLastUpdateCode);
                }
            }

            // 4. Insert Payments
            $totalPaid = 0;
            foreach ($clientPayments as $payment) {
                // Filter: Only process payments where client lastUpdateCode is 0
                if ($payment['lastUpdateCode'] !== 0) continue;

                $paymentDate = date('Y-m-d H:i:s', $payment['date'] / 1000);
                $sqlPayment = "INSERT INTO h_payments (orderId, paymentMethod, amountPaid, paymentDate, last_update_code, is_deleted)
                               VALUES (:orderId, :paymentMethod, :amountPaid, :paymentDate, :luc, 0)";
                $stmtPayment = $db->prepare($sqlPayment);
                $stmtPayment->bindParam(':orderId', $serverOrderId); // Use the NEW server order ID
                $stmtPayment->bindParam(':paymentMethod', $payment['paymentMethod']);
                $stmtPayment->bindParam(':amountPaid', $payment['paidAmount']);
                $stmtPayment->bindParam(':paymentDate', $paymentDate);
                $stmtPayment->bindParam(':luc', $newServerLastUpdateCode);

                if (!$stmtPayment->execute()) {
                     throw new Exception("Failed to insert payment for new order $serverOrderId (Client Payment ID: {$payment['id']}). Error: " . implode(", ", $stmtPayment->errorInfo()));
                }
                $totalPaid += $payment['paidAmount'];
            }

            // 5. Update User Credit (only if status is 'saved')
            if ($performCalculations) {
                updateUserCredit($db, $clientOrder['userId'], $clientOrder['total'], $totalPaid, $clientOrderAction);
            }

            $processedOrders[] = $serverOrderId; // Add the new server ID

        } else {
            // ***** Scenario 3 & 4: Existing Order (ID < 800000) *****
            $serverOrderId = $clientOrderId; // Server ID is the same as client ID
            $performCalculations = ($clientOrderStatus === 'saved');

            // 1. Fetch existing order details (especially status and old items/payments if needed)
            $existingOrder = fetchOrder($db, $serverOrderId);
            if (!$existingOrder) {
                // Order exists on client but not server? This might be an error condition
                // or requires specific handling. For now, we'll treat it like a new order insert,
                // but this could indicate a sync issue.
                // Let's log an error and skip for now, or you could adapt the 'new order' logic here.
                 $errors[] = "Order with ID $serverOrderId exists on client but not found on server. Skipping.";
                 continue; // Skip this order
                 // Alternatively, handle as a completely new insert if that's desired:
                 // throw new Exception("Order ID $serverOrderId not found on server.");
            }
            $existingOrderStatus = $existingOrder['status'];
            $existingOrderAction = $existingOrder['action']; // You might need the old action

            // Determine if calculations need reversal/application
            $applyNewCalculations = $performCalculations;
            $reverseOldCalculations = ($existingOrderStatus === 'saved'); // Reverse if it was previously saved

             // Fetch OLD items and payments to reverse their effects if needed
             $oldItems = [];
             $oldPayments = [];
             if ($reverseOldCalculations) {
                 $oldItems = fetchOrderItems($db, $serverOrderId);
                 $oldPayments = fetchPayments($db, $serverOrderId);
             }

            // --- Perform Reversals (if applicable) ---
            if ($reverseOldCalculations) {
                // Reverse stock changes from OLD items
                foreach ($oldItems as $oldItem) {
                    // Reverse action: selling becomes buying, buying becomes selling
                    $reverseAction = ($existingOrderAction === 'selling') ? 'buying' : 'selling';
                    // Use $existingOrderAction here to determine the *original* effect
                    updateStock($db, $oldItem['goodsId'], $oldItem['quantity'], $reverseAction, $newServerLastUpdateCode, true); // isReversal = true (don't check stock on reversal)
                }

                 // Reverse credit changes from OLD order/payments
                 $oldTotalPaid = 0;
                 foreach ($oldPayments as $oldPayment) {
                    $oldTotalPaid += $oldPayment['amountPaid'];
                 }
                 // Use $existingOrderAction here
                 updateUserCredit($db, $existingOrder['userId'], $existingOrder['totalAmount'], $oldTotalPaid, $existingOrderAction, true); // isReversal = true
            }


            // 2. Logically Delete Old Items and Payments for this order
            // We always replace items/payments when an existing order is synced
             markItemsDeleted($db, $serverOrderId, $newServerLastUpdateCode);
             markPaymentsDeleted($db, $serverOrderId, $newServerLastUpdateCode);

            // 3. Update Order Header
            $sqlOrderUpdate = "UPDATE h_orders SET
                                userId = :userId,
                                orderDate = :orderDate,
                                totalAmount = :totalAmount,
                                comment = :comment,
                                -- shop_id = :shop_id, -- shop id likely doesn't change
                                -- admin_id = :admin_id, -- admin might change if edited by someone else
                                status = :status,
                                last_update_code = :luc,
                                is_deleted = 0 -- Ensure it's not marked deleted
                               WHERE orderId = :orderId";
            $stmtOrderUpdate = $db->prepare($sqlOrderUpdate);
            $stmtOrderUpdate->bindParam(':userId', $clientOrder['userId']);
            $stmtOrderUpdate->bindParam(':orderDate', $orderDate);
            $stmtOrderUpdate->bindParam(':totalAmount', $clientOrder['total']);
            $stmtOrderUpdate->bindParam(':comment', $clientOrder['comment']);
            $stmtOrderUpdate->bindParam(':status', $clientOrderStatus); // Update to the client's status
            $stmtOrderUpdate->bindParam(':luc', $newServerLastUpdateCode);
            $stmtOrderUpdate->bindParam(':orderId', $serverOrderId);

            if (!$stmtOrderUpdate->execute()) {
                 throw new Exception("Failed to update order header for order $serverOrderId. Error: " . implode(", ", $stmtOrderUpdate->errorInfo()));
            }

            // 4. Insert NEW Order Items
            foreach ($clientOrderItems as $item) {
                // Filter: Only process items where client lastUpdateCode is 0
                if ($item['lastUpdateCode'] !== 0) continue;

                $itemDate = date('Y-m-d H:i:s', $item['date'] / 1000);
                // Use the same insert logic as for new orders
                $sqlItem = "INSERT INTO h_order_items (orderId, goodsId, quantity, priceAtOrder, last_update_code, is_deleted)
                            VALUES (:orderId, :goodsId, :quantity, :priceAtOrder, :luc, 0)"; // Ensure is_deleted is 0
                $stmtItem = $db->prepare($sqlItem);
                $stmtItem->bindParam(':orderId', $serverOrderId);
                $stmtItem->bindParam(':goodsId', $item['goodsId']);
                $stmtItem->bindParam(':quantity', $item['quantity']);
                $stmtItem->bindParam(':priceAtOrder', $item['price']);
                $stmtItem->bindParam(':luc', $newServerLastUpdateCode);

                if (!$stmtItem->execute()) {
                    throw new Exception("Failed to insert NEW order item for existing order $serverOrderId (Client Goods ID: {$item['id']}). Error: " . implode(", ", $stmtItem->errorInfo()));
                }

                // 5. Apply NEW Stock Calculations (if applicable)
                if ($applyNewCalculations) {
                    // Use $clientOrderAction for the current action
                    updateStock($db, $item['goodsId'], $item['quantity'], $clientOrderAction, $newServerLastUpdateCode);
                }
            }

            // 6. Insert NEW Payments
            $newTotalPaid = 0;
            foreach ($clientPayments as $payment) {
                // Filter: Only process payments where client lastUpdateCode is 0
                if ($payment['lastUpdateCode'] !== 0) continue;

                 $paymentDate = date('Y-m-d H:i:s', $payment['date'] / 1000);
                 // Use the same insert logic as for new orders
                 $sqlPayment = "INSERT INTO h_payments (orderId, paymentMethod, amountPaid, paymentDate, last_update_code, is_deleted)
                                VALUES (:orderId, :paymentMethod, :amountPaid, :paymentDate, :luc, 0)"; // Ensure is_deleted is 0
                 $stmtPayment = $db->prepare($sqlPayment);
                 $stmtPayment->bindParam(':orderId', $serverOrderId);
                 $stmtPayment->bindParam(':paymentMethod', $payment['paymentMethod']);
                 $stmtPayment->bindParam(':amountPaid', $payment['paidAmount']);
                 $stmtPayment->bindParam(':paymentDate', $paymentDate);
                 $stmtPayment->bindParam(':luc', $newServerLastUpdateCode);

                 if (!$stmtPayment->execute()) {
                     throw new Exception("Failed to insert NEW payment for existing order $serverOrderId (Client Payment ID: {$payment['id']}). Error: " . implode(", ", $stmtPayment->errorInfo()));
                 }
                 $newTotalPaid += $payment['paidAmount'];
            }

            // 7. Apply NEW User Credit Calculations (if applicable)
             if ($applyNewCalculations) {
                 // Use $clientOrderAction for the current action
                 updateUserCredit($db, $clientOrder['userId'], $clientOrder['total'], $newTotalPaid, $clientOrderAction);
             }

            $processedOrders[] = $serverOrderId;
        } // End of existing order handling
    } // End of foreach loop for orders

    // --- Final Steps ---

    // If all orders processed without throwing an exception, update the shop's last update code
    $shop->updateLastUpdateCode($shopId, $newServerLastUpdateCode);

    // Commit the transaction
    $db->commit();

    // --- Success Response ---
    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'message' => 'Data synchronized successfully.',
        'processed_order_ids' => $processedOrders,
        'new_order_id_mappings' => $newIdMappings, // Send mapping back to client if needed
        'new_server_last_update_code' => $newServerLastUpdateCode,
        'errors' => $errors // Report non-fatal errors
    ]);

} catch (Exception $e) {
    // --- Error Handling ---
    // An error occurred, rollback the transaction
    $db->rollBack();

    http_response_code(500); // Internal Server Error
    echo json_encode([
        'status' => 'error',
        'message' => 'Synchronization failed: ' . $e->getMessage(),
        'processed_order_ids' => $processedOrders, // Show which ones might have been processed before failure
         'errors' => array_merge($errors, [$e->getMessage()]) // Combine loop errors with the exception
    ]);
}

// ========================================
// --- Helper Functions ---
// ========================================

/**
 * Fetches a single order record.
 * Returns order data as an associative array or false if not found.
 */
function fetchOrder($db, $orderId) {
    // Added action, userId, totalAmount for reversal logic
    $sql = "SELECT orderId, status, action, userId, totalAmount FROM h_orders WHERE orderId = :orderId AND is_deleted = 0";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':orderId', $orderId);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Fetches all non-deleted items for a given order.
 * Returns an array of item associative arrays.
 */
 function fetchOrderItems($db, $orderId) {
     $sql = "SELECT goodsId, quantity FROM h_order_items WHERE orderId = :orderId AND is_deleted = 0";
     $stmt = $db->prepare($sql);
     $stmt->bindParam(':orderId', $orderId);
     $stmt->execute();
     return $stmt->fetchAll(PDO::FETCH_ASSOC);
 }

 /**
 * Fetches all non-deleted payments for a given order.
 * Returns an array of payment associative arrays.
 */
 function fetchPayments($db, $orderId) {
    $sql = "SELECT amountPaid FROM h_payments WHERE orderId = :orderId AND is_deleted = 0";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':orderId', $orderId);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
 }


/**
 * Marks existing order items as deleted.
 */
function markItemsDeleted($db, $orderId, $luc) {
    $sql = "UPDATE h_order_items SET is_deleted = 1, last_update_code = :luc WHERE orderId = :orderId AND is_deleted = 0";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':luc', $luc);
    $stmt->bindParam(':orderId', $orderId);
    if (!$stmt->execute()) {
        throw new Exception("Failed to mark old items as deleted for order $orderId. Error: " . implode(", ", $stmt->errorInfo()));
    }
}

/**
 * Marks existing payments as deleted.
 */
function markPaymentsDeleted($db, $orderId, $luc) {
    $sql = "UPDATE h_payments SET is_deleted = 1, last_update_code = :luc WHERE orderId = :orderId AND is_deleted = 0";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':luc', $luc);
    $stmt->bindParam(':orderId', $orderId);
     if (!$stmt->execute()) {
        throw new Exception("Failed to mark old payments as deleted for order $orderId. Error: " . implode(", ", $stmt->errorInfo()));
    }
}


/**
 * Updates the stock quantity for a given goods item.
 * Throws an exception if stock is insufficient during selling.
 * @param bool $isReversal If true, bypasses the stock check (used when undoing a previous operation).
 */
function updateStock($db, $goodsId, $quantityChange, $action, $luc, $isReversal = false) {
    // Determine the change based on action
    $change = ($action === 'selling') ? -$quantityChange : +$quantityChange;

    // Get current quantity *if* selling and not reversing
    if ($action === 'selling' && !$isReversal) {
        $sqlCheck = "SELECT quantity FROM h_goods WHERE goodsId = :goodsId";
        $stmtCheck = $db->prepare($sqlCheck);
        $stmtCheck->bindParam(':goodsId', $goodsId);
        $stmtCheck->execute();
        $currentStock = $stmtCheck->fetchColumn();

        if ($currentStock === false) {
            throw new Exception("Goods item with ID $goodsId not found for stock update.");
        }
        if ($currentStock < $quantityChange) {
            throw new Exception("Insufficient stock for goods ID $goodsId. Required: $quantityChange, Available: $currentStock.");
        }
    }

    // Update the stock
    $sql = "UPDATE h_goods SET quantity = quantity + :change, last_update_code = :luc WHERE goodsId = :goodsId";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':change', $change, PDO::PARAM_INT); // Assuming quantity is integer
    $stmt->bindParam(':luc', $luc);
    $stmt->bindParam(':goodsId', $goodsId);

    if (!$stmt->execute()) {
        throw new Exception("Failed to update stock for goods ID $goodsId. Error: " . implode(", ", $stmt->errorInfo()));
    }
     // Check if any row was actually updated, could indicate goodsId doesn't exist if not checking before
    if ($stmt->rowCount() === 0 && !$isReversal) {
         // Log warning or potentially throw error if goodsId *should* always exist
         // For reversal, it might be okay if the item was deleted previously.
         error_log("Warning: Stock update affected 0 rows for goodsId: $goodsId. It might not exist.");
         // throw new Exception("Goods item with ID $goodsId not found during stock update attempt.");
    }
}

/**
 * Updates the user's credit balance.
 * @param bool $isReversal If true, reverses the effect of the parameters.
 */
function updateUserCredit($db, $userId, $orderTotal, $totalPaid, $action, $isReversal = false) {
    // Calculate the net change in credit for the user from this order
    // Selling: Increases user's debt (decreases credit) by (orderTotal - totalPaid)
    // Buying: Decreases user's debt (increases credit) by (orderTotal - totalPaid) -> Or maybe buying always reduces debt by totalPaid? Assuming buying reduces debt by orderTotal. Check your business logic.
    // Let's assume 'credit' represents how much the *shop* owes the user (positive) or the user owes the *shop* (negative).

    // Effect = orderTotal - totalPaid; This is the amount outstanding on this order.
    $outstandingAmount = $orderTotal - $totalPaid;
    $creditChange = 0;

    if ($action === 'selling') {
        // User owes more, so credit balance decreases (or becomes more negative)
        $creditChange = -$outstandingAmount;
    } elseif ($action === 'buying') {
        // User owes less (shop owes user more), so credit balance increases
        $creditChange = +$outstandingAmount; // Or adjust based on exact definition of 'buying' credit impact
    }

    // If it's a reversal, flip the sign of the change
    if ($isReversal) {
        $creditChange = -$creditChange;
    }

    // Apply the change
    $sql = "UPDATE h_user SET credit = credit + :creditChange WHERE userId = :userId";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':creditChange', $creditChange); // PDO handles numeric types
    $stmt->bindParam(':userId', $userId);

    if (!$stmt->execute()) {
        throw new Exception("Failed to update credit for user ID $userId. Error: " . implode(", ", $stmt->errorInfo()));
    }
     // Check if user exists / was updated
    if ($stmt->rowCount() === 0) {
         // Log warning or throw error if user should always exist
         error_log("Warning: User credit update affected 0 rows for userId: $userId. User might not exist.");
         // throw new Exception("User with ID $userId not found during credit update attempt.");
    }
}

?>