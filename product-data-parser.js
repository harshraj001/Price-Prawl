/**
 * product-data-parser.js
 * Enhanced product data parsing and display functions for PricePrawl
 * WITH DARK MODE CLASSES APPLIED + Highcharts Theming
 */

// --- Helper Functions (Keep from previous version) ---
function getSafe(obj, path, defaultValue = null) { /* ... */
    if (!path || typeof path !== 'string' || obj === null || typeof obj !== 'object') { return defaultValue; }
    return path.split('.').reduce((acc, key) => (acc && acc[key] !== undefined && acc[key] !== null) ? acc[key] : defaultValue, obj);
}
function formatPrice(price, currency = '₹') { /* ... */
   const num = Number(price);
   return isNaN(num) ? 'N/A' : currency + num.toLocaleString('en-IN', { maximumFractionDigits: 0 });
}
function createStars(rating) { /* ... */
   const fullStars = Math.floor(rating);
   const halfStar = rating % 1 >= 0.5;
   const emptyStars = 5 - fullStars - (halfStar ? 1 : 0);
   return '★'.repeat(fullStars) + (halfStar ? '½' : '') + '☆'.repeat(emptyStars);
}

// --- Section Creation Functions (WITH DARK MODE CLASSES) ---

function createPriceComparisonSection(containerElement, dealsData, currency) {
   // ... (Keep the dark mode classes added in the previous step) ...
    if (!containerElement || !dealsData || dealsData.length === 0) return;
   console.log("Creating price comparison section with deals:", dealsData);

   let section = document.getElementById('price-comparison-section');
   if (!section) {
       section = document.createElement('div');
       section.id = 'price-comparison-section';
       section.className = 'p-4 md:p-6 border-t border-brand-border/30 dark:border-dark-brand-border/50 bg-gradient-to-r from-blue-50 to-white dark:from-indigo-900/20 dark:to-dark-brand-header rounded-b-xl md:rounded-bl-none md:rounded-br-xl section-transition';
       section.innerHTML = `
           <h3 class="text-xl font-semibold mb-4 text-brand-text-primary dark:text-dark-brand-text-primary">Price Comparison</h3>
           <div class="overflow-x-auto rounded-lg border border-brand-border/30 dark:border-dark-brand-border/50 shadow-sm">
               <table class="min-w-full">
                   <thead class="bg-brand-bg-light/40 dark:bg-dark-brand-bg-light/40">
                       <tr>
                           <th class="px-4 py-3 text-left text-xs font-semibold text-brand-text-secondary dark:text-dark-brand-text-secondary uppercase tracking-wider">Retailer</th>
                           <th class="px-4 py-3 text-left text-xs font-semibold text-brand-text-secondary dark:text-dark-brand-text-secondary uppercase tracking-wider">Price</th>
                           <th class="px-4 py-3 text-left text-xs font-semibold text-brand-text-secondary dark:text-dark-brand-text-secondary uppercase tracking-wider">Action</th>
                       </tr>
                   </thead>
                   <tbody id="price-comparison-tbody" class="bg-brand-header dark:bg-dark-brand-header divide-y divide-brand-border/30 dark:divide-dark-brand-border/50"></tbody>
               </table>
           </div>`;
       containerElement.appendChild(section);
   }

   const tbody = section.querySelector('#price-comparison-tbody');
   if (!tbody) return;
   tbody.innerHTML = '';

   const sortedDeals = [...dealsData].sort((a, b) => (parseFloat(a.price) || Infinity) - (parseFloat(b.price) || Infinity));

   sortedDeals.forEach(deal => { /* ... (keep row generation logic with dark classes) ... */
       if (!deal || typeof deal !== 'object') return;
       const tr = document.createElement('tr');
       tr.className = 'hover:bg-brand-bg-light/40 dark:hover:bg-dark-brand-bg-light/30 transition-colors duration-150';
       const retailerCell = document.createElement('td');
       retailerCell.className = 'px-4 py-3 whitespace-nowrap';
       retailerCell.innerHTML = `<div class="flex items-center"> ${deal.site_image ? `<img src="${deal.site_image}" alt="${deal.site_name}" class="h-6 w-auto mr-3 flex-shrink-0 object-contain rounded-sm">` : ''} <span class="font-medium text-sm text-brand-text-primary dark:text-dark-brand-text-primary">${deal.site_name || 'Unknown'}</span> </div>`;
       const priceCell = document.createElement('td');
       priceCell.className = 'px-4 py-3 whitespace-nowrap font-semibold text-brand-accent dark:text-dark-brand-accent text-base';
       priceCell.textContent = formatPrice(deal.price, currency);
       const originalPrice = parseFloat(deal.original_price || 0);
       const currentPrice = parseFloat(deal.price || 0);
       if (originalPrice > 0 && currentPrice > 0 && originalPrice > currentPrice) {
           const discountPercentage = Math.round(((originalPrice - currentPrice) / originalPrice) * 100);
           if (discountPercentage > 0 && discountPercentage < 100) {
               priceCell.innerHTML += `<span class="text-green-600 dark:text-green-400 text-xs block mt-0.5 font-medium">${discountPercentage}% OFF</span>`;
           }
       }
       const actionCell = document.createElement('td');
       actionCell.className = 'px-4 py-3 whitespace-nowrap';
       actionCell.innerHTML = `<a href="${deal.link || '#'}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center bg-brand-accent dark:bg-dark-brand-accent hover:bg-brand-accent-hover dark:hover:bg-dark-brand-accent-hover text-brand-text-on-accent dark:text-dark-brand-text-on-accent text-xs font-semibold px-4 py-2 rounded-md shadow-sm hover:shadow-md transition-all duration-200"> Buy Now </a>`;
       tr.appendChild(retailerCell); tr.appendChild(priceCell); tr.appendChild(actionCell); tbody.appendChild(tr);
   });
   console.log("Price comparison section updated.");
}

// --- MODIFIED Price History Section ---
function createPriceHistorySection(containerElement, historyString, currency) {
   if (!containerElement || !historyString || typeof historyString !== 'string') return;
   console.log("Creating price history section");

   let chartData = [];
   let maxPrice = 0, lowestPrice = Infinity, sumPriceAllTime = 0, countPriceAllTime = 0;
   let predictionScore = null;

   try {
       // --- Parsing Logic (same as before) ---
       const parts = historyString.split('&~&~');
       const priceHistoryPart = parts[0];
       const predictionScoreString = parts.length > 1 ? parts[parts.length - 2] : null;
       predictionScore = parseInt(predictionScoreString, 10);

       if (priceHistoryPart.includes('~')) {
           const priceRecords = priceHistoryPart.split('*~*');
           priceRecords.forEach(record => {
               if (!record || !record.includes('~')) return;
               const [dateStr, priceStr] = record.split('~');
               if (!dateStr || !priceStr) return;
               const price = parseFloat(priceStr);
               const timestamp = new Date(dateStr.replace(' ', 'T') + 'Z').getTime();
               if (!isNaN(price) && price > 0 && !isNaN(timestamp)) {
                   chartData.push([timestamp, price]);
                   if (price > maxPrice) maxPrice = price;
                   if (price < lowestPrice) lowestPrice = price;
                   sumPriceAllTime += price;
                   countPriceAllTime++;
               }
           });
       }
   } catch (error) {
       console.error("Error parsing history data:", error);
       return;
   }

   if (chartData.length < 2) {
       console.log("Not enough price history data points for chart.");
       return;
   }

   let section = document.getElementById('price-history-section');
   if (!section) {
       section = document.createElement('div');
       section.id = 'price-history-section';
       section.className = 'p-4 md:p-6 border-t border-brand-border/30 dark:border-dark-brand-border/50 bg-brand-header dark:bg-dark-brand-header rounded-b-xl md:rounded-none section-transition';
       section.innerHTML = `
           <h3 class="text-xl font-semibold mb-4 text-brand-text-primary dark:text-dark-brand-text-primary">Price History</h3>
           <div class="grid grid-cols-2 md:grid-cols-3 gap-3 md:gap-4 mb-6">
               <div class="bg-brand-bg-light/50 dark:bg-dark-brand-bg-light/60 p-3 rounded-lg shadow-sm border border-brand-border/20 dark:border-dark-brand-border/30 text-center md:text-left">
                   <p class="text-xs text-brand-text-secondary dark:text-dark-brand-text-secondary mb-0.5">Lowest Price</p>
                   <p class="text-lg md:text-xl font-bold text-brand-accent dark:text-dark-brand-accent" id="lowest-price"></p>
               </div>
               <div class="bg-brand-bg-light/50 dark:bg-dark-brand-bg-light/60 p-3 rounded-lg shadow-sm border border-brand-border/20 dark:border-dark-brand-border/30 text-center md:text-left">
                   <p class="text-xs text-brand-text-secondary dark:text-dark-brand-text-secondary mb-0.5">Highest Price</p>
                   <p class="text-lg md:text-xl font-bold text-brand-text-primary dark:text-dark-brand-text-primary" id="highest-price"></p>
               </div>
               <div class="bg-brand-bg-light/50 dark:bg-dark-brand-bg-light/60 p-3 rounded-lg shadow-sm border border-brand-border/20 dark:border-dark-brand-border/30 text-center md:text-left col-span-2 md:col-span-1">
                   <p class="text-xs text-brand-text-secondary dark:text-dark-brand-text-secondary mb-0.5">Average Price</p>
                   <p class="text-lg md:text-xl font-bold text-brand-text-primary dark:text-dark-brand-text-primary" id="average-price"></p>
               </div>
           </div>
           <div class="bg-white dark:bg-dark-brand-bg-light/30 p-3 sm:p-4 rounded-lg shadow-inner border border-brand-border/20 dark:border-dark-brand-border/40">
               <div id="price-chart" style="height: 300px;"></div>
           </div>
           <div id="buying-suggestion-container" class="mt-4"></div>`;
       containerElement.appendChild(section);
   }

   // Update stats
   document.getElementById('lowest-price').textContent = lowestPrice !== Infinity ? formatPrice(lowestPrice, currency) : 'N/A';
   document.getElementById('highest-price').textContent = maxPrice > 0 ? formatPrice(maxPrice, currency) : 'N/A';
   document.getElementById('average-price').textContent = countPriceAllTime > 0 ? formatPrice(Math.round(sumPriceAllTime / countPriceAllTime), currency) : 'N/A';

   chartData.sort((a, b) => a[0] - b[0]);

   // Render Chart with Theming
   if (typeof Highcharts !== 'undefined') {
       setTimeout(() => {
           try {
               // **** START HIGHCHARTS THEME FIX ****
               const isDarkMode = document.documentElement.classList.contains('dark');

               const chartOptions = {
                   chart: {
                       type: 'areaspline',
                       zoomType: 'x',
                       // Set background based on theme
                       backgroundColor: isDarkMode ? '#242424' : '#FAF6F2' // dark-brand-header / brand-header
                   },
                   title: { text: null },
                   credits: { enabled: false },
                   xAxis: {
                       type: 'datetime',
                       labels: {
                           format: '{value:%b %d}',
                           // Set label color based on theme
                           style: { color: isDarkMode ? '#A89A8B' : '#93785B' } // dark/light secondary text
                       },
                        // Set line/tick color based on theme
                       lineColor: isDarkMode ? '#5A5A5A' : '#AC8968', // dark/light border
                       tickColor: isDarkMode ? '#5A5A5A' : '#AC8968'
                   },
                   yAxis: {
                       title: { text: null },
                       labels: {
                           formatter: function() { return currency + this.value.toLocaleString('en-IN', { maximumFractionDigits: 0 }); },
                            // Set label color based on theme
                           style: { color: isDarkMode ? '#A89A8B' : '#93785B' }
                       },
                       // Set grid line color based on theme
                       gridLineColor: isDarkMode ? 'rgba(90, 90, 90, 0.5)' : 'rgba(172, 137, 104, 0.3)' // dark/light border with opacity
                   },
                   legend: { enabled: false },
                   tooltip: {
                       shared: true,
                       useHTML: true,
                       headerFormat: '<span style="font-size: 10px">{point.key:%A, %b %d, %Y}</span><br/>',
                       pointFormat: `<span style="color:{point.color}">●</span> Price: <b>${currency}{point.y:,.0f}</b><br/>`,
                        // Set tooltip style based on theme
                       backgroundColor: isDarkMode ? '#181818' : '#FFFFFF', // dark/light bg
                       borderColor: isDarkMode ? '#5A5A5A' : '#AC8968',    // dark/light border
                       style: { color: isDarkMode ? '#EAEAEA' : '#3E362E' } // dark/light primary text
                   },
                   plotOptions: {
                       areaspline: {
                            // Set area fill/line color based on theme
                           fillColor: {
                               linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                               stops: [
                                   [0, isDarkMode ? 'rgba(193, 154, 107, 0.5)' : 'rgba(134, 93, 54, 0.5)'], // dark/light accent with opacity
                                   [1, isDarkMode ? 'rgba(193, 154, 107, 0.05)' : 'rgba(134, 93, 54, 0.05)'] // Faded accent
                               ]
                           },
                           lineColor: isDarkMode ? '#C19A6B' : '#865D36', // dark/light accent
                           lineWidth: 2,
                           marker: {
                               enabled: false,
                               // Optional: Style markers if enabled
                               // fillColor: isDarkMode ? '#C19A6B' : '#865D36',
                               // lineColor: isDarkMode ? '#C19A6B' : '#865D36'
                           },
                           states: { hover: { lineWidth: 2.5 } }, // Slightly thicker on hover
                           threshold: null
                       }
                   },
                   series: [{
                       name: 'Price',
                       data: chartData,
                       // Ensure series color matches plotOptions
                       color: isDarkMode ? '#C19A6B' : '#865D36'
                   }]
               };
               // **** END HIGHCHARTS THEME FIX ****

               Highcharts.chart('price-chart', chartOptions); // Use the constructed options
               console.log("Price history chart rendered.");
           } catch (chartError) { console.error("Error rendering price history chart:", chartError); document.getElementById('price-chart').innerHTML = '<p class="text-center text-red-500 dark:text-red-400 py-6">Error rendering chart.</p>'; }
       }, 100);
   } else { console.warn("Highcharts library not available."); document.getElementById('price-chart').innerHTML = '<p class="text-center text-gray-500 dark:text-gray-400 py-6">Chart library not loaded.</p>'; }

   // Create Buying Suggestion
   createBuyingSuggestion(document.getElementById('buying-suggestion-container'), chartData, predictionScore, currency);

   console.log("Price history section updated.");
}


function createBuyingSuggestion(containerElement, chartData, predictionScore, currency) {
   // ... (Keep the dark mode classes added in the previous step) ...
    if (!containerElement) return;

   let suggestionText = '';
   let suggestionClass = '';
   let suggestionIcon = '';
   const currentPriceDataPoint = chartData.length > 0 ? chartData[chartData.length - 1] : null;
   const currentPrice = currentPriceDataPoint ? currentPriceDataPoint[1] : null;
   const lowestPrice = Math.min(...chartData.map(p => p[1]));
   const avgPrice = chartData.reduce((sum, p) => sum + p[1], 0) / chartData.length;

   if (currentPrice !== null) {
       const currentFormatted = formatPrice(currentPrice, currency);
       const lowestFormatted = formatPrice(lowestPrice, currency);
       const avgFormatted = formatPrice(Math.round(avgPrice), currency);

       if (currentPrice <= lowestPrice * 1.05) {
            suggestionText = `Price (${currentFormatted}) is near its lowest (${lowestFormatted}). Great time to buy!`;
            suggestionClass = 'bg-green-100 border-green-200 text-green-800 dark:bg-green-900/30 dark:border-green-700/50 dark:text-green-300';
            suggestionIcon = '<i class="fas fa-check-circle mr-2"></i>';
       } else if (currentPrice < avgPrice) {
           suggestionText = `Price (${currentFormatted}) is below average (${avgFormatted}). Good time to consider buying.`;
           suggestionClass = 'bg-yellow-100 border-yellow-200 text-yellow-800 dark:bg-yellow-900/30 dark:border-yellow-700/50 dark:text-yellow-300';
           suggestionIcon = '<i class="fas fa-thumbs-up mr-2"></i>';
       } else if (currentPrice <= avgPrice * 1.15) {
            suggestionText = `Price (${currentFormatted}) is slightly above average (${avgFormatted}). Maybe wait for a drop.`;
            suggestionClass = 'bg-orange-100 border-orange-200 text-orange-800 dark:bg-orange-900/30 dark:border-orange-700/50 dark:text-orange-300';
            suggestionIcon = '<i class="fas fa-clock mr-2"></i>';
       } else {
           suggestionText = `Price (${currentFormatted}) is significantly above average (${avgFormatted}). Better to wait.`;
           suggestionClass = 'bg-red-100 border-red-200 text-red-800 dark:bg-red-900/30 dark:border-red-700/50 dark:text-red-300';
           suggestionIcon = '<i class="fas fa-exclamation-triangle mr-2"></i>';
       }
   } else {
       suggestionText = "Not enough data for a buying suggestion. Check the price graph.";
       suggestionClass = 'bg-blue-100 border-blue-200 text-blue-800 dark:bg-blue-900/30 dark:border-blue-700/50 dark:text-blue-300';
       suggestionIcon = '<i class="fas fa-info-circle mr-2"></i>';
   }

   containerElement.innerHTML = `<div class="p-4 rounded-lg border shadow-sm ${suggestionClass} flex items-center text-sm"> <span class="flex-shrink-0 w-5 h-5">${suggestionIcon}</span> <span class="font-medium ml-2">${suggestionText}</span> </div>`;
   console.log("Buying suggestion created.");
}

function createSimilarProductsSection(containerElement, similarProducts, currency) {
   // ... (Keep the dark mode classes added in the previous step) ...
    if (!containerElement || !similarProducts || similarProducts.length === 0) return;
   console.log("Creating similar products section with products:", similarProducts);

   let section = document.getElementById('similar-products-section');
   if (!section) {
       section = document.createElement('div');
       section.id = 'similar-products-section';
       section.className = 'p-4 md:p-6 border-t border-brand-border/30 dark:border-dark-brand-border/50 section-transition';
       section.innerHTML = `<h3 class="text-xl font-semibold mb-4 text-brand-text-primary dark:text-dark-brand-text-primary">Similar Products</h3> <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4" id="similar-products-grid"></div>`;
       containerElement.appendChild(section);
   }

   const grid = section.querySelector('#similar-products-grid');
   if (!grid) return;
   grid.innerHTML = '';

   similarProducts.slice(0, 10).forEach(product => {
       if (!product || typeof product !== 'object') return;
       const similarPrice = getSafe(product, 'cur_price', 0);
       if (Number(similarPrice) <= 0) return;
       const div = document.createElement('div');
       div.className = 'bg-white dark:bg-dark-brand-header rounded-lg overflow-hidden shadow-card dark:shadow-dark-card hover:shadow-lg dark:hover:shadow-dark-hover border border-brand-border/20 dark:border-dark-brand-border/50 transition-all duration-200 transform hover:-translate-y-1 group';
       const similarTitle = getSafe(product, 'name', 'Similar Product');
       const similarLastPrice = getSafe(product, 'last_price', 0);
       let strikeHTML = '';
       if (similarLastPrice > similarPrice) {
           strikeHTML = `<span class="text-gray-400 dark:text-gray-500 line-through ml-2 text-xs">${formatPrice(similarLastPrice, currency)}</span>`;
       }
       div.innerHTML = `<a href="${getSafe(product, 'link', '#')}" target="_blank" rel="noopener noreferrer" class="block"> <div class="relative aspect-square bg-gray-100 dark:bg-gray-700/50 flex items-center justify-center p-2 overflow-hidden"> <img src="${getSafe(product, 'image', 'https://placehold.co/150/DBCFBF/3E362E?text=N/A')}" alt="${similarTitle}" loading="lazy" onerror="this.onerror=null; this.src='https://placehold.co/150/DBCFBF/3E362E?text=Error';" class="max-h-full max-w-full object-contain group-hover:scale-105 transition-transform duration-300"> </div> <div class="px-3 pt-2 pb-3"> <h4 class="text-brand-text-primary dark:text-dark-brand-text-primary font-medium text-sm mb-1 h-10 line-clamp-2 leading-tight" title="${similarTitle}"> ${similarTitle} </h4> <div class="flex items-baseline flex-wrap"> <span class="text-brand-accent dark:text-dark-brand-accent font-bold text-base">${formatPrice(similarPrice, currency)}</span> ${strikeHTML} </div> </div> </a>`;
       grid.appendChild(div);
   });
   console.log(`Similar products section updated.`);
}

function createDittoProductsSection(containerElement, dittoProducts, currency) {
   // ... (Keep the dark mode classes added in the previous step) ...
   if (!containerElement || !dittoProducts || dittoProducts.length === 0) return;
   console.log("Creating ditto products section with products:", dittoProducts);

   let section = document.getElementById('ditto-products-section');
   if (!section) {
       section = document.createElement('div');
       section.id = 'ditto-products-section';
       section.className = 'p-4 md:p-6 border-t border-brand-border/30 dark:border-dark-brand-border/50 section-transition';
       section.innerHTML = `<h3 class="text-xl font-semibold mb-4 text-brand-text-primary dark:text-dark-brand-text-primary">Same Product on Other Stores</h3> <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4" id="ditto-products-grid"></div>`;
       containerElement.appendChild(section);
   }

   const grid = section.querySelector('#ditto-products-grid');
   if (!grid) return;
   grid.innerHTML = '';

   dittoProducts.slice(0, 10).forEach(product => {
       if (!product || typeof product !== 'object') return;
       const dittoPrice = getSafe(product, 'price', 0);
       if (Number(dittoPrice) <= 0) return;
       const div = document.createElement('div');
       div.className = 'bg-white dark:bg-dark-brand-header rounded-lg overflow-hidden shadow-card dark:shadow-dark-card hover:shadow-lg dark:hover:shadow-dark-hover border border-brand-border/20 dark:border-dark-brand-border/50 transition-all duration-200 transform hover:-translate-y-1 group';
       const dittoTitle = getSafe(product, 'prod', 'Identical Product');
       const dittoStore = getSafe(product, 'site_name', 'Store');
       const dittoLink = getSafe(product, 'prodUrl', '#');
       const dittoImage = getSafe(product, 'imageUrl', 'https://placehold.co/150/DBCFBF/3E362E?text=N/A');
       const dittoStoreLogo = getSafe(product, 'logo', '');
       div.innerHTML = `<a href="${dittoLink}" target="_blank" rel="noopener noreferrer" class="block"> <div class="relative aspect-square bg-gray-100 dark:bg-gray-700/50 flex items-center justify-center p-2 overflow-hidden"> <img src="${dittoImage}" alt="${dittoTitle}" loading="lazy" onerror="this.onerror=null; this.src='https://placehold.co/150/DBCFBF/3E362E?text=Error';" class="max-h-full max-w-full object-contain group-hover:scale-105 transition-transform duration-300"> </div> <div class="px-3 pt-2 pb-3"> <h4 class="text-brand-text-primary dark:text-dark-brand-text-primary font-medium text-sm mb-1 h-10 line-clamp-2 leading-tight" title="${dittoTitle}"> ${dittoTitle} </h4> <div class="flex items-center mt-1 text-xs text-brand-text-secondary dark:text-dark-brand-text-secondary"> ${dittoStoreLogo ? `<img src="${dittoStoreLogo}" alt="${dittoStore}" class="h-4 w-auto mr-1.5 object-contain rounded-sm">` : ''} <span class="truncate">From ${dittoStore}</span> </div> <div class="mt-1 font-bold text-brand-accent dark:text-dark-brand-accent text-base">${formatPrice(dittoPrice, currency)}</div> </div> </a>`;
       grid.appendChild(div);
   });
    console.log(`Ditto products section updated.`);
}

// --- Main Parsing Function ---
function parseAndDisplayAdditionalData(nodes, containerId, currencySymbol) {
   const containerElement = document.getElementById(containerId);
   if (!containerElement) {
       console.error(`Container element with ID '${containerId}' not found.`);
       return;
   }
   containerElement.innerHTML = ''; // Clear previous content like errors

   console.log("Parsing additional data nodes:", nodes);

   try {
       const dataNode = nodes.find(node => node?.type === 'data' && Array.isArray(node?.data));
       if (!dataNode || !dataNode.data || dataNode.data.length === 0) {
           console.error("No valid data node found in the additional data nodes.");
           containerElement.innerHTML = '<p class="text-center text-sm text-brand-text-secondary dark:text-dark-brand-text-secondary p-4">No additional product details available.</p>';
           return;
       }

       const lookupTable = dataNode.data;
       const visitedRefs = new Set();

       function deReference(data) { /* ... (keep exact dereference logic) ... */
           if (typeof data === 'number' && lookupTable && data >= 0 && data < lookupTable.length) {
               if (visitedRefs.has(data)) return `[Circular Ref: ${data}]`;
               visitedRefs.add(data);
               const referencedValue = lookupTable[data];
               if (referencedValue === null) { visitedRefs.delete(data); return null; }
               const result = deReference(referencedValue);
               visitedRefs.delete(data);
               return result;
           } else if (Array.isArray(data)) { return data.map(item => deReference(item));
           } else if (typeof data === 'object' && data !== null) {
               const newObj = {};
               for (const key in data) { if (Object.prototype.hasOwnProperty.call(data, key)) newObj[key] = deReference(data[key]); }
               return newObj;
           }
           return data;
        }

       let pageData = null;
       // Improved logic to find pageData object
       for(let i = 0; i < Math.min(lookupTable.length, 5); i++) {
            if(lookupTable[i] && typeof lookupTable[i] === 'object' && lookupTable[i].productData !== undefined) {
                pageData = deReference(lookupTable[i]);
                break;
            }
       }

       if (!pageData) {
            console.error("Could not find main page data object within lookup table.");
            containerElement.innerHTML = '<p class="text-center text-sm text-brand-text-secondary dark:text-dark-brand-text-secondary p-4">Could not parse additional product details.</p>';
            return;
       }
       console.log("De-referenced additional page data:", pageData);

       // Extract data
       const dealsData = getSafe(pageData, 'dealsData', []);
       const similarProducts = getSafe(pageData, 'similarProducts', []);
       const dittoProducts = getSafe(pageData, 'dittoProducts', []);
       const historyString = getSafe(pageData, 'predictedData', ''); // History string

       // Create sections, passing the correct container *element*
       createPriceComparisonSection(containerElement, dealsData, currencySymbol);
       createPriceHistorySection(containerElement, historyString, currencySymbol);
       createSimilarProductsSection(containerElement, similarProducts, currencySymbol);
       createDittoProductsSection(containerElement, dittoProducts, currencySymbol);

       console.log("Additional data display complete.");

   } catch (error) {
       console.error("Error parsing or displaying additional data:", error);
       containerElement.innerHTML = `<p class="text-center text-red-500 dark:text-red-400 p-4">Error displaying additional details: ${error.message}</p>`;
   }
}