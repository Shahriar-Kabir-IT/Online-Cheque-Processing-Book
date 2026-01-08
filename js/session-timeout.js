/**
 * Auto-logout on inactivity (30 minutes)
 * Tracks user activity and automatically logs out after 30 minutes of inactivity
 */

(function() {
    'use strict';
    
    const TIMEOUT_MINUTES = 30; // 30 minutes
    const TIMEOUT_MS = TIMEOUT_MINUTES * 60 * 1000;
    const WARNING_MINUTES = 5; // Show warning 5 minutes before timeout
    const WARNING_MS = WARNING_MINUTES * 60 * 1000;
    const REFRESH_INTERVAL = 60000; // Refresh session every 60 seconds
    
    let warningShown = false;
    let lastActivity = Date.now();
    let warningTimer = null;
    let logoutTimer = null;
    let refreshTimer = null;
    
    // Activity events
    const activityEvents = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'];
    
    // Track user activity
    function updateActivity() {
        lastActivity = Date.now();
        if (!warningShown) {
            resetTimers();
        }
    }
    
    // Add event listeners for activity
    activityEvents.forEach(function(event) {
        document.addEventListener(event, updateActivity, true);
    });
    
    // Reset all timers
    function resetTimers() {
        clearTimeout(warningTimer);
        clearTimeout(logoutTimer);
        
        const timeSinceActivity = Date.now() - lastActivity;
        const timeUntilWarning = TIMEOUT_MS - WARNING_MS - timeSinceActivity;
        const timeUntilLogout = TIMEOUT_MS - timeSinceActivity;
        
        // Set warning timer
        if (timeUntilWarning > 0) {
            warningTimer = setTimeout(showWarning, timeUntilWarning);
        }
        
        // Set logout timer
        if (timeUntilLogout > 0) {
            logoutTimer = setTimeout(logout, timeUntilLogout);
        }
    }
    
    // Show warning dialog
    function showWarning() {
        warningShown = true;
        const remainingMinutes = Math.ceil((TIMEOUT_MS - (Date.now() - lastActivity)) / 60000);
        
        // Create warning modal
        const modal = document.createElement('div');
        modal.id = 'session-warning-modal';
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
        `;
        
        const content = document.createElement('div');
        content.style.cssText = `
            background: white;
            padding: 30px;
            border-radius: 8px;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        `;
        
        content.innerHTML = `
            <h2 style="color: #f59e0b; margin-top: 0;">Session Timeout Warning</h2>
            <p style="font-size: 16px; margin: 20px 0;">
                Your session will expire in <strong style="color: #ef4444;">${remainingMinutes} minute(s)</strong> due to inactivity.
            </p>
            <p style="color: #64748b; font-size: 14px; margin-bottom: 20px;">
                Click "Continue" to stay logged in.
            </p>
            <button id="continue-session" style="
                background: #2563eb;
                color: white;
                border: none;
                padding: 12px 30px;
                border-radius: 6px;
                font-size: 16px;
                cursor: pointer;
                font-weight: 500;
            ">Continue Session</button>
        `;
        
        modal.appendChild(content);
        document.body.appendChild(modal);
        
        // Continue button handler
        document.getElementById('continue-session').addEventListener('click', function() {
            warningShown = false;
            lastActivity = Date.now();
            refreshSession();
            document.body.removeChild(modal);
            resetTimers();
        });
        
        // Update countdown every second
        const countdownInterval = setInterval(function() {
            const remaining = Math.ceil((TIMEOUT_MS - (Date.now() - lastActivity)) / 60000);
            if (remaining <= 0) {
                clearInterval(countdownInterval);
                return;
            }
            const strongEl = content.querySelector('strong');
            if (strongEl) {
                strongEl.textContent = remaining + ' minute(s)';
            }
        }, 1000);
    }
    
    // Logout function
    function logout() {
        // Clear all timers
        clearTimeout(warningTimer);
        clearTimeout(logoutTimer);
        clearInterval(refreshTimer);
        
        // Show logout message
        const modal = document.createElement('div');
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
        `;
        
        const content = document.createElement('div');
        content.style.cssText = `
            background: white;
            padding: 30px;
            border-radius: 8px;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        `;
        
        content.innerHTML = `
            <h2 style="color: #ef4444; margin-top: 0;">Session Expired</h2>
            <p style="font-size: 16px; margin: 20px 0;">
                You have been logged out due to inactivity.
            </p>
            <p style="color: #64748b; font-size: 14px;">
                Redirecting to login page...
            </p>
        `;
        
        modal.appendChild(content);
        document.body.appendChild(modal);
        
        // Redirect after 2 seconds
        setTimeout(function() {
            window.location.href = 'login.php?msg=timeout';
        }, 2000);
    }
    
    // Refresh session via AJAX
    function refreshSession() {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'session_refresh.php', true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.status === 'timeout') {
                            logout();
                        }
                    } catch (e) {
                        // Ignore JSON parse errors
                    }
                } else if (xhr.status === 401) {
                    logout();
                }
            }
        };
        xhr.send();
    }
    
    // Periodically refresh session
    function startSessionRefresh() {
        refreshTimer = setInterval(function() {
            if (Date.now() - lastActivity < TIMEOUT_MS) {
                refreshSession();
            }
        }, REFRESH_INTERVAL);
    }
    
    // Initialize on page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            resetTimers();
            startSessionRefresh();
        });
    } else {
        resetTimers();
        startSessionRefresh();
    }
    
    // Handle page visibility (tab focus/blur)
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            // Page is hidden, don't update activity
        } else {
            // Page is visible again, check if we need to refresh
            const timeSinceActivity = Date.now() - lastActivity;
            if (timeSinceActivity < TIMEOUT_MS) {
                refreshSession();
            } else {
                logout();
            }
        }
    });
    
})();
