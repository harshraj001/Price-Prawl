// productData.js - Helper for processing product data and displaying UI elements

// Function to safely access nested properties
function getSafe(obj, path, defaultValue = null) {
    if (!path || typeof path !== 'string' || obj === null || typeof obj !== 'object') {
        return defaultValue;
    }
    return path.split('.').reduce((acc, key) => 
        (acc && acc[key] !== undefined && acc[key] !== null) ? acc[key] : defaultValue, obj);
}

// Format price with Indian locale
function formatPrice(price, currency = '₹') {
    const num = Number(price);
    return isNaN(num) ? 'N/A' : currency + num.toLocaleString('en-IN');
}

// Create star rating display
function createStars(rating) {
    const fullStars = Math.floor(rating);
    const halfStar = rating % 1 >= 0.5;
    const emptyStars = 5 - fullStars - (halfStar ? 1 : 0);
    return '★'.repeat(fullStars) + (halfStar ? '½' : '') + '☆'.repeat(emptyStars);
}

// Process the additional data from Buyhatke API
function processAdditionalData(additionalData) {
    console.log("Processing additional data:", additionalData);
    
    // Remove the loading animation for additional data if it exists
    const additionalDataLoader = document.getElementById('additional-data-loader');
    if (additionalDataLoader) {
        additionalDataLoader.remove();
    }
    
    if (!additionalData || !additionalData.type || additionalData.type !== 'data' || !additionalData.nodes || !additionalData.nodes.length) {
        console.error("Invalid additional data format");
        return;
    }
    
    try {
        // Find the data node (usually the third node)
        const dataNode = additionalData.nodes.find(node => node.type === 'data' && node.data);
        if (!dataNode || !dataNode.data || !dataNode.data.length) {
            console.error("No valid data node found in the additional data");
            return;
        }
        
        // The data is often in a lookup table format where numbers reference other indexes
        const lookupTable = dataNode.data;
        const visitedRefs = new Set();
        
        // De-reference function to resolve the lookup table
        function deReference(data) {
            if (typeof data === 'number' && lookupTable && data >= 0 && data < lookupTable.length) {
                if (visitedRefs.has(data)) {
                    return `[Circular Ref: ${data}]`;
                }
                visitedRefs.add(data);
                const referencedValue = lookupTable[data];
                if (referencedValue === null) {
                    visitedRefs.delete(data);
                    return null;
                }
                const result = deReference(referencedValue);
                visitedRefs.delete(data);
                return result;
            } else if (Array.isArray(data)) {
                return data.map(item => deReference(item));
            } else if (typeof data === 'object' && data !== null) {
                const newObj = {};
                for (const key in data) {
                    if (Object.prototype.hasOwnProperty.call(data, key)) {
                        newObj[key] = deReference(data[key]);
                    }
                }
                return newObj;
            }
            return data;
        }
        
        // De-reference the first item which should contain the main product info
        const pageData = deReference(lookupTable[0]);
        console.log("De-referenced product data:", pageData);
        
        // Extract data we need
        const productData = getSafe(pageData, 'productData', {});
        const dealsData = getSafe(pageData, 'dealsData', []);
        const similarProducts = getSafe(pageData, 'similarProducts', []);
        const dittoProducts = getSafe(pageData, 'dittoProducts', []);
        const currency = getSafe(pageData, 'currencySymbol', '₹');
        const siteName = getSafe(pageData, 'siteName', 'Store');
        const historyString = getSafe(pageData, 'predictedData', '');
        
        // Create or update sections
        createPriceComparisonSection(dealsData, currency);
        createPriceHistorySection(historyString, currency);
        createSimilarProductsSection(similarProducts, currency);
        createDittoProductsSection(dittoProducts, currency);
        
        console.log("Additional data processing complete");
        
    } catch (error) {
        console.error("Error processing additional data:", error);
    }
}

// Create the price comparison section
function createPriceComparisonSection(dealsData, currency) {
    console.log("Creating price comparison section with deals:", dealsData);
    
    if (!dealsData || dealsData.length === 0) {
        console.log("No deals available for price comparison");
        return;
    }
    
    // Get the container
    const container = document.querySelector('.bg-brand-header.rounded-xl');
    if (!container) {
        console.error("Could not find container for price comparison");
        return;
    }
    
    // Check if section already exists
    let priceComparisonSection = document.getElementById('price-comparison-section');
    if (!priceComparisonSection) {
        // Create new section
        priceComparisonSection = document.createElement('div');
        priceComparisonSection.id = 'price-comparison-section';
        priceComparisonSection.className = 'p-5 border-t border-brand-border/30 bg-gradient-to-r from-blue-50 to-white';
        priceComparisonSection.innerHTML = `
            <h3 class="text-xl font-semibold mb-4 text-brand-text-primary">Best Prices From Top Retailers</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white rounded-lg overflow-hidden shadow-md">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold">Retailer</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold">Price</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody id="price-comparison-tbody"></tbody>
                </table>
            </div>
        `;
        container.appendChild(priceComparisonSection);
    }
    
    // Get the tbody
    const tbody = document.getElementById('price-comparison-tbody');
    if (!tbody) {
        console.error("Could not find or create tbody for price comparison");
        return;
    }
    
    // Clear existing rows
    tbody.innerHTML = '';
      // Sort deals by price
    const sortedDeals = [...dealsData].sort((a, b) => {
        const aPrice = parseFloat(a.price) || 0;
        const bPrice = parseFloat(b.price) || 0;
        return aPrice - bPrice;
    });    // Add rows to the table
    sortedDeals.forEach(deal => {
        if (!deal || typeof deal !== 'object') return;
        
        const tr = document.createElement('tr');
        tr.className = 'border-b hover:bg-blue-50 transition-colors';
        
        const retailerCell = document.createElement('td');
        retailerCell.className = 'px-6 py-4';
        retailerCell.innerHTML = `
            <div class="flex items-center">
                ${deal.site_image ? `<img src="${deal.site_image}" alt="${deal.site_name}" class="h-10 w-auto mr-4">` : ''}
                <span class="font-medium text-base">${deal.site_name || 'Unknown'}</span>
            </div>
        `;
        
        const priceCell = document.createElement('td');
        priceCell.className = 'px-6 py-4 font-bold text-brand-accent text-xl';
        priceCell.textContent = formatPrice(deal.price, currency);
        
        // Handle discounts correctly - only show discount if original price and current price are valid
        // and original price is greater than current price
        const originalPrice = parseFloat(deal.original_price || 0);
        const currentPrice = parseFloat(deal.price || 0);
        let discountHtml = '';
        
        if (originalPrice > 0 && currentPrice > 0 && originalPrice > currentPrice) {
            const discountPercentage = Math.round(((originalPrice - currentPrice) / originalPrice) * 100);
            if (discountPercentage > 0 && discountPercentage < 100) {
                discountHtml = `<span class="text-green-600 text-sm block mt-1 font-medium">${discountPercentage}% OFF</span>`;
            }
        }
        
        if (discountHtml) {
            priceCell.innerHTML = `${formatPrice(deal.price, currency)} ${discountHtml}`;
        }
        
        const actionCell = document.createElement('td');
        actionCell.className = 'px-6 py-4';
        actionCell.innerHTML = `
            <a href="${deal.link || '#'}" target="_blank" class="bg-brand-accent text-white px-5 py-2 rounded-md hover:bg-brand-accent-hover transition-colors inline-block font-medium shadow-sm hover:shadow">
                Buy Now
            </a>
        `;
        
        tr.appendChild(retailerCell);
        tr.appendChild(priceCell);
        tr.appendChild(actionCell);
        
        tbody.appendChild(tr);
    });
    
    console.log("Price comparison section created successfully");
}

// Create the price history section with chart
function createPriceHistorySection(historyString, currency) {
    console.log("Creating price history section");
    
    if (!historyString || typeof historyString !== 'string') {
        console.log("No price history data available");
        return;
    }
    
    // Get the container
    const container = document.querySelector('.bg-brand-header.rounded-xl');
    if (!container) {
        console.error("Could not find container for price history");
        return;
    }
    
    // Parse the history string
    const parts = historyString.split('&~&~');
    const historyPartString = parts[0];
    const predictionScoreString = parts.length > 1 ? parts[parts.length - 2] : null;
    
    // Variables for stats
    let maxPrice = 0;
    let minPrice = Infinity;
    let sumPrice = 0;
    let countPrice = 0;
    let chartData = [];
    let predictionScore = parseInt(predictionScoreString, 10);
    
    // Parse price history data
    if (historyPartString) {
        const historyEntries = historyPartString.split('*~*');
        
        // Calculate timestamp for 2 months ago
        const twoMonthsAgo = new Date();
        twoMonthsAgo.setMonth(twoMonthsAgo.getMonth() - 2);
        const twoMonthsAgoTimestamp = twoMonthsAgo.getTime();
        
        // Track recent prices separately for buying suggestions
        let recentPrices = [];
        
        historyEntries.forEach(part => {
            const item = part.split('~');
            if (item.length >= 2) {
                const timestampStr = item[0];
                const price = parseInt(item[1], 10);
                
                if (!isNaN(price) && price > 0) {
                    let date = null;
                    try {
                        date = new Date(timestampStr.replace(' ', 'T') + 'Z');
                        if (isNaN(date.getTime())) {
                            const dateMatch = timestampStr.match(/(\d{4})-(\d{2})-(\d{2})\s(\d{2}):(\d{2}):(\d{2})/);
                            if (dateMatch) {
                                date = new Date(Date.UTC(
                                    parseInt(dateMatch[1]), parseInt(dateMatch[2]) - 1, parseInt(dateMatch[3]),
                                    parseInt(dateMatch[4]), parseInt(dateMatch[5]), parseInt(dateMatch[6])
                                ));
                            }
                        }
                    } catch (e) { /* ignore parsing errors */ }
                    
                    if (date && !isNaN(date.getTime())) {
                        const timestamp = date.getTime();
                        chartData.push([timestamp, price]);
                        
                        // Track separately for overall stats
                        if (price > maxPrice) maxPrice = price;
                        if (price < minPrice) minPrice = price;
                        sumPrice += price;
                        countPrice++;
                        
                        // Track recent prices (last 2 months) for buying suggestions
                        if (timestamp >= twoMonthsAgoTimestamp) {
                            recentPrices.push(price);
                        }
                    }
                }
            }
        });
        
        // Calculate recent average (last 2 months) for buying suggestions
        let recentAvg = 0;
        if (recentPrices.length > 0) {
            recentAvg = recentPrices.reduce((sum, price) => sum + price, 0) / recentPrices.length;
        }
    }
    
    // Check if we have valid chart data
    if (chartData.length < 2) {
        console.log("Not enough price history data for chart");
        return;
    }
    
    // Sort chart data by timestamp
    chartData.sort((a, b) => a[0] - b[0]);
    
    // Create or update the price history section
    let priceHistorySection = document.getElementById('price-history-section');
    if (!priceHistorySection) {
        priceHistorySection = document.createElement('div');
        priceHistorySection.id = 'price-history-section';
        priceHistorySection.className = 'p-4 border-t border-brand-border/30';
        priceHistorySection.innerHTML = `
            <h3 class="text-lg font-semibold mb-3 text-brand-text-primary">Price History</h3>
            <div id="price-chart" style="height: 300px;"></div>
            <div class="mt-3 text-sm text-brand-text-secondary">
                <div>Lowest Price: <span id="lowest-price">${formatPrice(minPrice, currency)}</span></div>
                <div>Highest Price: <span id="highest-price">${formatPrice(maxPrice, currency)}</span></div>
                <div>Average Price: <span id="avg-price">${formatPrice(Math.round(sumPrice / countPrice), currency)}</span></div>
            </div>
        `;
        container.appendChild(priceHistorySection);
    } else {
        // Update stats for existing section
        const lowestEl = document.getElementById('lowest-price');
        const highestEl = document.getElementById('highest-price');
        const avgEl = document.getElementById('avg-price');
        
        if (lowestEl) lowestEl.textContent = formatPrice(minPrice, currency);
        if (highestEl) highestEl.textContent = formatPrice(maxPrice, currency);
        if (avgEl) avgEl.textContent = formatPrice(Math.round(sumPrice / countPrice), currency);
    }
    
    // Create price chart
    setTimeout(() => {
        try {
            if (typeof Highcharts !== 'undefined') {
                Highcharts.chart('price-chart', {
                    chart: { type: 'areaspline', zoomType: 'x' },
                    title: { text: null },
                    credits: { enabled: false },
                    xAxis: { type: 'datetime', labels: { format: '{value:%b %d}' } },
                    yAxis: { 
                        title: { text: null },
                        labels: { formatter: function() { return currency + this.value.toLocaleString('en-IN'); } }
                    },
                    legend: { enabled: false },
                    tooltip: { 
                        shared: true, 
                        useHTML: true,
                        headerFormat: '<span style="font-size: 10px">{point.key:%A, %b %d, %Y}</span><br/>',
                        pointFormat: `<span style="color:{point.color}">●</span> Price: <b>${currency}{point.y:,.0f}</b><br/>`
                    },
                    plotOptions: { 
                        areaspline: { 
                            fillOpacity: 0.1,
                            lineWidth: 2,
                            marker: { enabled: false },
                            states: { hover: { lineWidth: 2 } },
                            threshold: null,
                            color: '#007bff'
                        }
                    },
                    series: [{ name: 'Price', data: chartData }]
                });
                console.log("Price chart created successfully");
            } else {
                console.error("Highcharts library not available");
                // Add Highcharts dynamically if not available
                const script = document.createElement('script');
                script.src = 'https://code.highcharts.com/highcharts.js';
                script.onload = () => {
                    const accessScript = document.createElement('script');
                    accessScript.src = 'https://code.highcharts.com/modules/accessibility.js';
                    accessScript.onload = () => createPriceHistorySection(historyString, currency);
                    document.head.appendChild(accessScript);
                };
                document.head.appendChild(script);
            }
        } catch (error) {
            console.error("Error creating price chart:", error);
        }    }, 100);
    
    // Always add buying suggestion, with or without prediction score
    let suggestionSection = document.getElementById('buying-suggestion-section');
    if (!suggestionSection) {
        suggestionSection = document.createElement('div');
        suggestionSection.id = 'buying-suggestion-section';
        suggestionSection.className = 'p-4 border-t border-brand-border/30 bg-gradient-to-r from-purple-50 to-white';
        
        let suggestionText = '';
        let suggestionClass = '';
        let suggestionIcon = '';
        
        // If we have a valid prediction score, use it
        if (!isNaN(predictionScore) && predictionScore !== null) {
            if (predictionScore <= 40) {
                suggestionText = "Price is low compared to historical data. Good time to buy!";
                suggestionClass = 'bg-green-100 border-green-200 text-green-800';
                suggestionIcon = '<i class="fas fa-check-circle mr-2"></i>';
            } else if (predictionScore <= 60) {
                suggestionText = "Price is average. Consider waiting for a better deal.";
                suggestionClass = 'bg-yellow-100 border-yellow-200 text-yellow-800';
                suggestionIcon = '<i class="fas fa-clock mr-2"></i>';
            } else {
                suggestionText = "Price is higher than usual. Better to wait for a price drop.";
                suggestionClass = 'bg-red-100 border-red-200 text-red-800';
                suggestionIcon = '<i class="fas fa-exclamation-triangle mr-2"></i>';
            }
        } 
        // Otherwise, use current price vs. historical average
        else {
            // Using price comparison with historical averages
            const currentPrice = chartData.length > 0 ? chartData[chartData.length - 1][1] : null;
            const avgPrice = countPrice > 0 ? sumPrice / countPrice : null;
            
            if (currentPrice !== null && avgPrice !== null) {
                const ratio = currentPrice / avgPrice;
                
                if (ratio < 0.9) { // 10% or more below average
                    suggestionText = "Current price is below the historical average. Good time to buy!";
                    suggestionClass = 'bg-green-100 border-green-200 text-green-800';
                    suggestionIcon = '<i class="fas fa-check-circle mr-2"></i>';
                } else if (ratio > 1.1) { // 10% or more above average
                    suggestionText = "Current price is above the historical average. Consider waiting for a price drop.";
                    suggestionClass = 'bg-red-100 border-red-200 text-red-800';
                    suggestionIcon = '<i class="fas fa-exclamation-triangle mr-2"></i>';
                } else {
                    suggestionText = "Current price is close to the historical average. Fair time to buy if you need it now.";
                    suggestionClass = 'bg-yellow-100 border-yellow-200 text-yellow-800';
                    suggestionIcon = '<i class="fas fa-clock mr-2"></i>';
                }
            } else {
                suggestionText = "Unable to determine price trend. Check the price history graph for more insights.";
                suggestionClass = 'bg-blue-100 border-blue-200 text-blue-800';
                suggestionIcon = '<i class="fas fa-info-circle mr-2"></i>';
            }
        }
        
        suggestionSection.innerHTML = `
            <h3 class="text-lg font-semibold mb-3 text-brand-text-primary">Buying Suggestion</h3>
            <div class="p-4 rounded-lg border shadow-sm ${suggestionClass} flex items-center">
                ${suggestionIcon}
                <span class="font-medium">${suggestionText}</span>
            </div>
        `;
        container.appendChild(suggestionSection);
    }
    
    console.log("Price history section created successfully");
}

// Create similar products section
function createSimilarProductsSection(similarProducts, currency) {
    console.log("Creating similar products section with products:", similarProducts);
    
    if (!similarProducts || similarProducts.length === 0) {
        console.log("No similar products available");
        return;
    }
    
    // Get the container
    const container = document.querySelector('.bg-brand-header.rounded-xl');
    if (!container) {
        console.error("Could not find container for similar products");
        return;
    }
    
    // Create or update the similar products section
    let similarSection = document.getElementById('similar-products-section');
    if (!similarSection) {
        similarSection = document.createElement('div');
        similarSection.id = 'similar-products-section';
        similarSection.className = 'p-5 border-t border-brand-border/30';
        similarSection.innerHTML = `
            <h3 class="text-xl font-semibold mb-4 text-brand-text-primary">Similar Products You Might Like</h3>
            <div id="similar-products-grid" class="grid grid-cols-2 md:grid-cols-4 gap-4"></div>
        `;
        container.appendChild(similarSection);
    }
    
    // Get the grid
    const grid = document.getElementById('similar-products-grid');
    if (!grid) {
        console.error("Could not find or create grid for similar products");
        return;
    }
    
    // Clear existing products
    grid.innerHTML = '';
    
    // Display ALL products (no limit slice)
    similarProducts.forEach(product => {
        if (!product || typeof product !== 'object') return;
        
        const productDiv = document.createElement('div');
        productDiv.className = 'bg-white rounded-lg shadow-md border border-brand-border/30 p-3 hover:shadow-lg transition-shadow';
        
        const name = product.name || 'Similar Product';
        const price = product.cur_price || 0;
        const image = product.image || '';
        const link = product.link || '#';
        const lastPrice = product.last_price || 0;
        const dropPercent = parseFloat(product.price_drop_per) || 0;
        
        let discountHtml = '';
        if (lastPrice > price && lastPrice > 0 && price > 0) {
            // Calculate discount percentage manually to avoid using potentially incorrect values
            const calculatedDiscount = Math.round(((lastPrice - price) / lastPrice) * 100);
            // Only show discount if it's reasonable (between 1-99%)
            if (calculatedDiscount > 0 && calculatedDiscount < 100) {
                discountHtml = `
                    <div class="text-xs text-green-600 mt-1">
                        ${calculatedDiscount}% OFF
                        <span class="text-gray-400 line-through">${formatPrice(lastPrice, currency)}</span>
                    </div>
                `;
            }
        }
        
        productDiv.innerHTML = `
            <a href="${link}" target="_blank" class="block">
                <div class="h-32 flex items-center justify-center mb-2">
                    <img src="${image}" alt="${name}" class="max-h-full max-w-full object-contain">
                </div>
                <div class="text-sm font-medium text-brand-text-primary line-clamp-2 h-10 overflow-hidden" title="${name}">
                    ${name}
                </div>
                <div class="mt-1 font-bold text-brand-accent">${formatPrice(price, currency)}</div>
                ${discountHtml}
            </a>
        `;
        
        grid.appendChild(productDiv);
    });
    
    console.log("Similar products section created successfully");
}

// Create ditto (identical) products section
function createDittoProductsSection(dittoProducts, currency) {
    console.log("Creating ditto products section with products:", dittoProducts);
    
    if (!dittoProducts || dittoProducts.length === 0) {
        console.log("No ditto products available");
        return;
    }
    
    // Get the container
    const container = document.querySelector('.bg-brand-header.rounded-xl');
    if (!container) {
        console.error("Could not find container for ditto products");
        return;
    }
    
    // Create or update the ditto products section
    let dittoSection = document.getElementById('ditto-products-section');
    if (!dittoSection) {
        dittoSection = document.createElement('div');
        dittoSection.id = 'ditto-products-section';
        dittoSection.className = 'p-4 border-t border-brand-border/30';
        dittoSection.innerHTML = `
            <h3 class="text-lg font-semibold mb-3 text-brand-text-primary">Same Product on Other Stores</h3>
            <div id="ditto-products-grid" class="grid grid-cols-2 md:grid-cols-4 gap-3"></div>
        `;
        container.appendChild(dittoSection);
    }
    
    // Get the grid
    const grid = document.getElementById('ditto-products-grid');
    if (!grid) {
        console.error("Could not find or create grid for ditto products");
        return;
    }
    
    // Clear existing products
    grid.innerHTML = '';
    
    // Add products to the grid
    dittoProducts.forEach(product => {
        if (!product || typeof product !== 'object') return;
        
        const productDiv = document.createElement('div');
        productDiv.className = 'bg-white rounded shadow-sm border border-brand-border/30 p-2 hover:shadow-md transition-shadow';
        
        const name = product.name || 'Identical Product';
        const price = product.cur_price || 0;
        const image = product.image || '';
        const link = product.link || '#';
        const store = product.site_name || 'Store';
        const storeLogo = product.site_logo || '';
        
        productDiv.innerHTML = `
            <a href="${link}" target="_blank" class="block">
                <div class="h-28 flex items-center justify-center mb-2">
                    <img src="${image}" alt="${name}" class="max-h-full max-w-full object-contain">
                </div>
                <div class="text-sm text-brand-text-primary line-clamp-2 h-10 overflow-hidden" title="${name}">
                    ${name}
                </div>
                <div class="flex items-center mt-1 text-xs text-brand-text-secondary">
                    ${storeLogo ? `<img src="${storeLogo}" alt="${store}" class="h-4 mr-1">` : ''}
                    <span>on ${store}</span>
                </div>
                <div class="mt-1 font-bold text-brand-accent">${formatPrice(price, currency)}</div>
            </a>
        `;
        
        grid.appendChild(productDiv);
    });
    
    console.log("Ditto products section created successfully");
}

// Add Highcharts if not already included
function ensureHighchartsLoaded(callback) {
    if (typeof Highcharts === 'undefined') {
        const script = document.createElement('script');
        script.src = 'https://code.highcharts.com/highcharts.js';
        script.onload = () => {
            const accessScript = document.createElement('script');
            accessScript.src = 'https://code.highcharts.com/modules/accessibility.js';
            accessScript.onload = callback;
            document.head.appendChild(accessScript);
        };
        document.head.appendChild(script);
    } else {
        callback();
    }
}
