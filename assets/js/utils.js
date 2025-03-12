export function formatNumber(num) {
  console.log("hello la fonction qui formatte le nombre!");

  const fixedNumber = num.toFixed(2);
  const [integerPart, decimalPart] = fixedNumber.split(".");
  const formattedInteger = new Intl.NumberFormat("fr-FR").format(integerPart);

  return `${formattedInteger},${decimalPart}`;
}

/**
 * Formats the value of an input field to ensure it contains only numbers and a single decimal point.
 * Additionally, it limits the number of digits after the decimal point to 2.
 * @param {HTMLInputElement} input - The input element to format.
 */
export function formatInputValue(input) {
  input.value = input.value
    .replace(/[^\d.]/g, "") // Keep only digits and the decimal point
    .replace(/(\..*)\./g, "$1") // Ensure only one decimal point exists
    .replace(/(\.[\d]{2})./g, "$1"); // Limit to 2 digits after the decimal point
}
