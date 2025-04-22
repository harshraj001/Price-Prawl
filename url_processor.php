<?php
/**
 * PricePrawl URL Processor
 * Handles the processing of URLs including shared links, shortened URLs, and special formats
 */

$url = isset($_GET['url']) ? trim($_GET['url']) : '';
$silent = isset($_GET['silent']) && $_GET['silent'] == '1';

// If no URL provided, redirect back to home
if (empty($url)) {
    header('Location: index.php');
    exit;
}

// Set page title
$pageTitle = 'Processing URL';
include_once 'includes/header.php';
?>

<!-- Main Content Area -->
<div class="max-w-4xl mx-auto py-10 px-4">
    <div class="text-center">
        <h1 class="text-2xl md:text-3xl font-bold text-brand-text-primary dark:text-dark-brand-text-primary mb-4">
            Processing URL
        </h1>
        <p class="text-brand-text-secondary dark:text-dark-brand-text-secondary mb-8">
            Please wait while we analyze and process the URL...
        </p>
        
        <!-- Loader Animation -->
        <div class="mx-auto w-16 h-16 mb-8">
            <div class="loader !w-16 !h-16"></div>
        </div>
        
        <div id="status-message" class="text-brand-text-secondary dark:text-dark-brand-text-secondary">
            Processing...
        </div>
    </div>
</div>

<script>
// Store the original URL
const originalUrl = "<?php echo htmlspecialchars($url); ?>";
const isSilent = <?php echo $silent ? 'true' : 'false'; ?>;

// Status update helper function
function updateStatus(message) {
    if (!isSilent) {
        document.getElementById('status-message').textContent = message;
    }
}

// Main function to process the URL
async function processUrl(url) {
    try {
        updateStatus("Analyzing URL...");
          
        // Use the CORS proxy
        const corsProxyUrl = 'https://cors-ninja.harshraj864869.workers.dev/proxy?url=';
        const insertApiUrl = `${corsProxyUrl}${encodeURIComponent("https://buyhatke.com/api/insertURLForRedirect")}`;
          
        // Step 1: Submit the URL for processing
        const submitResponse = await fetch(insertApiUrl, {
            method: "POST",
            headers: {
                "content-type": "application/json",
                "accept": "*/*"
            },
            body: JSON.stringify({ url: url }),
            mode: "cors",
            credentials: "omit"
        });

        console.log("Submit response:", submitResponse);
        
        if (!submitResponse.ok) {
            throw new Error("Failed to process URL");
        }
        
        const submitData = await submitResponse.json();
        
        if (submitData.status !== 200 || !submitData.id) {
            throw new Error("Invalid response when processing URL");
        }
          const redirectId = submitData.id;
        updateStatus("Analyzing redirects...");
        
        // Function to fetch redirected URL with retries
        async function getRedirectedUrl(id, maxRetries = 3, initialDelay = 2000) {
            let currentDelay = initialDelay;
            let retries = 0;
            
            while (retries <= maxRetries) {
                updateStatus(`Processing link${retries > 0 ? ` (attempt ${retries+1}/${maxRetries+1})` : ''}...`);
                
                // Wait before each attempt
                await new Promise(resolve => setTimeout(resolve, currentDelay));
                
                // Get the redirected URL
                const redirectApiUrl = `${corsProxyUrl}${encodeURIComponent(`https://buyhatke.com/api/getRedirectedURL?id=${id}`)}`;
                const redirectResponse = await fetch(redirectApiUrl, {
                    method: "GET",
                    mode: "cors",
                    credentials: "omit",
                    headers: {
                        "accept": "*/*",
                        "content-type": "application/json"
                    }
                });
                console.log(`Redirect response (attempt ${retries+1}):`, redirectResponse);
                
                if (!redirectResponse.ok) {
                    console.log(`Failed attempt ${retries+1}: HTTP error ${redirectResponse.status}`);
                    retries++;
                    currentDelay *= 1.5; // Increase delay for next attempt
                    continue;
                }
                
                const redirectData = await redirectResponse.json();
                console.log(`Redirect data (attempt ${retries+1}):`, JSON.stringify(redirectData, null, 2));
                
                // If we have a valid response, return it
                if (redirectData.status === 1 && redirectData.data && redirectData.data.redirectedURL) {
                    return redirectData;
                }
                
                // If status is 0 and message is "not yet fetched", retry
                if (redirectData.status === 0 && redirectData.msg === "not yet fetched") {
                    console.log(`URL not ready yet, retrying (attempt ${retries+1})`);
                    retries++;
                    currentDelay *= 1.5; // Increase delay for next attempt
                } else {
                    // If we received a different error, throw it
                    throw new Error(`URL processing failed: ${redirectData.msg || "Unknown error"}`);
                }
            }
            
            // If we've exhausted all retries
            throw new Error("Maximum retries reached. Could not process this URL.");
        }
        
        // Attempt to get the redirected URL with retries
        const redirectData = await getRedirectedUrl(redirectId);
        console.log("Final redirect response data:", JSON.stringify(redirectData, null, 2));
        
        const processedUrl = redirectData.data.redirectedURL;
        updateStatus("URL processed successfully! Redirecting...");
        
        // Step 4: Submit the processed URL using POST method instead of GET redirect
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'search_processor.php';
        
        // Create hidden input for the processed URL
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'processed_url';
        input.value = processedUrl;
        
        // Add input to form and submit
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
        } catch (error) {
        console.error("Error processing URL:", error);
        
        if (isSilent) {
            // For silent mode, redirect back to search with generic error
            window.location.href = "index.php?error=processing_failed&message=Something+went+wrong+please+try+again+later";
        } else {
            updateStatus("Error: Something went wrong. Please try again later.");
            
            // Redirect back to home after a brief delay
            setTimeout(() => {
                window.location.href = "index.php?error=processing_failed&message=Something+went+wrong+please+try+again+later";
            }, 3000);
        }
    }
}

// Start the processing when page loads
document.addEventListener('DOMContentLoaded', function() {
    processUrl(originalUrl);
});
</script>

<?php
include_once 'includes/footer.php';
?>
