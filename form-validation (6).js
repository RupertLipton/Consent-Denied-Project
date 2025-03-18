document.addEventListener('DOMContentLoaded', function() {
    // Force hide all validation messages immediately
    document.querySelectorAll('.validation-message').forEach(msg => {
        msg.style.display = 'none';
    });
    
    // Get form and subscription buttons
    const form = document.getElementById('signup-form');
    const coreSubscribeButton = document.getElementById('core-subscribe-button');
    const enhancedSubscribeButton = document.getElementById('enhanced-subscribe-button');
    
    if (!form || (!coreSubscribeButton && !enhancedSubscribeButton)) {
        console.error('Could not find form or subscription buttons');
        return;
    }
    
    const allRequiredFields = form.querySelectorAll('input[required]');
    
    // Add an explicit entry for the postcode field to ensure it just checks for existence
const validationPatterns = {
    'email': {
        pattern: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/,
    },
    // REMOVED: postcode validation pattern
    'postcode': {
        // Simple pattern that just requires at least one character
        pattern: /.+/,
        message: 'Please enter your postcode'
    },
    'mobile': {
        // Updated pattern to better handle spaces and be more lenient
        pattern: /^(07\d{3}\s\d{6})$|^(\+44\s7\d{3}\s\d{6})$|^[0-9+\s]{10,15}$/,
        message: 'Please enter a valid phone number with spaces (e.g., 07123 456789 or +44 7123 456789)'
    },
    'car-make': {
        pattern: /^[A-Za-z ]{1,15}$/,
    }
};
    
    // Mobile number auto-formatting
    const mobileField = document.getElementById('mobile');
    if (mobileField) {
        mobileField.addEventListener('input', function() {
            const cursorPos = this.selectionStart;
            const oldLength = this.value.length;
            
            // Remove non-digit characters except + and spaces
            let cleaned = this.value.replace(/[^\d+ ]/g, '');
            
            // Format based on number type
            if (cleaned.startsWith('+44')) {
                // Format as +44 7xxx xxxxxx
                cleaned = cleaned.replace(/\s/g, ''); // Remove existing spaces
                if (cleaned.length > 3) {
                    this.value = '+44 ' + cleaned.substring(3).replace(/(\d{4})(\d{0,6})/, '$1 $2').trim();
                } else {
                    this.value = cleaned;
                }
            } else if (cleaned.startsWith('07')) {
                // Format as 07xxx xxxxxx
                cleaned = cleaned.replace(/\s/g, ''); // Remove existing spaces
                this.value = cleaned.replace(/(\d{5})(\d{0,6})/, '$1 $2').trim();
            } else {
                this.value = cleaned;
            }
            
            // Maintain cursor position after formatting
            const newLength = this.value.length;
            const cursorAdjustment = newLength - oldLength;
            this.setSelectionRange(cursorPos + cursorAdjustment, cursorPos + cursorAdjustment);
        });
    }
    
    // Function to show validation message
    function showValidationMessage(field, show, message) {
        const messageElement = document.getElementById(field.id + '-validation-message');
        if (messageElement) {
            if (show) {
                messageElement.textContent = message;
                messageElement.style.display = 'block';
            } else {
                messageElement.style.display = 'none';
            }
        }
    }
    
    // Function to validate a specific field
    function validateField(field) {
        const fieldId = field.id;
        const value = field.value.trim();
        
        // Required field must have a value
        if (!value) {
            return false;
        }
        
        // If we have a specific pattern for this field, test it
        if (validationPatterns[fieldId]) {
            return validationPatterns[fieldId].pattern.test(value);
        }
        
        // Otherwise, just check it has a value
        return true;
    }
    
    // Function to check if all required fields are filled AND valid
    function checkFormCompletion(showMessages = false) {
        let allValid = true;
        
        // Check each required field
        allRequiredFields.forEach(field => {
            if (!validateField(field)) {
                allValid = false;
                field.classList.add('invalid');
                
                // Show validation message if flag is set to true
                if (showMessages && validationPatterns[field.id]) {
                    showValidationMessage(field, true, validationPatterns[field.id].message);
                }
            } else {
                field.classList.remove('invalid');
                if (showMessages) {
                    showValidationMessage(field, false);
                }
            }
        });
        
        // Enable/disable buttons based on form validity
        if (coreSubscribeButton) {
            // Store the original href if not already stored
            if (!coreSubscribeButton.getAttribute('data-original-href')) {
                coreSubscribeButton.setAttribute('data-original-href', coreSubscribeButton.getAttribute('href'));
            }
            
            if (allValid) {
                coreSubscribeButton.setAttribute('href', coreSubscribeButton.getAttribute('data-original-href'));
            } else {
                coreSubscribeButton.setAttribute('href', 'javascript:void(0)');
            }
        }
        
        if (enhancedSubscribeButton) {
            // Store the original href if not already stored
            if (!enhancedSubscribeButton.getAttribute('data-original-href')) {
                enhancedSubscribeButton.setAttribute('data-original-href', enhancedSubscribeButton.getAttribute('href'));
            }
            
            if (allValid) {
                enhancedSubscribeButton.setAttribute('href', enhancedSubscribeButton.getAttribute('data-original-href'));
            } else {
                enhancedSubscribeButton.setAttribute('href', 'javascript:void(0)');
            }
        }
        
        console.log('Form validation check: ' + (allValid ? 'Valid' : 'Invalid'));
        return allValid;
    }
    
    // Add input event listener to all required fields
    allRequiredFields.forEach(field => {
        field.addEventListener('input', function() {
            // Hide validation message when typing
            showValidationMessage(this, false);
            checkFormCompletion(true); // Show messages during user input
        });
        
        field.addEventListener('blur', function() {
            if (!validateField(this)) {
                this.style.border = '2px solid #ff0000';
                
                // Show validation message if available
                if (validationPatterns[this.id]) {
                    showValidationMessage(this, true, validationPatterns[this.id].message);
                }
            } else {
                this.style.border = '1px solid #ccc';
                showValidationMessage(this, false);
            }
        });
    });
    
    // Handle click on subscription buttons
    function handleSubscriptionClick(e) {
        if (!checkFormCompletion(true)) { // Show messages when validating on click
            e.preventDefault();
            
            // Find the first invalid field
            let firstInvalid = null;
            let invalidFields = [];
            
            allRequiredFields.forEach(field => {
                if (!validateField(field)) {
                    if (validationPatterns[field.id]) {
                        invalidFields.push(field.previousElementSibling.textContent.replace(':', '') + 
                                         ": " + validationPatterns[field.id].message);
                    } else {
                        invalidFields.push(field.previousElementSibling.textContent.replace(':', ''));
                    }
                    
                    field.style.border = '2px solid #ff0000';
                    
                    if (!firstInvalid) {
                        firstInvalid = field;
                    }
                    
                    // Show validation message if available
                    if (validationPatterns[field.id]) {
                        showValidationMessage(field, true, validationPatterns[field.id].message);
                    }
                }
            });
            
            // Focus the first invalid field
            if (firstInvalid) {
                firstInvalid.focus();
            }
            
            // Show an alert about invalid fields
            if (invalidFields.length > 0) {
                alert('Please correct these fields before subscribing:\n\n' + invalidFields.join('\n'));
            } else {
                alert('Please complete all required fields before subscribing.');
            }
            
            // Scroll to the form
            form.scrollIntoView({ behavior: 'smooth', block: 'start' });
            return false;
        }
        
        // If form is complete and valid, store the form data in session storage
        const formData = {};
        
        // Get all form fields (including non-required ones)
        const allFields = form.querySelectorAll('input');
        allFields.forEach(field => {
            formData[field.id] = field.value;
        });
        
        // Store form data in session storage
        sessionStorage.setItem('cd_registration_data', JSON.stringify(formData));
        
        console.log('Form data stored successfully. Proceeding to checkout.');
        
        // Allow the link to proceed
        return true;
    }
    
    // Attach event listeners to subscription buttons
    if (coreSubscribeButton) {
        coreSubscribeButton.addEventListener('click', handleSubscriptionClick);
    }
    
    if (enhancedSubscribeButton) {
        enhancedSubscribeButton.addEventListener('click', handleSubscriptionClick);
    }
    
    // Initial check to disable buttons if form is incomplete
    // IMPORTANT: Pass false to avoid showing validation messages
    checkFormCompletion(false);
    
    // Clear any validation styling when user focuses on a field
    const allFields = form.querySelectorAll('input');
    allFields.forEach(field => {
        field.addEventListener('focus', function() {
            this.style.border = '1px solid #ccc';
            this.style.boxShadow = 'none';
        });
    });
    
    // Prevent form submission (as we're using the buttons to navigate to checkout)
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        return false;
    });
});