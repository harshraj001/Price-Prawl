<?php
/**
 * PricePrawl Search Processing Page
 * Handles URL validation and redirects accordingly
 */

// Store search query
$query = isset($_GET['query']) ? trim($_GET['query']) : '';
if (empty($query)) {
    // Try mobile query if main query is empty
    $query = isset($_GET['query_mobile']) ? trim($_GET['query_mobile']) : '';
}

// If still empty, redirect back with error
if (empty($query)) {
    header('Location: index.php?error=empty_search');
    exit;
}

// Log search activity if user is logged in
session_start();
if (isset($_SESSION['user_id'])) {
    require_once 'includes/activity_logger.php';
    logSearchActivity($_SESSION['user_id'], $query, [
        'search_time' => date('Y-m-d H:i:s'),
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);
}

// URL validation helper functions
/**
 * Checks if a string contains a substring.
 * @param string $mainStr The main string.
 * @param string $subStr The substring to check for.
 * @return bool True if mainStr includes subStr.
 */
function includesString($mainStr, $subStr) {
    return strpos($mainStr, $subStr) !== false;
}

/**
 * Checks if a string can be converted to a number.
 * @param string $value The string to check.
 * @return bool True if the string is numeric.
 */
function isNumericString($value) {
    return is_numeric($value);
}

/**
 * Splits a string by a delimiter and returns a specific part.
 * @param string $str The string to split.
 * @param string $delimiter The delimiter to split by.
 * @param int $index The index of the part to return (negative index counts from the end).
 * @return string The extracted part or the original string if delimiter not found.
 */
function splitAndGetValue($str, $delimiter, $index) {
    $result = $str;
    if (includesString($str, $delimiter)) {
        $parts = explode($delimiter, $str);
        if ($index < 0) {
            $index = count($parts) + $index;
        }
        if ($index >= 0 && $index < count($parts)) {
            $result = trim($parts[$index]);
        } else {
            $result = $str;
        }
    }
    return $result;
}

/**
 * Applies multiple split operations sequentially.
 * @param string $str The initial string.
 * @param array $operations Array of split operations.
 * @return string The result after all operations.
 */
function splitMultiple($str, $operations) {
    $currentStr = $str;
    $opLength = count($operations);
    for ($i = 0; $i < $opLength; $i++) {
        $currentStr = splitAndGetValue($currentStr, $operations[$i]['find'], $operations[$i]['val']);
    }
    return $currentStr;
}

/**
 * Splits by '/' and gets the last non-empty path component.
 * @param string $str The string (usually a path).
 * @param string $delimiter The delimiter (usually '/').
 * @return string The last non-empty component.
 */
function getLastPathComponent($str, $delimiter) {
    $result = $str;
    if (includesString($result, $delimiter)) {
        $lastPart = splitAndGetValue($result, $delimiter, -1);
        if ($lastPart === "") {
            // If the last part is empty (e.g., trailing slash), get the second to last
            $lastPart = splitAndGetValue($result, $delimiter, -2);
        }
        $result = $lastPart;
    }
    return $result;
}

/**
 * Extracts the Product ID (pid) based on the Point of Sale ID (pos) and URL.
 * @param int $pos The Point of Sale ID.
 * @param string $url The product URL.
 * @return string The extracted Product ID (pid) or an empty string if not found/applicable.
 */
function extractPidByPos($pos, $url) {
    $pid = "";
    switch ($pos) {
        case 6660: // JioMart
            {
                $r = $url;
                $r = splitMultiple($r, [['find' => "#", 'val' => 0], ['find' => "?", 'val' => 0], ['find' => "&", 'val' => 0]]);
                if (includesString($r, "/p/")) {
                    $s = explode("/p/", $r);
                    $o = count($s);
                    $r = trim($s[$o - 1]);
                } else {
                    $r = "";
                }
                $pid = $r;
                break;
            }
        case 6607: // RelianceDigital
            {
                $r = $url;
                $r = splitMultiple($r, [['find' => "#", 'val' => 0], ['find' => "?", 'val' => 0], ['find' => "&", 'val' => 0]]);
                if (includesString($r, "/p/")) {
                    $s = explode("/p/", $r);
                    $o = count($s);
                    $r = trim($s[$o - 1]);
                    if (includesString($r, "/")) {
                        $r = trim(explode("/", $r)[0]);
                    }
                } else {
                    $r = "";
                }
                $pid = $r;
                break;
            }
        case 63: // Amazon
            {
                $r = $url;
                $i = "";
                if (includesString($r, "?ASIN=")) { $r = trim(explode("?ASIN=", $r)[1]); }
                else if (includesString($r, "&ASIN=")) { $r = trim(explode("&ASIN=", $r)[1]); }
                else if (includesString($r, "/dp/")) { $r = trim(explode("/dp/", $r)[1]); }
                else if (includesString($r, "/product/")) { $r = trim(explode("/product/", $r)[1]); }
                else if (includesString($r, "/offer-listing/")) { $r = trim(explode("/offer-listing/", $r)[1]); }
                else if (includesString($r, "/gp/product/")) { $r = trim(explode("/gp/product/", $r)[1]); }
                else if (includesString($r, "/aw/d/")) { $r = trim(explode("/aw/d/", $r)[1]); }

                $r = splitAndGetValue($r, "#", 0);
                $r = splitAndGetValue($r, "?", 0);
                $r = splitAndGetValue($r, "/ref=", 0);
                if (includesString($r, "/")) {
                    $s = explode("/", $r);
                    $o = count($s);
                    $i = trim($s[0]);
                    if ($i === "" || strlen($i) < 10 || !preg_match('/^[A-Z0-9]{10}$/', $i)) {
                        $i = trim($s[$o - 1]);
                        if ($i === "") {
                            $i = isset($s[$o - 2]) ? trim($s[$o - 2]) : "";
                        }
                    }
                    if (!preg_match('/^[A-Z0-9]{10}$/', $i)) {
                        $found = false;
                        foreach ($s as $part) {
                            if (preg_match('/^[A-Z0-9]{10}$/', trim($part))) {
                                $i = trim($part);
                                $found = true;
                                break;
                            }
                        }
                        if (!$found) {
                            $i = "";
                        }
                    }
                    $r = $i;
                }
                $pid = preg_match('/^[A-Z0-9]{10}$/', $r) ? $r : "";
                break;
            }
        case 71: // Croma
            {
                $r = $url;
                $r = splitMultiple($r, [['find' => "#", 'val' => 0], ['find' => "?", 'val' => 0], ['find' => "&", 'val' => 0]]);
                $r = splitAndGetValue($r, "/p/", 1);
                if (includesString($r, "/")) {
                    $r = trim(explode("/", $r)[0]);
                }
                $pid = $r;
                break;
            }
        case 2190: // Tatacliq
            {
                $r = $url;
                $r = splitMultiple($r, [['find' => "#", 'val' => 0], ['find' => "?", 'val' => 0], ['find' => "&", 'val' => 0]]);
                if (includesString($r, "/p-")) {
                    $s = explode("/p-", $r);
                    $o = count($s);
                    $r = trim($s[$o - 1]);
                    if (includesString($r, "/")) {
                        $r = trim(explode("/", $r)[0]);
                    }
                } else {
                    $r = "";
                }
                $pid = $r;
                break;
            }
        case 2191: // Ajio
            {
                $r = $url;
                $r = splitMultiple($r, [['find' => "#", 'val' => 0], ['find' => "?", 'val' => 0], ['find' => "&", 'val' => 0]]);
                if (includesString($r, "/p/")) {
                    $s = explode("/p/", $r);
                    $o = count($s);
                    $r = trim($s[$o - 1]);
                    if (includesString($r, "/")) {
                        $r = trim(explode("/", $r)[0]);
                    }
                } else {
                    $matches = [];
                    if (preg_match('/\/pdp\/[^\/]*?-(\d+)$/', $r, $matches)) {
                        $r = $matches[1];
                    } else {
                        $r = "";
                    }
                }
                $pid = $r;
                break;
            }
        case 6031: // 2gud - Flipkart's refurbished site
        case 2:    // Flipkart
            {
                $r = $url;
                $r = splitAndGetValue($r, "#", 0);
                if (includesString($r, "?pid=")) {
                    $r = trim(explode("?pid=", $r)[1]);
                    $r = splitAndGetValue($r, "?", 0);
                    $r = splitAndGetValue($r, "/", 0);
                    $r = splitAndGetValue($r, "&", 0);
                    $pid = $r;
                } else if (includesString($r, "&pid=")) {
                    $r = trim(explode("&pid=", $r)[1]);
                    $r = splitAndGetValue($r, "?", 0);
                    $r = splitAndGetValue($r, "/", 0);
                    $r = splitAndGetValue($r, "&", 0);
                    $pid = $r;
                } else {
                    $matches = [];
                    if (preg_match('/\/p.*\/([a-zA-Z0-9]+)\?/', $r, $matches)) {
                        $pid = $matches[1];
                    } else {
                        $matches = [];
                        if (preg_match('/[?&]pid=([a-zA-Z0-9]+)/', $r, $matches)) {
                            $pid = $matches[1];
                        } else {
                            $pid = "";
                        }
                    }
                }
                break;
            }
        case 111: // Myntra
            {
                $r = $url;
                $r = splitMultiple($r, [['find' => "?", 'val' => 0], ['find' => "#", 'val' => 0]]);
                if (includesString($r, "myntra.com/")) {
                    $r = splitAndGetValue($r, "myntra.com/", 1);
                    $parts = explode('/', $r);
                    $r = !empty($parts) ? array_pop($parts) : '';
                    while ($r === "" && !empty($parts)) {
                        $r = array_pop($parts);
                    }
                    if (!isNumericString($r) || $r === "0") {
                        $r = "";
                    }
                } else {
                    $r = "";
                }
                $pid = $r;
                break;
            }
              case 1830: // Nykaa
            {
                $r = $url;
                $r = splitMultiple($r, [['find' => "#", 'val' => 0], ['find' => "?", 'val' => 0], ['find' => "&", 'val' => 0]]);
                if (includesString($r, "/p/")) {
                    $s = explode("/p/", $r);
                    $o = count($s);
                    $r = trim($s[$o - 1]);
                    if (includesString($r, "/")) {
                        $r = trim(explode("/", $r)[0]);
                    }
                } else {
                    $r = "";
                }
                $pid = $r;
                break;
            }
        case 129: // Snapdeal
            {
                $r = $url;
                $r = splitMultiple($r, [['find' => "#", 'val' => 0], ['find' => "?", 'val' => 0], ['find' => "&", 'val' => 0]]);
                if (includesString($r, "/product/")) {
                    $s = explode("/product/", $r);
                    $o = count($s);
                    $r = trim($s[$o - 1]);
                    if (includesString($r, "/")) {
                        $parts = explode("/", $r);
                        if (count($parts) > 1) {
                            $r = trim($parts[1]); // Get the second part which is usually the PID
                        } else {
                            $r = "";
                        }
                    }
                } else {
                    $r = "";
                }
                $pid = $r;
                break;
            }
        case 421: // Shopclues
            {
                $r = $url;
                $r = splitMultiple($r, [['find' => "#", 'val' => 0], ['find' => "?", 'val' => 0], ['find' => "&", 'val' => 0]]);
                if (includesString($r, "p_id=")) {
                    $s = explode("p_id=", $r);
                    $o = count($s);
                    $r = trim($s[$o - 1]);
                    if (includesString($r, "&")) {
                        $r = trim(explode("&", $r)[0]);
                    }
                } else {
                    $matches = [];
                    if (preg_match('/\/([a-zA-Z0-9-]+)-([a-zA-Z0-9]+)$/', $r, $matches)) {
                        $r = $matches[2];
                    } else {
                        $r = "";
                    }
                }
                $pid = $r;
                break;
            }
        case 1429: // Paytm Mall
            {
                $r = $url;
                $r = splitMultiple($r, [['find' => "#", 'val' => 0], ['find' => "?", 'val' => 0], ['find' => "&", 'val' => 0]]);
                if (includesString($r, "/product/")) {
                    $s = explode("/product/", $r);
                    $o = count($s);
                    $r = trim($s[$o - 1]);
                    if (includesString($r, "/")) {
                        $parts = explode("/", $r);
                        $r = trim(end($parts));
                    }
                } else {
                    $matches = [];
                    if (preg_match('/\/([a-zA-Z0-9-]+)_([a-zA-Z0-9]+)$/', $r, $matches)) {
                        $r = $matches[2];
                    } else {
                        $r = "";
                    }
                }
                $pid = $r;
                break;
            }
        case 333: // Pepperfry
            {
                $r = $url;
                $r = splitMultiple($r, [['find' => "#", 'val' => 0], ['find' => "?", 'val' => 0], ['find' => "&", 'val' => 0]]);
                if (includesString($r, "/product/")) {
                    $s = explode("/product/", $r);
                    $o = count($s);
                    $r = trim($s[$o - 1]);
                    $matches = [];
                    if (preg_match('/\/([a-zA-Z0-9-]+)_([a-zA-Z0-9]+)$/', $r, $matches)) {
                        $r = $matches[2];
                    } else {
                        $parts = explode('_', $r);
                        $r = end($parts);
                    }
                } else {
                    $r = "";
                }
                $pid = $r;
                break;
            }
        case 57: // Lenskart
            {
                $r = $url;
                $r = splitMultiple($r, [['find' => "#", 'val' => 0], ['find' => "?", 'val' => 0], ['find' => "&", 'val' => 0]]);
                if (includesString($r, "/p-")) {
                    $s = explode("/p-", $r);
                    $o = count($s);
                    $r = trim($s[$o - 1]);
                } else if (includesString($r, "product_id=")) {
                    $s = explode("product_id=", $r);
                    $o = count($s);
                    $r = trim($s[$o - 1]);
                    if (includesString($r, "&")) {
                        $r = trim(explode("&", $r)[0]);
                    }
                } else {
                    $r = "";
                }
                $pid = $r;
                break;
            }
        
        // Default case for unsupported retailers
        default: {
            $r = $url;
            $r = splitMultiple($r, [['find' => "#", 'val' => 0], ['find' => "?", 'val' => 0], ['find' => "&", 'val' => 0]]);

            if (includesString($r, "/p/")) {
                $s = explode("/p/", $r); 
                $r = trim(explode('/', $s[count($s) - 1])[0]);
            } else if (includesString($r, "/dp/")) {
                $s = explode("/dp/", $r); 
                $r = trim(explode('/', $s[count($s) - 1])[0]);
            } else if (includesString($r, "/product/")) {
                $s = explode("/product/", $r); 
                $r = trim(explode('/', $s[count($s) - 1])[0]);
            } else {
                $r = getLastPathComponent($r, "/");
                if (includesString($r, '-')) {
                    $parts = explode('-', $r);
                    $r = end($parts);
                }
                if (includesString($r, '.htm')) {
                    $r = explode('.htm', $r)[0];
                    if (includesString($r, '-')) {
                        $parts = explode('-', $r);
                        $r = end($parts);
                    }
                }
            }

            $pid = trim($r);
            break;
        }
    }
    
    // Final cleanup
    $pid = trim(explode('?', $pid)[0]);
    $pid = trim(explode('&', $pid)[0]);
    $pid = trim(explode('#', $pid)[0]);
    $pid = rtrim($pid, '/');
    
    return $pid;
}

// Rules mapping domain patterns to POS IDs - Comprehensive list from urls.js
$posExtractionRules = [
    // Amazon sites
    ['domainPattern' => "amazon.in", 'pos' => 63],
    ['domainPattern' => "amazon.com", 'pos' => 6326],
    ['domainPattern' => "amazon.co.uk", 'pos' => 8965],
    ['domainPattern' => "amazon.ca", 'pos' => 8963],
    
    // Major Indian e-commerce platforms
    ['domainPattern' => "flipkart.com", 'pos' => 2],
    ['domainPattern' => "myntra.com", 'pos' => 111],
    ['domainPattern' => "ajio.com", 'pos' => 2191],
    ['domainPattern' => "tatacliq.com", 'pos' => 2190],
    ['domainPattern' => "jiomart.com", 'pos' => 6660],
    ['domainPattern' => "croma.com", 'pos' => 71],
    ['domainPattern' => "reliancedigital.in", 'pos' => 6607],
    ['domainPattern' => "snapdeal.com", 'pos' => 129],
    ['domainPattern' => "shopclues.com", 'pos' => 421],
    ['domainPattern' => "infibeam.com", 'pos' => 99],
    ['domainPattern' => "meesho.com", 'pos' => 7376],
    ['domainPattern' => "paytmmall.com", 'pos' => 1429],
    ['domainPattern' => "paytm.com", 'pos' => 1331],
    
    // Fashion and beauty platforms
    ['domainPattern' => "nykaa.com", 'pos' => 1830],
    ['domainPattern' => "nykaafashion.com", 'pos' => 6068],
    ['domainPattern' => "clovia.com", 'pos' => 1973],
    ['domainPattern' => "jabong.com", 'pos' => 50],
    ['domainPattern' => "koovs.com", 'pos' => 22],
    ['domainPattern' => "zivame.com", 'pos' => 429],
    ['domainPattern' => "shoppersstop.com", 'pos' => 45],
    ['domainPattern' => "purplle.com", 'pos' => 900],
    ['domainPattern' => "abof.com", 'pos' => 1850],
    ['domainPattern' => "nnow.com", 'pos' => 2192],
    ['domainPattern' => "voonik.com", 'pos' => 2266],
    ['domainPattern' => "mrvoonik.com", 'pos' => 2267],
    ['domainPattern' => "veromoda.in", 'pos' => 4423],
    ['domainPattern' => "adidas.co.in", 'pos' => 2097],
    ['domainPattern' => "2gud.com", 'pos' => 6031],
    ['domainPattern' => "myvishal.com", 'pos' => 2372],
    ['domainPattern' => "shein.in", 'pos' => 2362],
    ['domainPattern' => "plumgoodness.com", 'pos' => 6069],
    
    // Specialty stores
    ['domainPattern' => "lenskart.com", 'pos' => 57],
    ['domainPattern' => "bluestone.com", 'pos' => 426],    ['domainPattern' => "pepperfry.com", 'pos' => 333],
    ['domainPattern' => "decathlon.in", 'pos' => 2335],
    ['domainPattern' => "ebay.in", 'pos' => 1],
    ['domainPattern' => "banggood.in", 'pos' => 2364],
    ['domainPattern' => "fnp.com", 'pos' => 11],
    ['domainPattern' => "healthkart.com", 'pos' => 921],
    ['domainPattern' => "chumbak.com", 'pos' => 902],
    ['domainPattern' => "zoomin.com", 'pos' => 1005],
    
    // Pharma sites
    ['domainPattern' => "1mg.com", 'pos' => 2237],
    ['domainPattern' => "netmeds.com", 'pos' => 2238],
    
    // Other platforms
    ['domainPattern' => "indiatimes.com", 'pos' => 401],
    ['domainPattern' => "naaptol.com", 'pos' => 441],
    ['domainPattern' => "crossword.in", 'pos' => 471],
    ['domainPattern' => "uread.com", 'pos' => 1580],
    ['domainPattern' => "sapnaonline.com", 'pos' => 451],
    ['domainPattern' => "tradus.com", 'pos' => 13],
    ['domainPattern' => "homeshop18.com", 'pos' => 4],
    ['domainPattern' => "landmarkshops.in", 'pos' => 7],
    ['domainPattern' => "rediff.com", 'pos' => 291]
];

/**
 * Determines the Point of Sale ID (pos) based on the URL.
 * @param string $url The product URL.
 * @return int The corresponding Point of Sale ID (pos).
 */
function determinePosFromUrl($url) {
    global $posExtractionRules;
    
    // Default pos (0 means unknown)
    $pos = 0;
    
    // Clean the URL for better processing
    $cleanUrl = strtolower(trim($url));
    
    // Check each rule to find a match
    foreach ($posExtractionRules as $rule) {
        if (includesString($cleanUrl, $rule['domainPattern'])) {
            $pos = $rule['pos'];
            break;
        }
    }
    
    return $pos;
}

// Check if this is a short URL that needs client-side resolution
function isShortUrl($url) {
    $shortUrlPatterns = [
        '/amzn\.(in|to|com)\/[a-zA-Z0-9]+/i',
        '/flip\.kart\//i',
        '/myntra\.app\//i',
        '/bit\.ly\//i',
        '/goo\.gl\//i',
        '/tinyurl\.com\//i'
    ];
    
    foreach ($shortUrlPatterns as $pattern) {
        if (preg_match($pattern, $url)) {
            return true;
        }
    }
    return false;
}

// Process the URL and determine product ID and retailer ID
$url = $query;
$pos = determinePosFromUrl($url);

// Check if we should try to process this URL
// First, handle obvious short URLs
if (($pos === 0 || empty($pos)) && isShortUrl($url)) {
    // Encode the URL for passing to the processor page
    $encodedUrl = urlencode($url);
    
    // Redirect to the URL processor page
    header("Location: url_processor.php?url=" . $encodedUrl);
    exit;
}

$pid = extractPidByPos($pos, $url);

// If product ID is found and POS is valid, redirect to product page
if (!empty($pid) && !empty($pos)) {
    header("Location: product.php?url=" . urlencode($url) . "&pid=" . urlencode($pid) . "&pos=" . urlencode($pos));
    exit;
} else {
    // For invalid URLs or when we can't extract proper product information, 
    // try processing it silently through the URL processor
    $encodedUrl = urlencode($url);
    header("Location: url_processor.php?url=" . $encodedUrl . "&silent=1");
    exit;
}
?>
