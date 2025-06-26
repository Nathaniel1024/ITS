    document.addEventListener('DOMContentLoaded', function() {
      // Enhanced filter form functionality
      document.getElementById('filterForm').addEventListener('change', async function (e) {
        e.preventDefault();

        const formData = new FormData(this);
        formData.append('ajax', 'fetch_data');
        const params = new URLSearchParams(formData).toString();

        // Show loading state
        document.getElementById('table-container').innerHTML = 
          '<div class="loading"><div class="loading-text">Loading data...</div></div>';

        try {
          const response = await fetch('?' + params);
          const html = await response.text();
          document.getElementById('table-container').innerHTML = html;
        } catch (error) {
          console.error('Error fetching data:', error);
          document.getElementById('table-container').innerHTML = 
            '<div class="no-data">Error loading data. Please try again.</div>';
        }
      });

      // Form submission handlers with loading states
      const forms = document.querySelectorAll('form');
      forms.forEach(form => {
        form.addEventListener('submit', function(e) {
          // Skip AJAX handling for filter form
          if (this.id === 'filterForm' && e.target.type !== 'submit') {
            return;
          }

          const button = this.querySelector('button[type="submit"]');
          const originalText = button.textContent;
          
          if (this.id !== 'filterForm') {
            button.textContent = 'Processing...';
            button.disabled = true;
            
            // Re-enable after delay
            setTimeout(() => {
              button.textContent = originalText;
              button.disabled = false;
            }, 3000);
          }
        });
      });

      // Enhanced form validation and UX
      const trackingInput = document.getElementById('tracking_id');
      if (trackingInput) {
        trackingInput.addEventListener('input', function() {
          this.value = this.value.toUpperCase();
        });
      }

      // Auto-load data if filters are set
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.get('filterType') || urlParams.get('column') || urlParams.get('keyword')) {
        document.getElementById('filterForm').dispatchEvent(new Event('change'));
      }

      // Keyboard shortcuts
      document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + F to focus search
        if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
          e.preventDefault();
          document.getElementById('keyword').focus();
        }
      });
    });