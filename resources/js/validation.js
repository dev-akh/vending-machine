class VendingMachineValidation {
    constructor() {
        this.init();
    }

    init() {
        this.setupProductFormValidation();
        this.setupPurchaseFormValidation();
        this.setupRealTimeValidation();
    }

    setupProductFormValidation() {
        const productForm = document.querySelector('#productForm');
        if (!productForm) return;

        productForm.addEventListener('submit', (e) => {
            if (!this.validateProductForm()) {
                e.preventDefault();
            }
        });
    }

    setupPurchaseFormValidation() {
        const purchaseForm = document.querySelector('#purchaseForm');
        if (!purchaseForm) return;

        purchaseForm.addEventListener('submit', (e) => {
            if (!this.validatePurchaseForm()) {
                e.preventDefault();
            }
        });
    }

    setupRealTimeValidation() {
        // Product name validation
        const nameInput = document.querySelector('#name');
        if (nameInput) {
            nameInput.addEventListener('input', () => {
                this.validateField(nameInput, this.validateProductName);
            });
        }

        // Price validation
        const priceInput = document.querySelector('#price');
        if (priceInput) {
            priceInput.addEventListener('input', () => {
                this.validateField(priceInput, this.validatePrice);
            });
        }

        // Quantity validation
        const quantityInput = document.querySelector('#quantity_available');
        if (quantityInput) {
            quantityInput.addEventListener('input', () => {
                this.validateField(quantityInput, this.validateQuantity);
            });
        }

        // Purchase quantity validation
        const purchaseQuantityInput = document.querySelector('#quantity');
        if (purchaseQuantityInput) {
            purchaseQuantityInput.addEventListener('input', () => {
                this.validateField(purchaseQuantityInput, this.validatePurchaseQuantity);
            });
        }

        // Amount paid validation
        const amountPaidInput = document.querySelector('#amount_paid');
        if (amountPaidInput) {
            amountPaidInput.addEventListener('input', () => {
                this.validateField(amountPaidInput, this.validateAmountPaid);
            });
        }
    }

    validateField(input, validator) {
        const result = validator.call(this, input.value);
        this.showValidationFeedback(input, result);
        return result.isValid;
    }

    validateProductName(value) {
        if (!value || value.trim() === '') {
            return { isValid: false, message: 'Product name is required.' };
        }
        if (value.length > 255) {
            return { isValid: false, message: 'Product name cannot exceed 255 characters.' };
        }
        return { isValid: true, message: '' };
    }

    validatePrice(value) {
        if (!value || value.trim() === '') {
            return { isValid: false, message: 'Price is required.' };
        }
        
        const price = parseFloat(value);
        if (isNaN(price)) {
            return { isValid: false, message: 'Price must be a valid number.' };
        }
        if (price <= 0) {
            return { isValid: false, message: 'Price must be greater than 0.' };
        }
        if (price > 99999.999) {
            return { isValid: false, message: 'Price cannot exceed 99999.999.' };
        }
        
        const priceRegex = /^\d{1,5}(\.\d{1,3})?$/;
        if (!priceRegex.test(value)) {
            return { isValid: false, message: 'Price must have up to 3 decimal places.' };
        }
        
        return { isValid: true, message: '' };
    }

    validateQuantity(value) {
        if (!value || value.trim() === '') {
            return { isValid: false, message: 'Quantity is required.' };
        }
        
        const quantity = parseInt(value);
        if (isNaN(quantity)) {
            return { isValid: false, message: 'Quantity must be a valid number.' };
        }
        if (quantity < 0) {
            return { isValid: false, message: 'Quantity cannot be negative.' };
        }
        if (quantity > 999999) {
            return { isValid: false, message: 'Quantity cannot exceed 999,999.' };
        }
        
        return { isValid: true, message: '' };
    }

    validatePurchaseQuantity(value) {
        if (!value || value.trim() === '') {
            return { isValid: false, message: 'Quantity is required.' };
        }
        
        const quantity = parseInt(value);
        if (isNaN(quantity)) {
            return { isValid: false, message: 'Quantity must be a valid number.' };
        }
        if (quantity < 1) {
            return { isValid: false, message: 'Quantity must be at least 1.' };
        }
        
        const maxQuantity = parseInt(document.querySelector('#quantity')?.max || 0);
        if (quantity > maxQuantity) {
            return { isValid: false, message: `Cannot purchase more than ${maxQuantity} units.` };
        }
        
        return { isValid: true, message: '' };
    }

    validateAmountPaid(value) {
        if (!value || value.trim() === '') {
            return { isValid: false, message: 'Amount paid is required.' };
        }
        
        const amount = parseFloat(value);
        if (isNaN(amount)) {
            return { isValid: false, message: 'Amount paid must be a valid number.' };
        }
        if (amount <= 0) {
            return { isValid: false, message: 'Amount paid must be greater than 0.' };
        }
        
        const amountRegex = /^\d{1,5}(\.\d{1,3})?$/;
        if (!amountRegex.test(value)) {
            return { isValid: false, message: 'Amount must have up to 3 decimal places.' };
        }
        
        return { isValid: true, message: '' };
    }

    validateProductForm() {
        const nameInput = document.querySelector('#name');
        const priceInput = document.querySelector('#price');
        const quantityInput = document.querySelector('#quantity_available');

        let isValid = true;

        if (nameInput && !this.validateField(nameInput, this.validateProductName)) {
            isValid = false;
        }
        if (priceInput && !this.validateField(priceInput, this.validatePrice)) {
            isValid = false;
        }
        if (quantityInput && !this.validateField(quantityInput, this.validateQuantity)) {
            isValid = false;
        }

        return isValid;
    }

    validatePurchaseForm() {
        const quantityInput = document.querySelector('#quantity');
        const amountPaidInput = document.querySelector('#amount_paid');

        let isValid = true;

        if (quantityInput && !this.validateField(quantityInput, this.validatePurchaseQuantity)) {
            isValid = false;
        }
        if (amountPaidInput && !this.validateField(amountPaidInput, this.validateAmountPaid)) {
            isValid = false;
        }

        // Check if amount paid is sufficient
        if (isValid) {
            const quantity = parseInt(quantityInput.value);
            const amountPaid = parseFloat(amountPaidInput.value);
            const unitPrice = parseFloat(document.querySelector('#unitPrice')?.value || 0);
            const totalPrice = unitPrice * quantity;

            if (amountPaid < totalPrice) {
                this.showValidationFeedback(amountPaidInput, {
                    isValid: false,
                    message: `Amount paid is insufficient. Total price: $${totalPrice.toFixed(3)}`
                });
                isValid = false;
            }
        }

        return isValid;
    }

    showValidationFeedback(input, result) {
        // Remove existing feedback
        const existingFeedback = input.parentNode.querySelector('.validation-feedback');
        if (existingFeedback) {
            existingFeedback.remove();
        }

        // Remove existing classes
        input.classList.remove('border-green-500', 'border-red-500');

        if (result.isValid) {
            input.classList.add('border-green-500');
        } else {
            input.classList.add('border-red-500');
            
            const feedback = document.createElement('div');
            feedback.className = 'validation-feedback text-red-500 text-sm mt-1';
            feedback.textContent = result.message;
            input.parentNode.appendChild(feedback);
        }
    }
}

// Initialize validation when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new VendingMachineValidation();
});

// Export for use in other files
window.VendingMachineValidation = VendingMachineValidation;
