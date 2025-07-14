    </main>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Admin Panel Common Scripts -->
    <script>
        // Common admin panel functionality
        
        // Toast notifications
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-white bg-${type} border-0`;
            toast.setAttribute('role', 'alert');
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-${type === 'success' ? 'check' : type === 'danger' ? 'times' : 'info'}-circle me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;
            
            // Create toast container if it doesn't exist
            let toastContainer = document.getElementById('toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.id = 'toast-container';
                toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
                toastContainer.style.zIndex = '11';
                document.body.appendChild(toastContainer);
            }
            
            toastContainer.appendChild(toast);
            
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
            
            // Auto remove after shown
            toast.addEventListener('hidden.bs.toast', () => {
                toast.remove();
            });
        }
        
        // Confirm delete
        function confirmDelete(message = 'Bu öğeyi silmek istediğinizden emin misiniz?') {
            return confirm(message);
        }
        
        // AJAX helper
        function ajaxRequest(url, method = 'GET', data = null) {
            return fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: data ? JSON.stringify(data) : null
            })
            .then(response => response.json())
            .catch(error => {
                console.error('AJAX Error:', error);
                showToast('Bir hata oluştu. Lütfen tekrar deneyin.', 'danger');
            });
        }
        
        // Form validation helper
        function validateForm(formId) {
            const form = document.getElementById(formId);
            if (!form) return false;
            
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            return isValid;
        }
        
        // Auto-save functionality
        function setupAutoSave(formId, saveUrl, interval = 30000) {
            const form = document.getElementById(formId);
            if (!form) return;
            
            let saveTimer;
            let hasChanges = false;
            
            // Track changes
            form.addEventListener('input', () => {
                hasChanges = true;
                clearTimeout(saveTimer);
                saveTimer = setTimeout(() => {
                    if (hasChanges) {
                        autoSave(form, saveUrl);
                    }
                }, interval);
            });
            
            function autoSave(form, url) {
                const formData = new FormData(form);
                const data = Object.fromEntries(formData);
                
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        showToast('Otomatik kaydedildi', 'info');
                        hasChanges = false;
                    }
                })
                .catch(error => {
                    console.error('Auto-save error:', error);
                });
            }
        }
        
        // File upload with progress
        function uploadFile(fileInput, uploadUrl, onProgress, onComplete) {
            const file = fileInput.files[0];
            if (!file) return;
            
            const formData = new FormData();
            formData.append('file', file);
            
            const xhr = new XMLHttpRequest();
            
            xhr.upload.addEventListener('progress', (e) => {
                if (e.lengthComputable) {
                    const percentComplete = (e.loaded / e.total) * 100;
                    if (onProgress) onProgress(percentComplete);
                }
            });
            
            xhr.addEventListener('load', () => {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (onComplete) onComplete(response);
                } catch (error) {
                    console.error('Upload response error:', error);
                    showToast('Dosya yükleme hatası', 'danger');
                }
            });
            
            xhr.addEventListener('error', () => {
                showToast('Dosya yükleme başarısız', 'danger');
            });
            
            xhr.open('POST', uploadUrl);
            xhr.send(formData);
        }
        
        // Data table functionality
        function initDataTable(tableId, options = {}) {
            const table = document.getElementById(tableId);
            if (!table) return;
            
            // Add search functionality
            if (options.search !== false) {
                addTableSearch(table);
            }
            
            // Add sorting functionality
            if (options.sort !== false) {
                addTableSort(table);
            }
            
            // Add pagination
            if (options.pagination !== false) {
                addTablePagination(table, options.pageSize || 10);
            }
        }
        
        function addTableSearch(table) {
            const searchInput = document.createElement('input');
            searchInput.type = 'text';
            searchInput.className = 'form-control mb-3';
            searchInput.placeholder = 'Tabloda ara...';
            
            table.parentNode.insertBefore(searchInput, table);
            
            searchInput.addEventListener('input', () => {
                const searchTerm = searchInput.value.toLowerCase();
                const rows = table.querySelectorAll('tbody tr');
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });
        }
        
        function addTableSort(table) {
            const headers = table.querySelectorAll('thead th');
            
            headers.forEach((header, index) => {
                header.style.cursor = 'pointer';
                header.innerHTML += ' <i class="fas fa-sort text-muted"></i>';
                
                header.addEventListener('click', () => {
                    sortTable(table, index);
                });
            });
        }
        
        function sortTable(table, columnIndex) {
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const isAscending = !table.dataset.sortAsc || table.dataset.sortAsc === 'false';
            
            rows.sort((a, b) => {
                const aVal = a.cells[columnIndex].textContent.trim();
                const bVal = b.cells[columnIndex].textContent.trim();
                
                // Try to parse as numbers
                const aNum = parseFloat(aVal);
                const bNum = parseFloat(bVal);
                
                if (!isNaN(aNum) && !isNaN(bNum)) {
                    return isAscending ? aNum - bNum : bNum - aNum;
                }
                
                // Sort as strings
                return isAscending ? aVal.localeCompare(bVal) : bVal.localeCompare(aVal);
            });
            
            // Update sort icons
            table.querySelectorAll('thead th i').forEach(icon => {
                icon.className = 'fas fa-sort text-muted';
            });
            
            const currentHeader = table.querySelectorAll('thead th')[columnIndex];
            const icon = currentHeader.querySelector('i');
            icon.className = isAscending ? 'fas fa-sort-up text-primary' : 'fas fa-sort-down text-primary';
            
            // Reorder rows
            rows.forEach(row => tbody.appendChild(row));
            
            table.dataset.sortAsc = isAscending.toString();
        }
        
        // Modal helpers
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                const bsModal = new bootstrap.Modal(modal);
                bsModal.show();
            }
        }
        
        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                const bsModal = bootstrap.Modal.getInstance(modal);
                if (bsModal) bsModal.hide();
            }
        }
        
        // Page loading indicator
        function showPageLoader() {
            const loader = document.createElement('div');
            loader.id = 'page-loader';
            loader.className = 'position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-75';
            loader.style.zIndex = '9999';
            loader.innerHTML = `
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Yükleniyor...</span>
                </div>
            `;
            document.body.appendChild(loader);
        }
        
        function hidePageLoader() {
            const loader = document.getElementById('page-loader');
            if (loader) {
                loader.remove();
            }
        }
        
        // Initialize tooltips and popovers
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Bootstrap tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Initialize Bootstrap popovers
            const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl);
            });
            
            // Auto-hide alerts after 5 seconds
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
                alerts.forEach(alert => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl+S for save
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                const saveBtn = document.querySelector('[data-action="save"], .btn-save, button[type="submit"]');
                if (saveBtn) {
                    saveBtn.click();
                }
            }
            
            // Escape to close modals
            if (e.key === 'Escape') {
                const openModals = document.querySelectorAll('.modal.show');
                openModals.forEach(modal => {
                    const bsModal = bootstrap.Modal.getInstance(modal);
                    if (bsModal) bsModal.hide();
                });
            }
        });
        
        // Session timeout warning
        let sessionTimeoutWarning = null;
        
        function showSessionWarning() {
            if (sessionTimeoutWarning) return;
            
            sessionTimeoutWarning = setTimeout(() => {
                if (confirm('Oturumunuz yakında sona erecek. Devam etmek istiyor musunuz?')) {
                    // Extend session
                    fetch('extend-session.php', { method: 'POST' })
                        .then(() => {
                            showToast('Oturum süresi uzatıldı', 'success');
                            sessionTimeoutWarning = null;
                        });
                } else {
                    window.location.href = 'logout.php';
                }
            }, 3.5 * 60 * 60 * 1000); // 3.5 hours
        }
        
        // Start session timeout warning
        showSessionWarning();
        
        // Reset warning on user activity
        ['mousedown', 'keypress', 'scroll', 'touchstart'].forEach(event => {
            document.addEventListener(event, () => {
                if (sessionTimeoutWarning) {
                    clearTimeout(sessionTimeoutWarning);
                    sessionTimeoutWarning = null;
                    showSessionWarning();
                }
            });
        });
        
        // Window beforeunload warning for unsaved changes
        let hasUnsavedChanges = false;
        
        window.addEventListener('beforeunload', function(e) {
            if (hasUnsavedChanges) {
                e.preventDefault();
                e.returnValue = 'Kaydedilmemiş değişiklikleriniz var. Sayfadan çıkmak istediğinizden emin misiniz?';
            }
        });
        
        // Track form changes
        document.addEventListener('input', function(e) {
            if (e.target.closest('form')) {
                hasUnsavedChanges = true;
            }
        });
        
        // Reset on form submit
        document.addEventListener('submit', function() {
            hasUnsavedChanges = false;
        });
        
        // Console welcome message
        console.log('%c🎰 Casino Yayıncısı - BERAT K Admin Panel', 'color: #6f42c1; font-size: 16px; font-weight: bold;');
        console.log('%cYetkisiz erişim yasaktır!', 'color: #e91e63; font-size: 14px;');
        
        // Development helpers (only in development)
        if (location.hostname === 'localhost' || location.hostname === '127.0.0.1') {
            window.adminDebug = {
                showToast: showToast,
                confirmDelete: confirmDelete,
                ajaxRequest: ajaxRequest,
                validateForm: validateForm
            };
            console.log('%cDevelopment mode - Debug helpers available in window.adminDebug', 'color: #ffc107;');
        }
    </script>
</body>
</html>