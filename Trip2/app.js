/**
 * Trip Scout - Travel Destination Explorer
 * Integrates with Open-Meteo, OpenStreetMap, and video platforms
 * 
 * @version 2.0.0
 * @author Trip Scout Team
 */

// ==================== Configuration ====================
const CONFIG = {
  API: {
    NOMINATIM: 'https://nominatim.openstreetmap.org',
    OPEN_METEO: 'https://api.open-meteo.com/v1/forecast',
    OVERPASS: 'https://overpass-api.de/api/interpreter',
  },
  SEARCH_RADIUS: 5000, // meters
  HOTEL_LIMIT: 10,
  TIMEOUT: 10000, // milliseconds
  MAX_HISTORY: 5, // Maximum number of recent searches to store
  selectedPlatform: 'youtube', // Default selected platform
};

// ==================== DOM Elements ====================
const DOM = {
  searchForm: document.getElementById('search-form'),
  destinationInput: document.getElementById('destination'),
  formStatus: document.getElementById('form-status'),
  recentSearches: document.getElementById('recent-searches'),
  recentItems: document.getElementById('recent-items'),
  
  // Result containers
  videoLinks: document.getElementById('video-links'),
  weatherContent: document.getElementById('weather-content'),
  hotelList: document.getElementById('hotel-list'),
  tripStartDate: document.getElementById('trip-start-date'),
  tripEndDate: document.getElementById('trip-end-date'),
};

// ==================== Weather Code Mapping ====================
const WEATHER_CODES = {
  0: { desc: 'Clear sky', icon: '☀️' },
  1: { desc: 'Mainly clear', icon: '🌤️' },
  2: { desc: 'Partly cloudy', icon: '⛅' },
  3: { desc: 'Overcast', icon: '☁️' },
  45: { desc: 'Foggy', icon: '🌫️' },
  48: { desc: 'Depositing rime fog', icon: '🌫️' },
  51: { desc: 'Light drizzle', icon: '🌦️' },
  53: { desc: 'Drizzle', icon: '🌦️' },
  55: { desc: 'Dense drizzle', icon: '🌧️' },
  61: { desc: 'Slight rain', icon: '🌧️' },
  63: { desc: 'Rain', icon: '🌧️' },
  65: { desc: 'Heavy rain', icon: '⛈️' },
  71: { desc: 'Slight snow', icon: '🌨️' },
  73: { desc: 'Snow', icon: '❄️' },
  75: { desc: 'Heavy snow', icon: '⛄' },
  80: { desc: 'Rain showers', icon: '🌧️' },
  81: { desc: 'Heavy rain showers', icon: '⛈️' },
  82: { desc: 'Violent rain showers', icon: '⛈️' },
  95: { desc: 'Thunderstorm', icon: '⛈️' },
  96: { desc: 'Thunderstorm with hail', icon: '⛈️' },
  99: { desc: 'Thunderstorm with heavy hail', icon: '⛈️' },
};

// ==================== Initialization ====================
document.addEventListener('DOMContentLoaded', () => {
  setupEventListeners();
  setupNavigation();
  loadSearchHistory();
});

function setupEventListeners() {
  if (DOM.searchForm) {
    DOM.searchForm.addEventListener('submit', handleSearch);
  }

  // Platform button selection
  const platformBtns = document.querySelectorAll('.platform-btn');
  platformBtns.forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      // Remove active class from all buttons
      platformBtns.forEach(b => b.classList.remove('active'));
      // Add active class to clicked button
      btn.classList.add('active');
      // Update selected platform
      CONFIG.selectedPlatform = btn.dataset.platform;
    });
  });
}

function setupNavigation() {
  const navToggle = document.getElementById('nav-toggle');
  const navMenu = document.querySelector('.nav-menu');
  const navLinks = document.querySelectorAll('.nav-link');
  const themeToggle = document.getElementById('theme-toggle');

  if (navToggle && navMenu) {
    navToggle.addEventListener('click', () => {
      navMenu.classList.toggle('active');
    });

    // Close menu when a link is clicked
    navLinks.forEach(link => {
      link.addEventListener('click', () => {
        navMenu.classList.remove('active');
      });
    });

    // Close menu when clicking outside
    document.addEventListener('click', (e) => {
      if (!e.target.closest('.navbar-container')) {
        navMenu.classList.remove('active');
      }
    });
  }

  // Theme toggle functionality
  if (themeToggle) {
    // Check for saved theme preference or default to light mode
    const savedTheme = localStorage.getItem('theme') || 'light';
    if (savedTheme === 'dark') {
      document.documentElement.classList.add('dark-mode');
      themeToggle.textContent = '☀️';
    }

    themeToggle.addEventListener('click', () => {
      const isDarkMode = document.documentElement.classList.toggle('dark-mode');
      const theme = isDarkMode ? 'dark' : 'light';
      localStorage.setItem('theme', theme);
      themeToggle.textContent = isDarkMode ? '☀️' : '🌙';
    });
  }

  // Hide navbar on scroll down, show on scroll up
  setupScrollNavigation();
}

/**
 * Setup navbar hide/show on scroll
 */
function setupScrollNavigation() {
  const navbar = document.querySelector('.navbar');
  let lastScrollTop = 0;

  window.addEventListener('scroll', () => {
    const currentScroll = window.pageYOffset || document.documentElement.scrollTop;
    
    if (currentScroll > lastScrollTop && currentScroll > 100) {
      // Scrolling down
      navbar.classList.add('hide');
    } else {
      // Scrolling up
      navbar.classList.remove('hide');
    }
    
    lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
  });
}

// ==================== Main Search Handler ====================
async function handleSearch(event) {
  event.preventDefault();

  const destination = DOM.destinationInput?.value?.trim();
  if (!destination) {
    showFormStatus('Please enter a destination', 'error');
    return;
  }

  // Add to search history
  addToSearchHistory(destination);

  // Clear previous results
  clearResults();
  showFormStatus('Searching...', 'loading');

  try {
    // 1. Show video search links after a small delay to ensure loading indicator is visible
    setTimeout(() => displayVideoLinks(destination), 100);

    // 2. Geocode destination
    const coords = await geocodeDestination(destination);
    if (!coords) {
      showFormStatus('Location not found. Please try another destination.', 'error');
      showEmptyState(DOM.weatherContent, 'Location not found');
      showEmptyState(DOM.hotelList, 'Location not found');
      return;
    }

    // 3. Fetch and display weather and hotels in parallel
    const [weatherSuccess, hotelsSuccess] = await Promise.all([
      fetchAndDisplayWeather(coords),
      fetchAndDisplayHotels(coords),
    ]);

    if (weatherSuccess || hotelsSuccess) {
      showFormStatus('Results loaded successfully', 'success');
      setTimeout(() => clearFormStatus(), 3000);
    } else {
      showFormStatus('Some results could not be loaded. Please try again.', 'error');
    }
  } catch (error) {
    console.error('Search error:', error);
    showFormStatus('An error occurred. Please try again.', 'error');
  }
}

// ==================== API Functions ====================

/**
 * Geocode a destination using OpenStreetMap Nominatim
 * @param {string} destination - The destination to search for
 * @returns {Promise<Object|null>} Coordinates object or null
 */
async function geocodeDestination(destination) {
  try {
    const controller = new AbortController();
    const timeout = setTimeout(() => controller.abort(), CONFIG.TIMEOUT);

    const response = await fetch(
      `${CONFIG.API.NOMINATIM}/search?format=json&q=${encodeURIComponent(destination)}&limit=1`,
      {
        headers: { 'Accept-Language': 'en' },
        signal: controller.signal,
      }
    );

    clearTimeout(timeout);

    if (!response.ok) throw new Error('Geocoding failed');

    const data = await response.json();
    if (!data || data.length === 0) return null;

    return {
      lat: parseFloat(data[0].lat),
      lon: parseFloat(data[0].lon),
      name: data[0].display_name,
    };
  } catch (error) {
    console.error('Geocoding error:', error);
    return null;
  }
}

/**
 * Fetch weather data from Open-Meteo API
 * @param {number} lat - Latitude
 * @param {number} lon - Longitude
 * @returns {Promise<Object|null>} Weather data or null
 */
async function fetchWeatherData(lat, lon) {
  try {
    const controller = new AbortController();
    const timeout = setTimeout(() => controller.abort(), CONFIG.TIMEOUT);

    const response = await fetch(
      `${CONFIG.API.OPEN_METEO}?latitude=${lat}&longitude=${lon}&current_weather=true&daily=temperature_2m_max,temperature_2m_min,weathercode,precipitation_sum&timezone=auto&forecast_days=7`,
      { signal: controller.signal }
    );

    clearTimeout(timeout);

    if (!response.ok) throw new Error('Weather API failed');

    return await response.json();
  } catch (error) {
    console.error('Weather fetch error:', error);
    return null;
  }
}

/**
 * Fetch hotels from Overpass API
 * @param {number} lat - Latitude
 * @param {number} lon - Longitude
 * @returns {Promise<Object|null>} Hotel data or null
 */
async function fetchHotels(lat, lon) {
  try {
    const query = `
      [out:json][timeout:25];
      (
        node["tourism"="hotel"](around:${CONFIG.SEARCH_RADIUS},${lat},${lon});
        way["tourism"="hotel"](around:${CONFIG.SEARCH_RADIUS},${lat},${lon});
        relation["tourism"="hotel"](around:${CONFIG.SEARCH_RADIUS},${lat},${lon});
      );
      out center ${CONFIG.HOTEL_LIMIT};
    `;

    const controller = new AbortController();
    const timeout = setTimeout(() => controller.abort(), CONFIG.TIMEOUT);

    const response = await fetch(CONFIG.API.OVERPASS, {
      method: 'POST',
      body: query,
      headers: { 'Content-Type': 'text/plain' },
      signal: controller.signal,
    });

    clearTimeout(timeout);

    if (!response.ok) throw new Error('Hotels API failed');

    return await response.json();
  } catch (error) {
    console.error('Hotels fetch error:', error);
    return null;
  }
}

// ==================== Display Functions ====================

/**
 * Display video platform search links
 * @param {string} destination - The destination to display links for
 */
function displayVideoLinks(destination) {
  if (!DOM.videoLinks) return;

  const platforms = {
    youtube: {
      name: '▶️ YouTube',
      url: `https://www.youtube.com/results?search_query=${encodeURIComponent(destination + ' travel guide')}`,
    },
    tiktok: {
      name: '🎵 TikTok',
      url: `https://www.tiktok.com/search?q=${encodeURIComponent(destination)}`,
    },
    instagram: {
      name: '📸 Instagram',
      url: `https://www.instagram.com/explore/tags/${encodeURIComponent(destination.replace(/\s+/g, ''))}`,
    },
  };

  // Get selected platform or default to YouTube
  const selectedPlatformKey = CONFIG.selectedPlatform || 'youtube';
  const selectedPlatform = platforms[selectedPlatformKey];

  if (selectedPlatform) {
    DOM.videoLinks.innerHTML = `<a href="${selectedPlatform.url}" target="_blank" rel="noopener noreferrer" class="video-link">${selectedPlatform.name}</a>`;
  }
}

/**
 * Fetch and display weather information
 * @param {Object} coords - Coordinates object with lat and lon
 * @returns {Promise<boolean>} Success status
 */
async function fetchAndDisplayWeather(coords) {
  if (!DOM.weatherContent) return false;

  const data = await fetchWeatherData(coords.lat, coords.lon);

  if (!data || !data.current_weather) {
    showEmptyState(DOM.weatherContent, 'Unable to fetch weather data');
    return false;
  }

  const current = data.current_weather;
  const daily = data.daily;
  
  // Get trip date range
  const startDate = DOM.tripStartDate?.value ? new Date(DOM.tripStartDate.value) : null;
  const endDate = DOM.tripEndDate?.value ? new Date(DOM.tripEndDate.value) : null;
  
  const currentWeatherInfo = WEATHER_CODES[current.weathercode] || {
    desc: 'Unknown conditions',
    icon: '❓',
  };

  const currentTemp = Math.round(current.temperature);
  const currentWind = Math.round(current.windspeed);

  // Build 7-day forecast
  let forecastHTML = `
    <div class="weather-current">
      <div class="weather-tile" style="grid-column: span 2;">
        <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">${currentWeatherInfo.icon}</div>
        <div style="font-weight: 600; margin-bottom: 0.25rem; font-size: 1.1rem;">${currentWeatherInfo.desc}</div>
        <div style="font-size: 2rem; color: var(--primary); font-weight: 700;">${currentTemp}°C</div>
        <div style="font-size: 0.9rem; color: var(--text-secondary); margin-top: 0.5rem;">Wind: ${currentWind} km/h</div>
      </div>
    </div>
    <div class="weather-grid">
  `;

  // Add 7-day forecast (or filtered forecast based on trip dates)
  if (daily && daily.time && daily.time.length > 0) {
    let forecastCount = 0;
    const maxDays = startDate && endDate ? Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1 : 7;
    
    for (let i = 0; i < Math.min(maxDays + 1, daily.time.length); i++) {
      const forecastDate = new Date(daily.time[i]);
      
      // Filter by date range if selected
      if (startDate && endDate) {
        if (forecastDate < startDate || forecastDate > endDate) {
          continue;
        }
      }
      
      const dayName = forecastDate.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' });
      const weatherCode = daily.weathercode[i];
      const weatherInfo = WEATHER_CODES[weatherCode] || { desc: 'Unknown', icon: '?' };
      const maxTemp = Math.round(daily.temperature_2m_max[i]);
      const minTemp = Math.round(daily.temperature_2m_min[i]);
      const precipitation = daily.precipitation_sum[i] || 0;

      forecastHTML += `
        <div class="weather-tile">
          <div style="font-weight: 600; margin-bottom: 0.5rem; color: var(--text-primary);">${dayName}</div>
          <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">${weatherInfo.icon}</div>
          <div style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.5rem;">${weatherInfo.desc}</div>
          <div style="font-size: 0.9rem; color: var(--primary); font-weight: 600;">${maxTemp}° / ${minTemp}°</div>
          ${precipitation > 0 ? `<div style="font-size: 0.8rem; color: var(--warning-color);">💧 ${precipitation}mm</div>` : ''}
        </div>
      `;
      
      forecastCount++;
    }
  }

  forecastHTML += `</div>`;

  DOM.weatherContent.innerHTML = forecastHTML;

  return true;
}

/**
 * Fetch and display nearby hotels
 * @param {Object} coords - Coordinates object with lat and lon
 * @returns {Promise<boolean>} Success status
 */
async function fetchAndDisplayHotels(coords) {
  if (!DOM.hotelList) return false;

  const data = await fetchHotels(coords.lat, coords.lon);

  if (!data || !data.elements || data.elements.length === 0) {
    showEmptyState(DOM.hotelList, 'No hotels found nearby');
    return false;
  }

  const hotelItems = data.elements.slice(0, CONFIG.HOTEL_LIMIT).map((hotel) => {
    const name = hotel.tags?.name || 'Unnamed Hotel';
    const osmUrl = `https://www.openstreetmap.org/${hotel.type}/${hotel.id}`;
    return `
      <li>
        <a href="${osmUrl}" target="_blank" rel="noopener noreferrer" title="View on OpenStreetMap">
          ${escapeHtml(name)}
        </a>
      </li>
    `;
  });

  DOM.hotelList.innerHTML = hotelItems.join('');
  return true;
}

// ==================== UI Utility Functions ====================

/**
 * Show form status message
 * @param {string} message - The message to display
 * @param {string} type - Message type: 'info', 'error', 'success', 'loading'
 */
function showFormStatus(message, type = 'info') {
  if (!DOM.formStatus) return;

  DOM.formStatus.textContent = message;
  DOM.formStatus.className = `form-status ${type}`;
  DOM.formStatus.setAttribute('role', 'status');
}

/**
 * Clear form status message
 */
function clearFormStatus() {
  if (DOM.formStatus) {
    DOM.formStatus.className = 'form-status';
    DOM.formStatus.textContent = '';
  }
}

/**
 * Show empty state message
 * @param {HTMLElement} container - Container element
 * @param {string} message - Message to display
 */
function showEmptyState(container, message) {
  if (!container) return;
  container.innerHTML = `<div class="empty-state"><p>${escapeHtml(message)}</p></div>`;
}

/**
 * Clear all results
 */
function clearResults() {
  const containers = [DOM.videoLinks, DOM.weatherContent, DOM.hotelList];
  containers.forEach((container) => {
    if (container) container.innerHTML = '';
  });
  clearFormStatus();
}

// ==================== Security & Utility Functions ====================

/**
 * Escape HTML to prevent XSS attacks
 * @param {string} text - Text to escape
 * @returns {string} Escaped text
 */
function escapeHtml(text) {
  const map = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;',
  };
  return String(text).replace(/[&<>"']/g, (char) => map[char]);
}

// ==================== Error Handling ====================
window.addEventListener('error', (event) => {
  console.error('Global error:', event.error);
  showFormStatus('An unexpected error occurred. Please try again.', 'error');
});

window.addEventListener('unhandledrejection', (event) => {
  console.error('Unhandled rejection:', event.reason);
  showFormStatus('An unexpected error occurred. Please try again.', 'error');
});

// ==================== Search History Management ====================

/**
 * Load search history from localStorage and display it
 */
function loadSearchHistory() {
  const history = getSearchHistory();
  if (history.length > 0 && DOM.recentSearches && DOM.recentItems) {
    displaySearchHistory(history);
    DOM.recentSearches.style.display = 'block';
  }
}

/**
 * Get search history from localStorage
 */
function getSearchHistory() {
  const history = localStorage.getItem('searchHistory');
  return history ? JSON.parse(history) : [];
}

/**
 * Add search to history
 */
function addToSearchHistory(destination) {
  let history = getSearchHistory();
  
  // Remove if already exists (to move to front)
  history = history.filter(item => item.toLowerCase() !== destination.toLowerCase());
  
  // Add to beginning
  history.unshift(destination);
  
  // Keep only the last MAX_HISTORY items
  history = history.slice(0, CONFIG.MAX_HISTORY);
  
  localStorage.setItem('searchHistory', JSON.stringify(history));
  loadSearchHistory();
}

/**
 * Remove item from search history
 */
function removeFromSearchHistory(destination) {
  let history = getSearchHistory();
  history = history.filter(item => item.toLowerCase() !== destination.toLowerCase());
  
  if (history.length === 0) {
    localStorage.removeItem('searchHistory');
    if (DOM.recentSearches) DOM.recentSearches.style.display = 'none';
  } else {
    localStorage.setItem('searchHistory', JSON.stringify(history));
  }
  
  loadSearchHistory();
}

/**
 * Display search history
 */
function displaySearchHistory(history) {
  if (!DOM.recentItems) return;
  
  DOM.recentItems.innerHTML = history
    .map(
      (item) =>
        `<div class="recent-item" onclick="searchFromHistory('${escapeHtml(item)}')">
          📍 ${escapeHtml(item)}
          <span class="remove-recent" onclick="event.stopPropagation(); removeFromHistory('${escapeHtml(item)}')">✕</span>
        </div>`
    )
    .join('');
}

/**
 * Search from history
 */
function searchFromHistory(destination) {
  if (DOM.destinationInput) {
    DOM.destinationInput.value = destination;
    const event = new Event('submit');
    DOM.searchForm?.dispatchEvent(event);
  }
}

/**
 * Remove from history (wrapper for onclick)
 */
function removeFromHistory(destination) {
  removeFromSearchHistory(destination);
}
