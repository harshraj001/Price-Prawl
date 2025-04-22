<?php
/**
 * PricePrawl Search Processor
 * Handles URLs submitted via POST from the url_processor.php file
 */

// Check if we received data via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['processed_url'])) {
    $url = trim($_POST['processed_url']);
    
    if (empty($url)) {
        header('Location: index.php?error=empty_url');
        exit;
    }
    
    // Import the functions from search.php
    require_once 'search_functions.php';
    
    // Process the URL
    $pos = determinePosFromUrl($url);
    $pid = extractPidByPos($pos, $url);
    
    // If product ID is found and POS is valid, redirect to product page
    if (!empty($pid) && !empty($pos)) {
        header("Location: product.php?url=" . urlencode($url) . "&pid=" . urlencode($pid) . "&pos=" . urlencode($pos));
        exit;
    }
    
    // If we couldn't extract the information but have a valid retailer, process as search query
    if (!empty($pos)) {
        header("Location: product.php?url=" . urlencode($url) . "&pid=unknown&pos=" . urlencode($pos));
        exit;
    }
    
    // If all else fails, show a generic error
    header('Location: index.php?error=processing_failed&message=Could+not+process+this+link+format');
    exit;
} else {
    // If not a POST request, redirect to home
    header('Location: index.php');
    exit;
}
?>
