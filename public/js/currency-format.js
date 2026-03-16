/**
 * MyPawnShop - Global Currency Formatter
 * Auto-formats inputs with class .currency-input to display "Rp 10.000"
 * Raw numeric value stored in sibling hidden input with same name
 */
document.addEventListener('DOMContentLoaded', function() {
    initCurrencyInputs();
});

function initCurrencyInputs() {
    document.querySelectorAll('.currency-input').forEach(function(input) {
        // Create hidden input for raw value
        const hiddenName = input.getAttribute('name');
        if (!hiddenName) return;

        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = hiddenName;
        hidden.value = input.value || '0';
        input.parentNode.insertBefore(hidden, input.nextSibling);

        // Remove name from display input
        input.removeAttribute('name');
        input.setAttribute('data-currency-for', hiddenName);

        // Format initial value
        if (input.value) {
            hidden.value = parseNumber(input.value);
            input.value = formatCurrency(hidden.value);
        }

        // Format on input
        input.addEventListener('input', function() {
            const raw = parseNumber(this.value);
            hidden.value = raw;
            
            const cursorPos = this.selectionStart;
            const oldLen = this.value.length;
            this.value = formatCurrency(raw);
            const newLen = this.value.length;
            
            // Adjust cursor position
            const diff = newLen - oldLen;
            this.setSelectionRange(cursorPos + diff, cursorPos + diff);
        });

        input.addEventListener('focus', function() {
            // Select all on focus for easy replacement
        });
    });
}

function formatCurrency(num) {
    if (!num || num == 0) return 'Rp 0';
    return 'Rp ' + Number(num).toLocaleString('id-ID');
}

function parseNumber(str) {
    if (!str) return 0;
    // Remove "Rp", spaces, dots, and any non-numeric chars except minus
    return parseInt(String(str).replace(/[^0-9-]/g, '')) || 0;
}

// Helper to get raw value from a currency input
function getCurrencyValue(inputElement) {
    const name = inputElement.getAttribute('data-currency-for');
    if (name) {
        const hidden = inputElement.parentNode.querySelector('input[name="' + name + '"]');
        return hidden ? parseFloat(hidden.value) : 0;
    }
    return parseNumber(inputElement.value);
}
