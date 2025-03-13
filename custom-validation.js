document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('signup-form');
    if (form) {
        const subscribeButtons = document.querySelectorAll('.subscribe-button');

        function checkFormCompletion() {
            const formFields = form.querySelectorAll('input[required]');
            let allFilled = true;

            formFields.forEach(field => {
                if (!field.value.trim()) {
                    allFilled = false;
                    console.log(`Field ${field.id} is empty.`);
                } else {
                    console.log(`Field ${field.id} is filled.`);
                }
            });

            subscribeButtons.forEach(button => {
                button.disabled = !allFilled;
                console.log(`Button ${button.id} is ${button.disabled ? 'disabled' : 'enabled'}`);
            });
        }

        form.addEventListener('input', checkFormCompletion);
        checkFormCompletion(); // Initial check to disable buttons if form is incomplete

        // Form submission (just prevent default for now)
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Thank you for registering! In a real implementation, this would process your registration.');
        });

        // Prevent default action if form is incomplete
        const subscriptionOptions = document.querySelectorAll('.subscription-option');
        subscriptionOptions.forEach(option => {
            option.addEventListener('click', function(e) {
                const button = this.querySelector('.subscribe-button');
                if (button && button.disabled) {
                    e.preventDefault();
                    alert('Please complete the form before subscribing.');
                    console.log('Attempted to subscribe without completing the form.');
                } else {
                    console.log('Subscription option clicked');
                    console.log('Product ID:', this.getAttribute('data-product-id'));
                }
            });
        });
    }
});