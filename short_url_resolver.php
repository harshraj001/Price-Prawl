<?php
/**
 * PricePrawl Short URL Resolver
 * Handles the resolution of shortened URLs (like amzn.in, bit.ly, etc.) using client-side API calls
 */

$shortUrl = isset($_GET['url']) ? trim($_GET['url']) : '';

// If no URL provided, redirect back to home
if (empty($shortUrl)) {
    header('Location: index.php');
    exit;
}

// Set page title
$pageTitle = 'Resolving URL';
include_once 'includes/header.php';
?>

<!-- Main Content Area -->
<div class="max-w-4xl mx-auto py-10 px-4">
    <div class="text-center">
        <h1 class="text-2xl md:text-3xl font-bold text-brand-text-primary dark:text-dark-brand-text-primary mb-4">
            Resolving Short URL
        </h1>
        <p class="text-brand-text-secondary dark:text-dark-brand-text-secondary mb-8">
            Please wait while we process the shortened URL and redirect you to the product page...
        </p>
        
        <!-- Loader Animation -->
        <div class="mx-auto w-16 h-16 mb-8">
            <div class="loader !w-16 !h-16"></div>
        </div>
        
        <div id="status-message" class="text-brand-text-secondary dark:text-dark-brand-text-secondary">
            Contacting server...
        </div>
    </div>
</div>

<script>
// Store the original short URL
const shortUrl = "<?php echo htmlspecialchars($shortUrl); ?>";

// Status update helper function
function updateStatus(message) {
    document.getElementById('status-message').textContent = message;
}

// Main function to resolve short URL
async function resolveShortUrl(url) {
    try {
        updateStatus("Requesting URL resolution...");
          // Use the same CORS proxy as in product.php
        const corsProxyUrl = 'https://cors-ninja.harshraj864869.workers.dev/proxy?url=';
        const insertApiUrl = `${corsProxyUrl}${encodeURIComponent("https://buyhatke.com/api/insertURLForRedirect")}`;
          // Step 1: Submit the URL for redirection analysis
        const submitResponse = await fetch(insertApiUrl, {
            method: "POST",
            headers: {
                "accept": "*/*",
                "accept-language": "en-US,en;q=0.5",
                "baggage": "sentry-environment=production,sentry-release=bf92fb76e67fca2836013c7ad5baf671f3fb26ad,sentry-public_key=b51e09137a43a260d868ea63aaa49b2f,sentry-trace_id=417ee77243fd45d4be25a6799979fcbb,sentry-sample_rate=1,sentry-transaction=%2F(main),sentry-sampled=true",
                "content-type": "application/json",
                "priority": "u=1, i",
                "sec-ch-ua": "\"Brave\";v=\"135\", \"Not-A.Brand\";v=\"8\", \"Chromium\";v=\"135\"",
                "sec-ch-ua-mobile": "?0",
                "sec-ch-ua-platform": "\"Windows\"",
                "sec-fetch-dest": "empty",
                "sec-fetch-mode": "cors",
                "sec-fetch-site": "same-origin",
                "sec-gpc": "1",
                "sentry-trace": "417ee77243fd45d4be25a6799979fcbb-afb4af1a9f1936c8-1"
            },
            body: JSON.stringify({ url: url }),
            mode: "cors",
            credentials: "include"
        });
        
        if (!submitResponse.ok) {
            throw new Error("Failed to submit URL for resolution. Server returned: " + submitResponse.status);
        }
        
        const submitData = await submitResponse.json();
        
        if (submitData.status !== 200 || !submitData.id) {
            throw new Error("Invalid response when submitting URL");
        }
        
        const redirectId = submitData.id;
        updateStatus("Processing redirect...");
        
        // Step 2: Wait a moment to allow server to process the redirect
        await new Promise(resolve => setTimeout(resolve, 1000));
          // Step 3: Get the redirected URL using the CORS proxy
        const redirectApiUrl = `${corsProxyUrl}${encodeURIComponent(`https://buyhatke.com/api/getRedirectedURL?id=${redirectId}`)}`;
        const redirectResponse = await fetch(redirectApiUrl, {
            method: "GET",
            mode: "cors",
            credentials: "omit"
        });
        
        if (!redirectResponse.ok) {
            throw new Error("Failed to get redirected URL. Server returned: " + redirectResponse.status);
        }
        
        const redirectData = await redirectResponse.json();
        
        if (redirectData.status !== 1 || !redirectData.data || !redirectData.data.redirectedURL) {
            throw new Error("Could not resolve the shortened URL");
        }
        
        const longUrl = redirectData.data.redirectedURL;
        updateStatus("URL resolved successfully! Redirecting...");
        
        // Step 4: Redirect to search.php with the resolved URL
        window.location.href = "search.php?query=" + encodeURIComponent(longUrl);
        
    } catch (error) {
        console.error("Error resolving short URL:", error);
        updateStatus("Error: " + error.message + ". Redirecting back to home...");
        
        // If there's an error, redirect back to home after a brief delay
        setTimeout(() => {
            window.location.href = "index.php?error=url_resolution_failed&query=" + encodeURIComponent(shortUrl);
        }, 3000);
    }
}

// Start the resolution process when page loads
document.addEventListener('DOMContentLoaded', function() {
    resolveShortUrl(shortUrl);
});
</script>

<?php
include_once 'includes/footer.php';
?>
